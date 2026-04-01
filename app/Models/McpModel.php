<?php

namespace App\Models;

use CodeIgniter\Model;

class McpModel extends Model
{
    protected $table = 'tbl_devotional';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'lang','title','subtitle','text','series_id',
        'acknowledgements','tag_ids','created_on',
        'date_quarter','date_year','active',
        'book_ids','author_ids','devotional_date','user_id'
    ];

    const TABLE_CONTENT = 'tbl_devotional';
    const TABLE_TAGS = 'tbl_tags';
    const TABLE_USERS = 'tbl_admin_user';

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    // =========================================================
    // LIST CONTENT
    // =========================================================
    public function listContent($limit = 10, $offset = 0)
    {
        $builder = $this->db->table(self::TABLE_CONTENT);

        $items = $builder
            ->select('id, title, subtitle, text, acknowledgements, created_on')
            ->orderBy('created_on', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        $total = $this->db->table(self::TABLE_CONTENT)->countAllResults();

        return [
            'items'  => $items,
            'total'  => $total,
            'limit'  => $limit,
            'offset' => $offset,
        ];
    }

    // =========================================================
    // GET SINGLE CONTENT
    // =========================================================
    public function getContentById($id)
    {
        $builder = $this->db->table(self::TABLE_CONTENT);

        $content = $builder
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$content) {
            return null;
        }

        // Author
        if (!empty($content['author_ids'])) {
            $content['author_name'] = $this->getAuthorName($content['author_ids']);
        }

        return $content;
    }

    // =========================================================
    // SEARCH
    // =========================================================
    public function searchContent($keyword, $limit = 10)
    {
        $builder = $this->db->table(self::TABLE_CONTENT);

        $results = $builder
            ->select('id, title, text, created_on')
            ->groupStart()
                ->like('title', $keyword)
                ->orLike('text', $keyword)
            ->groupEnd()
            ->orderBy('created_on', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return [
            'query'   => $keyword,
            'count'   => count($results),
            'results' => $results,
        ];
    }

    // =========================================================
    // UPDATE
    // =========================================================
    public function updateContent($id, $data)
    {
        $builder = $this->db->table(self::TABLE_CONTENT);

        $data['updated_at'] = date('Y-m-d H:i:s');

        return $builder
            ->where('id', $id)
            ->update($data);
    }

    // =========================================================
    // Tags
    // =========================================================
    public function listtags()
    {
        $builder = $this->db->table(self::TABLE_TAGS);

        $tags = $builder
            ->select('id, title, title_es , type')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        return  [
            'results' => $tags,
            'total'      => count($tags),
        ];

    }

    // =========================================================
    // HELPERS
    // =========================================================
    private function getAuthorName($userId)
    {
        return $this->db->table(self::TABLE_USERS)
            ->select('username')
            ->where('id', $userId)
            ->get()
            ->getRow('username');
    }
}