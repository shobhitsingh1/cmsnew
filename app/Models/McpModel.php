<?php

namespace App\Models;

use CodeIgniter\Model;

class McpModel extends Model
{
    protected $table      = 'tbl_devotional';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'lang', 'title', 'subtitle', 'text', 'series_id',
        'acknowledgements', 'tag_ids', 'created_on',
        'date_quarter', 'date_year', 'active',
        'book_ids', 'author_ids', 'devotional_date', 'user_id',
    ];

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // =========================================================
    // LIST / GET / SEARCH / UPDATE  (called by controller tools)
    // =========================================================

    /**
     * List devotionals with pagination.
     */
    public function listContent(int $limit = 10, int $offset = 0): array
    {
        return $this->db->table('tbl_devotional')
            ->select('id, title, subtitle, devotional_date, series_id, lang, active, created_on')
            ->orderBy('devotional_date', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    /**
     * Get a single devotional by ID (joins tag titles).
     */
    public function getContentById(int $id): ?array
    {
        $row = $this->db->table('tbl_devotional')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        // Resolve tag / book / author titles
        $row['tag_titles']    = $this->resolveTagTitles($row['tag_ids']    ?? '');
        $row['book_titles']   = $this->resolveTagTitles($row['book_ids']   ?? '');
        $row['author_titles'] = $this->resolveTagTitles($row['author_ids'] ?? '');

        return $row;
    }

    /**
     * Full-text search across title, subtitle, text.
     */
    public function searchContent(string $query, int $limit = 10): array
    {
        $like = '%' . $this->db->escapeLikeString($query) . '%';

        return $this->db->table('tbl_devotional')
            ->select('id, title, subtitle, devotional_date, series_id, lang')
            ->groupStart()
                ->like('title',    $query)
                ->orLike('subtitle', $query)
                ->orLike('text',     $query)
            ->groupEnd()
            ->where('active', 1)
            ->orderBy('devotional_date', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Update arbitrary fields on a devotional row.
     */
    public function updateContent(int $id, array $data): bool
    {
        return $this->db->table('tbl_devotional')
            ->where('id', $id)
            ->update($data);
    }

    public function listtags(array $input): array
    {
        $limit = min((int) ($input['limit'] ?? 10), 100);
        $offset = (int) ($input['offset'] ?? 0);
        
        return $this->db->table('tbl_tags')
            ->select('id, title, type')
            ->orderBy('type')
            ->orderBy('title')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();
    }

    public function toolParseDevotionalText(array $input): array
    {
        if (empty($input['text'])) {
            return ['error' => 'text is required'];
        }

        $raw        = $input['text'];
        $lang       = $input['lang']        ?? 'en';
        $year       = (int)($input['date_year'] ?? date('Y'));
        $ack        = $input['acknowledgements'] ?? null;
        $tag_ids    = $input['tag_ids']          ?? null;
        $book_ids   = $input['book_ids']         ?? null;
        $author_ids = $input['author_ids']       ?? null;
        $user_id    = (int)($input['user_id']    ?? 0);
        $filename   = $input['filename']         ?? '';

        $quarterGroup = $this->detectQuarterGroup($filename);

        $quarterOverride = (isset($input['date_quarter']) && $input['date_quarter'] !== '')
            ? $input['date_quarter']
            : null;

        $refPerDate  = [];
        $refResolved = ['tag_ids' => null, 'book_ids' => null, 'author_ids' => null];

        if (!empty($input['references_text'])) {
            $resolved    = $this->resolveTagsFromReferences($input['references_text']);
            $refPerDate  = $resolved['per_date'] ?? [];
            if ($resolved['tag_ids']    && !$tag_ids)    { $tag_ids    = $resolved['tag_ids'];    }
            if ($resolved['book_ids']   && !$book_ids)   { $book_ids   = $resolved['book_ids'];   }
            if ($resolved['author_ids'] && !$author_ids) { $author_ids = $resolved['author_ids']; }
        }

        $series_processing = (int)($input['series_processing'] ?? 0);
        $start_processing  = (int)($input['start_processing']  ?? 0);

        if (strpos($raw, "\r\n") !== false) {
            $entries   = explode("\r\n\r\n", $raw);
            $lineBreak = "\r\n";
        } else {
            $entries   = explode("\n\n", $raw);
            $lineBreak = "\n";
        }

        $maxRow          = $this->db->table('tbl_devotional')
            ->selectMax('series_id', 'max_series')
            ->get()->getRow();
        $nextSeriesId    = (int)($maxRow->max_series ?? 0);
        $currentSeriesId = 0;

        $devotional_rows = [];
        $tmp_rows        = [];
        $errors          = [];

        foreach ($entries as $idx => $entry) {

            if (trim($entry) === '') {
                continue;
            }

            $lines = array_values(array_filter(
                explode($lineBreak, $entry),
                fn($l) => trim($l) !== ''
            ));

            if (count($lines) < 3) {
                $errors[] = "Entry " . ($idx + 1) . ": not enough lines (need date, title, subtitle)";
                continue;
            }

            $dateLine = trim($lines[0]);
            $parsed   = $this->parseDevotionalDateLine($dateLine, $year);

            if ($parsed === null) {
                $errors[] = "Entry " . ($idx + 1) . ": cannot parse date line '{$dateLine}'";
                continue;
            }

            [$devotionalDate, $monthNum, $entryYear] = $parsed;

            $quarter = $quarterOverride
                ?? $this->computeQuarter($monthNum, $entryYear, $quarterGroup);

            $titleRaw = trim($lines[1]);

            if (preg_match('/\((\d+)\)\s*$/', $titleRaw, $m)) {
                $seriesNum = (int)$m[1];
                if ($seriesNum === 1) {
                    $nextSeriesId++;
                    $currentSeriesId = $nextSeriesId;
                }
                $series_id = $currentSeriesId;
            } else {
                $currentSeriesId = 0;
                $series_id       = 0;
            }

            $title    = $this->cleanDevotionalField($lines[1]);
            $subtitle = $this->cleanDevotionalField($lines[2]);

            $bodyParts = [];
            for ($i = 3; $i < count($lines); $i++) {
                $bodyParts[] = $lines[$i];
            }
            $body = $this->cleanDevotionalBody(implode("\n", $bodyParts));
            $dateKey      = substr($devotionalDate, 5);  // "MM-DD"
            $perDateEntry = $refPerDate[$dateKey] ?? null;

            $rowBookIds   = isset($input['book_ids'])   ? $book_ids
                          : ($perDateEntry['book_ids']   ?? $book_ids);
            $rowAuthorIds = isset($input['author_ids']) ? $author_ids
                          : ($perDateEntry['author_ids'] ?? $author_ids);
            $rowTagIds    = isset($input['tag_ids'])    ? $tag_ids
                          : ($perDateEntry['tag_ids']    ?? $tag_ids);

            $row = [
                'lang'             => $lang,
                'title'            => $title        ?: null,
                'subtitle'         => $subtitle     ?: null,
                'text'             => $body         ?: null,
                'series_id'        => $series_id,
                'acknowledgements' => $ack          ?: null,
                'tag_ids'          => $rowTagIds    ?: null,
                'book_ids'         => $rowBookIds   ?: null,
                'author_ids'       => $rowAuthorIds ?: null,
                'devotional_date'  => $devotionalDate,
                'date_year'        => $entryYear,
                'date_quarter'     => $quarter,
                'user_id'          => $user_id,
                'created_on'       => date('Y-m-d H:i:s'),
                'active'           => 1,
            ];

            $devotional_rows[] = $row;

            if ($series_id > 0) {
                $tmp_rows[] = $row;
            }
        }

        $flush_tmp = ($series_processing > 0 && $start_processing === $series_processing);

        return [
            'devotional_rows'   => $devotional_rows,
            'tmp_rows'          => $tmp_rows,
            'total_devotional'  => count($devotional_rows),
            'total_tmp'         => count($tmp_rows),
            'series_processing' => $series_processing,
            'start_processing'  => $start_processing,
            'flush_tmp'         => $flush_tmp,
            'errors'            => $errors,
        ];
    }

    public function toolImportDevotionalRows(array $input): array
    {
        if (empty($input['rows'])) {
            return ['error' => 'rows required'];
        }

        $rows              = $input['rows'];
        $series_processing = (int)($input['series_processing'] ?? 0);
        $start_processing  = (int)($input['start_processing']  ?? 0);
        $flush_tmp         = (bool)($input['flush_tmp']        ?? false);

        $inserted     = 0;
        $updated      = 0;
        $tmp_inserted = 0;
        $tmp_updated  = 0;
        $flushed      = 0;

        foreach ($rows as $row) {
            $row        = (array)$row;   // handle objects passed from JSON
            $date       = $row['devotional_date'] ?? null;
            $series_id  = (int)($row['series_id'] ?? 0);
            $builtRow   = $this->buildRow($row);

            if ($series_id === 0) {
                $this->upsertByDate('tbl_devotional', $date, $builtRow, $inserted, $updated);
            } else {
                $this->upsertByDate('tbl_devotional_tmp', $date, $builtRow, $tmp_inserted, $tmp_updated);
            }
        }
        if ($flush_tmp && $series_processing > 0) {

            $seriesIds = array_unique(array_filter(
                array_map(fn($r) => (int)(is_array($r) ? ($r['series_id'] ?? 0) : ($r->series_id ?? 0)), $rows),
                fn($sid) => $sid > 0
            ));

            foreach ($seriesIds as $sid) {
                $tmpRows = $this->db->table('tbl_devotional_tmp')
                    ->where('series_id', $sid)
                    ->get()
                    ->getResultArray();

                foreach ($tmpRows as $tmpRow) {
                    $d = $tmpRow['devotional_date'];
                    unset($tmpRow['id']);   

                    $this->upsertByDate('tbl_devotional', $d, $tmpRow, $inserted, $updated);
                    $flushed++;
                }

                $this->db->table('tbl_devotional_tmp')
                    ->where('series_id', $sid)
                    ->delete();
            }
        }

        return [
            'inserted'     => $inserted,
            'updated'      => $updated,
            'tmp_inserted' => $tmp_inserted,
            'tmp_updated'  => $tmp_updated,
            'flushed'      => $flushed,
        ];
    }


    private function upsertByDate(
        string $table,
        ?string $date,
        array $row,
        int &$inserted,
        int &$updated
    ): void {
        if (empty($date)) {
            return;
        }

        $exists = $this->db->table($table)
            ->where('devotional_date', $date)
            ->countAllResults();

        if ($exists) {
            $updateRow = $row;
            unset($updateRow['devotional_date']);  

            $this->db->table($table)
                ->where('devotional_date', $date)
                ->update($updateRow);

            $updated++;
        } else {
            $this->db->table($table)->insert($row);
            $inserted++;
        }
    }

    private function buildRow(array $row): array
    {
        return [
            'lang'             => $row['lang']             ?? 'en',
            'title'            => $row['title']            ?? null,
            'subtitle'         => $row['subtitle']         ?? null,
            'text'             => $row['text']             ?? null,
            'series_id'        => (int)($row['series_id'] ?? 0),
            'acknowledgements' => $row['acknowledgements'] ?? null,
            'tag_ids'          => $row['tag_ids']          ?? null,
            'book_ids'         => $row['book_ids']         ?? null,
            'author_ids'       => $row['author_ids']       ?? null,
            'devotional_date'  => $row['devotional_date']  ?? null,
            'date_year'        => $row['date_year']        ?? date('Y'),
            'date_quarter'     => $row['date_quarter']     ?? null,
            'user_id'          => (int)($row['user_id']   ?? 0),
            'created_on'       => $row['created_on']       ?? date('Y-m-d H:i:s'),
            'active'           => (int)($row['active']    ?? 1),
        ];
    }

    private function resolveTagTitles(string $ids): string
    {
        if (empty(trim($ids))) {
            return '';
        }

        $idArr = array_filter(array_map('trim', explode(',', $ids)), 'is_numeric');

        if (empty($idArr)) {
            return '';
        }

        $result = $this->db->table('tbl_tags')
            ->select('GROUP_CONCAT(title ORDER BY id SEPARATOR ", ") AS titles')
            ->whereIn('id', $idArr)
            ->get()
            ->getRowArray();

        return $result['titles'] ?? '';
    }

    private function cleanDevotionalField(string $str): string
    {
        return trim($str);
    }

    private function cleanDevotionalBody(string $str): string
    {
        return trim($str);
    }

   
    private function parseDevotionalDateLine(string $dateLine, int $baseYear): ?array
    {
        // ── Spanish month map ──────────────────────────────────────────────
        static $spanishMonths = [
            'enero'      => ['January',  1],
            'febrero'    => ['February', 2],
            'marzo'      => ['March',    3],
            'abril'      => ['April',    4],
            'mayo'       => ['May',      5],
            'junio'      => ['June',     6],
            'julio'      => ['July',     7],
            'agosto'     => ['August',   8],
            'septiembre' => ['September',9],
            'octubre'    => ['October',  10],
            'noviembre'  => ['November', 11],
            'diciembre'  => ['December', 12],
        ];

        $line = trim($dateLine);

        if (preg_match(
            '/^[a-záéíóúüñ]+,?\s+(\d{1,2})\s+de\s+([a-záéíóúüñ]+)(?:\s+(?:de\s+)?(\d{4}))?/iu',
            $line,
            $m
        )) {
            $day      = (int)$m[1];
            $monthKey = mb_strtolower($m[2]);
            $yearInLine = isset($m[3]) && $m[3] ? (int)$m[3] : null;

            if (!isset($spanishMonths[$monthKey])) {
                return null;
            }

            [$englishMonth, $monthNum] = $spanishMonths[$monthKey];
            $entryYear = $yearInLine ?? $baseYear;
            $ts        = mktime(0, 0, 0, $monthNum, $day, $entryYear);

            if ($ts === false) {
                return null;
            }

            return [date('Y-m-d', $ts), $monthNum, $entryYear];
        }

        if (preg_match('/^[A-Za-z]+\s+([A-Za-z]+)\s+(\d{1,2})(?:\s+(\d{4}))?/', $line, $m)) {
            $monthName  = $m[1];
            $day        = (int)$m[2];
            $yearInLine = isset($m[3]) && $m[3] ? (int)$m[3] : null;
            $entryYear  = $yearInLine ?? $baseYear;
            $ts         = strtotime("{$day} {$monthName} {$entryYear}");

            if ($ts === false) {
                return null;
            }

            $monthNum = (int)date('n', $ts);
            return [date('Y-m-d', $ts), $monthNum, $entryYear];
        }

        return null;
    }

    private function detectQuarterGroup(string $filename): string
    {
        if (preg_match('/\b(DJF|JFM|MAM|AMJ|JJA|JAS|SON|OND)\b/i', $filename, $m)) {
            return strtoupper($m[1]);
        }
        return '';
    }

    private function computeQuarter(int $month, int $year): string
    {
        // DJF handling (cross-year quarter)
        if ($month == 12) {
            $mm = '12'; // December stays same year
            $yy = substr((string)$year, -2);
        } elseif ($month <= 2) {
            $mm = '03'; // Jan & Feb belong to Q ending in March
            $yy = substr((string)$year, -2);
        }
        // Other quarters (normal)
        elseif ($month <= 5) {
            $mm = '06'; // MAM/AMJ type
            $yy = substr((string)$year, -2);
        } elseif ($month <= 8) {
            $mm = '09'; // JJA/JAS
            $yy = substr((string)$year, -2);
        } else {
            $mm = '12'; // SON/OND
            $yy = substr((string)$year, -2);
        }

        return $mm . $yy;
    }

    private function resolveTagsFromReferences(string $referencesText): array
    {
        $knownSurnames = [
        ];

        $esMonths = [
            'ene' => 1,  'feb' => 2,  'mar' => 3,  'abr' => 4,
            'may' => 5,  'jun' => 6,  'jul' => 7,  'ago' => 8,
            'sep' => 9,  'oct' => 10, 'nov' => 11, 'dic' => 12,
        ];

        $isJunk = function (string $s): bool {
            $s = trim($s);
            if (strlen($s) < 2) {
                return true;
            }
            if (ctype_digit($s)) {
                return true;
            }
            if (preg_match('/^[\s.,;:]+$/', $s)) {
                return true;
            }
            if (preg_match('/\bpp\.\b|\bvol\b|\bpág\b/i', $s)) {
                return true;
            }
            if (preg_match('/\b(?:MI|TN|OR|CO|PA|NY|FL|CA|IL|OK|GA)\b/', $s)) {
                return true;
            }
            return false;
        };

        $extractItalicBooks = function (string $line) use ($isJunk): array {

            $n = preg_replace('/\*\s+/', '*', $line);
            $n = preg_replace('/\s+\*/', '*', $n);

            $found = [];

            if (preg_match_all('/\*([^*]{2,80}?)\*/', $n, $m)) {
                foreach ($m[1] as $i => $span) {
                    $title = rtrim(trim($span), '.,;:');
                    if ($isJunk($title)) {
                        continue;
                    }

                    $matchEnd = strpos($n, '*' . $m[1][$i] . '*') + strlen('*' . $m[1][$i] . '*');
                    $after    = substr($n, $matchEnd);
                    if (preg_match('/^([A-ZÁÉÍÓÚ][a-záéíóúüñ]+)(?=[,\s(]|$)/', $after, $am)) {
                        $appendWord = $am[1];
                        if (!preg_match('/^(?:pp|p|vol|ed|TN|CO|MI|OR|PA|NY|FL|CA|IL)\b/i', $appendWord)) {
                            $title .= ' ' . $appendWord;
                        }
                    }

                    $found[] = $title;
                }
            }

            return $found;
        };

        $extractAuthor = function (string $rest) use ($knownSurnames): ?string {
            $pre = preg_split('/[*«]/', $rest)[0];
            $pre = trim($pre, " \t,;:");

            if (empty($pre)) {
                return null;
            }

            $words = preg_split('/\s+/', $pre);
            if (empty($words)) {
                return null;
            }

            $lastKey = strtolower(rtrim(end($words), '.,;'));
            if (isset($knownSurnames[$lastKey])) {
                return $knownSurnames[$lastKey];
            }

            $namePart = explode(',', $pre)[0];
            $namePart = trim($namePart);
            $nameParts = preg_split('/\s+/', $namePart);
            $nameLastKey = strtolower(rtrim(end($nameParts), '.,;'));
            if (isset($knownSurnames[$nameLastKey])) {
                return $knownSurnames[$nameLastKey];
            }

            if (count($nameParts) >= 2 && !preg_match('/\d|[«»()]/', $namePart)) {
                return $namePart;
            }

            return null;
        };

        // ══════════════════════════════════════════════════════════════════
        // MAIN PARSING LOOP
        // ══════════════════════════════════════════════════════════════════

        $allBooks   = [];  // title string → true (dedup)
        $allAuthors = [];  // full name   → true (dedup)
        $perDate    = [];  // 'MM-DD' => ['books' => [...], 'authors' => [...]]

        foreach (preg_split('/\r?\n/', $referencesText) as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Match "D mes:" or "DD mes:" prefix
            if (!preg_match('/^(\d{1,2})\s+([a-záéíóúüñ]+)\s*:/iu', $line, $dm)) {
                continue;
            }

            $day      = (int)$dm[1];
            $monthNum = $esMonths[strtolower($dm[2])] ?? null;
            $rest     = ltrim(substr($line, strlen($dm[0])));

            // Extract italic books
            $lineBooks  = $extractItalicBooks($rest);

            // Extract author
            $lineAuthor = $extractAuthor($rest);

            // Accumulate globals
            foreach ($lineBooks as $b) {
                $allBooks[mb_strtolower($b)] = $b;
            }
            if ($lineAuthor) {
                $allAuthors[mb_strtolower($lineAuthor)] = $lineAuthor;
            }

            // Store per-date
            if ($monthNum !== null) {
                $dateKey           = sprintf('%02d-%02d', $monthNum, $day);
                $perDate[$dateKey] = [
                    'books'   => $lineBooks,
                    'authors' => $lineAuthor ? [$lineAuthor] : [],
                ];
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // MATCH AGAINST tbl_tags
        // ══════════════════════════════════════════════════════════════════

        // Load all tags once
        $allTags = $this->db->table('tbl_tags')
            ->select('id, title, type')
            ->get()
            ->getResultArray();

        // Build lowercase index: title_lc → tag row
        $tagIndex = [];
        foreach ($allTags as $tag) {
            $tagIndex[mb_strtolower(trim($tag['title']))] = $tag;
        }

        // ── Match helper ──────────────────────────────────────────────────
        $matchTag = function (string $candidate) use ($tagIndex): ?array {
            $key = mb_strtolower($candidate);

            // 1. Exact match
            if (isset($tagIndex[$key])) {
                return $tagIndex[$key];
            }

            // 2. Substring match (candidate ⊃ tag OR tag ⊃ candidate)
            //    Require at least 5 chars in the shorter side to avoid
            //    spurious single-word matches.
            foreach ($tagIndex as $tagKey => $tag) {
                if (strlen($tagKey) < 5 && strlen($key) < 5) {
                    continue;
                }
                if (mb_strpos($key, $tagKey) !== false || mb_strpos($tagKey, $key) !== false) {
                    return $tag;
                }
            }

            return null;
        };

        // Match all unique books
        $bookIds   = [];
        $authorIds = [];
        $tagIds    = [];

        foreach ($allBooks as $bookTitle) {
            $matched = $matchTag($bookTitle);
            if (!$matched) {
                continue;
            }
            $type = strtolower($matched['type'] ?? '');
            $id   = (int)$matched['id'];
            if ($type === 'book') {
                $bookIds[$id] = $id;
            } elseif ($type === 'author') {
                $authorIds[$id] = $id;
            } else {
                $tagIds[$id] = $id;
            }
        }

        // Match all unique authors
        foreach ($allAuthors as $authorName) {
            $matched = $matchTag($authorName);
            if (!$matched) {
                continue;
            }
            $type = strtolower($matched['type'] ?? '');
            $id   = (int)$matched['id'];
            if ($type === 'author') {
                $authorIds[$id] = $id;
            } elseif ($type === 'book') {
                $bookIds[$id] = $id;
            } else {
                $tagIds[$id] = $id;
            }
        }

        // ── Build per-date ID strings ─────────────────────────────────────
        $perDateIds = [];
        foreach ($perDate as $dateKey => $entry) {
            $dBookIds   = [];
            $dAuthorIds = [];
            $dTagIds    = [];

            foreach ($entry['books'] as $bt) {
                $matched = $matchTag($bt);
                if (!$matched) {
                    continue;
                }
                $t  = strtolower($matched['type'] ?? '');
                $id = (int)$matched['id'];
                if ($t === 'book') { $dBookIds[$id]   = $id; }
                elseif ($t === 'author') { $dAuthorIds[$id] = $id; }
                else { $dTagIds[$id] = $id; }
            }

            foreach ($entry['authors'] as $an) {
                $matched = $matchTag($an);
                if (!$matched) {
                    continue;
                }
                $t  = strtolower($matched['type'] ?? '');
                $id = (int)$matched['id'];
                if ($t === 'author') { $dAuthorIds[$id] = $id; }
                elseif ($t === 'book') { $dBookIds[$id]   = $id; }
                else { $dTagIds[$id] = $id; }
            }

            $perDateIds[$dateKey] = [
                'book_ids'   => $dBookIds   ? implode(',', $dBookIds)   : null,
                'author_ids' => $dAuthorIds ? implode(',', $dAuthorIds) : null,
                'tag_ids'    => $dTagIds    ? implode(',', $dTagIds)    : null,
            ];
        }

        return [
            'tag_ids'    => $tagIds    ? implode(',', $tagIds)    : null,
            'book_ids'   => $bookIds   ? implode(',', $bookIds)   : null,
            'author_ids' => $authorIds ? implode(',', $authorIds) : null,
            'per_date'   => $perDateIds,  // 'MM-DD' → per-row IDs
        ];
    }
}