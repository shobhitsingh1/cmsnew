<?php

namespace App\Controllers;

use App\Models\McpModel;
use CodeIgniter\RESTful\ResourceController;

class Mcp extends ResourceController
{
    protected $format = 'json';

    const SERVER_NAME = 'CMS MCP Connector';
    const SERVER_VERSION = '1.0.0';
    const PROTOCOL_VERSION = '2024-11-05';

    protected $mcpModel;

    public function __construct()
    {
        $this->mcpModel = new McpModel();
    }

    public function index()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->fail('Only POST method allowed', 405);
        }

        if (!$this->authenticate()) {
            return $this->failUnauthorized('Invalid or missing API key');
        }

        $body = $this->request->getJSON(true);

        if (!$body) {
            return $this->fail('Invalid JSON', 400);
        }

        $method = $body['method'] ?? '';
        $params = $body['params'] ?? [];
        $id = $body['id'] ?? null;

        switch ($method) {
            case 'initialize':
                return $this->sendResult([
                    'protocolVersion' => self::PROTOCOL_VERSION,
                    'serverInfo' => [
                        'name' => self::SERVER_NAME,
                        'version' => self::SERVER_VERSION,
                    ],
                    'capabilities' => [
                        'tools' => ['listChanged' => false],
                    ],
                ], $id);

            case 'tools/list':
                return $this->sendResult([
                    'tools' => $this->getToolDefinitions(),
                ], $id);

            case 'tools/call':
                return $this->handleToolsCall($params, $id);

            default:
                return $this->sendError(-32601, 'Method not found', $id);
        }
    }

    // =========================================================
    // TOOL DISPATCH
    // =========================================================

    private function handleToolsCall(array $params, $id)
    {
        $tool = $params['name'] ?? '';
        $input = $params['arguments'] ?? [];

        switch ($tool) {
            case 'list_content':
                $result = $this->toolListContent($input);
                break;

            case 'get_content':
                $result = $this->toolGetContent($input);
                break;

            case 'search_content':
                $result = $this->toolSearchContent($input);
                break;

            case 'list_tags':
                $result = $this->mcpModel->listtags();
                break;

            case 'update_content':
                if (!env('mcp.allowWrite', false)) {
                    $result = ['error' => 'Write disabled'];
                } else {
                    $result = $this->toolUpdateContent($input);
                }
                break;

            case 'parse_devotional_text':
                $result = $this->mcpModel->toolParseDevotionalText($input);
                break;

            case 'import_devotional_rows':
                if (!env('mcp.allowWrite', false)) {
                    $result = ['error' => 'Write disabled'];
                } else {
                    // Forward flush_tmp flag computed by parse step (or caller)
                    $result = $this->mcpModel->toolImportDevotionalRows($input);
                }
                break;

            default:
                return $this->sendError(-32602, 'Unknown tool', $id);
        }

        return $this->sendResult([
            'content' => [
                [
                    'type' => 'text',
                    'text' => json_encode($result, JSON_PRETTY_PRINT),
                ],
            ],
        ], $id);
    }

    // =========================================================
    // TOOL IMPLEMENTATIONS
    // =========================================================

    private function toolListContent(array $input): array
    {
        $limit = min((int) ($input['limit'] ?? 10), 100);
        $offset = (int) ($input['offset'] ?? 0);

        return $this->mcpModel->listContent($limit, $offset);
    }

    private function toolGetContent(array $input): array
    {
        if (empty($input['id'])) {
            return ['error' => 'ID required'];
        }

        $data = $this->mcpModel->getContentById((int) $input['id']);

        return $data ?: ['error' => 'Not found'];
    }

    private function toolSearchContent(array $input): array
    {
        if (empty($input['query'])) {
            return ['error' => 'Query required'];
        }

        return $this->mcpModel->searchContent(
            $input['query'],
            min((int) ($input['limit'] ?? 10), 50)
        );
    }

    private function toolUpdateContent(array $input): array
    {
        if (empty($input['id'])) {
            return ['error' => 'ID required'];
        }

        helper('security');

        $data = [];

        if (isset($input['title'])) {
            $data['title'] = esc($input['title']);
        }

        if (isset($input['body'])) {
            $data['text'] = esc($input['body']);
        }

        if (isset($input['status'])) {
            $data['active'] = esc($input['status']);
        }

        if (empty($data)) {
            return ['error' => 'No data to update'];
        }

        $success = $this->mcpModel->updateContent((int) $input['id'], $data);

        return $success
            ? ['success' => true]
            : ['error' => 'Update failed'];
    }

    // =========================================================
    // TOOL DEFINITIONS  (schema exposed to Manus / MCP clients)
    // =========================================================

    private function getToolDefinitions(): array
    {
        return [
            [
                'name' => 'list_content',
                'description' => 'List devotional content with pagination.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer', 'description' => 'Max rows to return (1-100, default 10)'],
                        'offset' => ['type' => 'integer', 'description' => 'Pagination offset (default 0)'],
                    ],
                ],
            ],
            [
                'name' => 'get_content',
                'description' => 'Get a single devotional by its database ID. Returns full row including resolved tag/book/author titles.',
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['id'],
                    'properties' => [
                        'id' => ['type' => 'integer', 'description' => 'tbl_devotional.id'],
                    ],
                ],
            ],
            [
                'name' => 'search_content',
                'description' => 'Search devotionals by keyword across title, subtitle, and body text.',
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['query'],
                    'properties' => [
                        'query' => ['type' => 'string', 'description' => 'Search keyword'],
                        'limit' => ['type' => 'integer', 'description' => 'Max results (1-50, default 10)'],
                    ],
                ],
            ],
            [
                'name' => 'list_tags',
                'description' => 'List all tags, books, and authors from tbl_tags (id, title, tag_type).',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass(),
                ],
            ],
            // [
            //     'name' => 'update_content',
            //     'description' => 'Update a devotional record. Requires mcp.allowWrite=true.',
            //     'inputSchema' => [
            //         'type' => 'object',
            //         'required' => ['id'],
            //         'properties' => [
            //             'id' => ['type' => 'integer', 'description' => 'tbl_devotional.id'],
            //             'title' => ['type' => 'string'],
            //             'body' => ['type' => 'string', 'description' => 'Maps to the text column'],
            //             'status' => ['type' => 'string', 'description' => 'Maps to the active column (0 or 1)'],
            //         ],
            //     ],
            // ],
            [
                'name' => 'parse_devotional_text',
                'description' => <<<'DESC'
                    Parse raw devotional text into structured row sets for tbl_devotional and tbl_devotional_tmp.

                    Supports BOTH English and Spanish date lines:
                    English: "Monday January 6"
                    Spanish: "lunes, 1 de enero"  or  "domingo, 1 de marzo de 2026"
                    Spanish month names are automatically translated to build the correct devotional_date.

                    Quarter code (date_quarter) is auto-computed per entry from the month number and year:
                    Format: MMYY  (e.g. "0126" for January 2026, "1225" for December 2025)
                    Pass 'filename' to auto-detect the quarter group (DJF / MAM / JJA / SON).
                    DJF: December uses date_year, January/February use date_year+1 automatically.
                    Override per-entry computation by supplying 'date_quarter' explicitly.

                    Pass 'references_text' (the raw "Referencias" section from the document) to
                    auto-resolve book/author IDs from tbl_tags. Matched IDs are merged into
                    book_ids / author_ids (caller-supplied values take precedence).

                    Text format (each entry separated by a blank line):
                    Line 1 – date line (English or Spanish as above)
                    Line 2 – title. Append "(1)" to start a series, "(2)" "(3)" … to continue.
                    Line 3 – subtitle
                    Line 4+ – body

                    Returns:
                    devotional_rows  – all entries (non-series + series)
                    tmp_rows         – series-only entries for tbl_devotional_tmp staging
                    flush_tmp        – true when start_processing == series_processing (last batch)
                    DESC,
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['text', 'lang', 'date_year'],
                    'properties' => [
                        'text' => ['type' => 'string', 'description' => 'Raw devotional text, entries separated by blank lines.'],
                        'lang' => ['type' => 'string', 'enum' => ['en', 'es'], 'description' => 'Language code.'],
                        'date_year' => ['type' => 'integer', 'description' => 'Base year for dates (e.g. 2025 for DJF 2025-26). January/February in DJF will automatically use date_year+1.'],
                        'filename' => ['type' => 'string', 'description' => 'Original filename (e.g. "TWFYT_SPANISH_DJF_2025-26_..."). Used to auto-detect quarter group (DJF/MAM/JJA/SON) for quarter code computation.'],
                        'date_quarter' => ['type' => 'string', 'description' => 'Override auto-computed quarter code (format MMYY, e.g. "0126"). Leave blank to auto-compute from filename.'],
                        'references_text' => ['type' => 'string', 'description' => 'Raw "Referencias" / "References" section from the document. Book/author titles are matched against tbl_tags to auto-populate book_ids/author_ids.'],
                        'acknowledgements' => ['type' => 'string'],
                        'tag_ids' => ['type' => 'string', 'description' => 'Comma-separated tag IDs (overrides auto-resolved values from references_text).'],
                        'book_ids' => ['type' => 'string', 'description' => 'Comma-separated book IDs (overrides auto-resolved values from references_text).'],
                        'author_ids' => ['type' => 'string', 'description' => 'Comma-separated author IDs (overrides auto-resolved values from references_text).'],
                        'user_id' => ['type' => 'integer', 'description' => 'CMS user submitting the content.'],
                        'series_processing' => ['type' => 'integer', 'description' => 'Total entries in this series batch (0 = not a series batch).'],
                        'start_processing' => ['type' => 'integer', 'description' => 'How many entries have been processed so far (0-based). When equal to series_processing the tmp table is flushed.'],
                    ],
                ],
            ],
            [
                'name' => 'import_devotional_rows',
                'description' => <<<'DESC'
                    Insert or update devotional rows into tbl_devotional and tbl_devotional_tmp.

                    Pass the full output of parse_devotional_text as input:
                    rows              – devotional_rows from parse step
                    flush_tmp         – flush flag from parse step (copies series tmp rows to main table)
                    series_processing – total series count
                    start_processing  – current entry count

                    Non-series rows (series_id == 0) go directly to tbl_devotional.
                    Series rows (series_id > 0) go to tbl_devotional_tmp first; when flush_tmp is true
                    they are copied to tbl_devotional and the tmp staging rows are purged.
                    Duplicate detection is by devotional_date (existing dates are updated, not duplicated).
                    DESC,
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['rows', 'user_id'],
                    'properties' => [
                        'rows' => ['type' => 'array', 'description' => 'devotional_rows from parse_devotional_text.', 'items' => ['type' => 'object']],
                        'user_id' => ['type' => 'integer'],
                        'series_processing' => ['type' => 'integer', 'description' => 'Total entries in series (0 = non-series).'],
                        'start_processing' => ['type' => 'integer', 'description' => 'Current count. Flush happens when equal to series_processing.'],
                        'flush_tmp' => ['type' => 'boolean', 'description' => 'Set to true on the last batch entry to move tmp rows to main table.'],
                    ],
                ],
            ],
        ];
    }

    // =========================================================
    // AUTH
    // =========================================================

    private function authenticate(): bool
    {
        $expected = env('mcp.apiKey');

        if (!$expected) {
            return false;
        }

        $header = $this->request->getHeaderLine('Authorization');

        if (!$header) {
            return false;
        }

        $key = str_replace('Bearer ', '', $header);

        return hash_equals($expected, trim($key));
    }

    // =========================================================
    // RESPONSE HELPERS
    // =========================================================

    private function sendResult(array $result, $id)
    {
        return $this->response->setJSON([
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ]);
    }

    private function sendError(int $code, string $message, $id)
    {
        return $this->response->setJSON([
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ]);
    }
}
