<?php


namespace App\Models;

use CodeIgniter\Model;

class Tags extends Model {

    protected $table = 'tbl_tags';

    protected $primaryKey = 'id';

    protected $allowedFields = ['type', 'title'];


    public function __construct() {
        parent::__construct();
    }

    public function getTagsName($ids) {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_tags');

        $ids_array = explode(",", $ids);
        $builder->select('title');
        $builder->whereIn('id', $ids_array);
        $query = $builder->get();
        $query = $query->getResultObject();
        $titles = [];
        foreach ($query as $row) {
            $titles[] = $row->title;
        }
        return implode(",", $titles);
    }

    public function getTagNameById($ids) {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_tags');
        $title = array();
        $builder->select('title');
        $builder->where('id', $ids);
        $querys = $builder->get();
        $querys = $querys->getResultObject();

        if (count($querys) > 0) {
            foreach ($querys as $query) {
                $title = $query->title;
            }
        }
        return $title;
    }

    public function getTagsCounts($ids) {
        $query = $this->db->query("SELECT * FROM tbl_devotional WHERE FIND_IN_SET('$ids', tag_ids) > 0");
        return $query->num_rows();
    }

    public function getBookCounts($ids) {
        $query = $this->db->query("SELECT * FROM tbl_devotional WHERE FIND_IN_SET('$ids', book_ids) > 0");
        return $query->num_rows();
    }

    public function getAuthorCounts($ids) {
        $query = $this->db->query("SELECT * FROM tbl_devotional WHERE FIND_IN_SET('$ids', author_ids) > 0");
        return $query->num_rows();
    }

    public function getTagTypeById($ids) {
        $this->db->select('type');
        $this->db->where('id', $ids);
        $query = $this->db->get('tbl_tags');
        $title = '';
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $title = ($row->type == 'Tags') ? 'Keyword' : $row->type;
            }
        }
        return $title;
    }

    public function getStringCount($str, $text) {
        $str = trim($str);
        $found = 0;
        if (!empty($str)) {
            $str = str_replace(";", ",", $str);
            $str_array = explode(",", $str);
            foreach ($str_array as $str_val) {
                if (preg_match("/" . $str_val . "/i", $text)) {
                    $found++;
                }
            }
        }
        return $found;
    }

    public function getTagsCount($GET, $devotionalArray) {
        $count_match_array = [];
        if (isset($GET['Tags'])) {
            $tag_ids = $devotionalArray->tag_ids;
            $tag_array = [];
            if ($tag_ids != '') {
                $tag_array = explode(",", $tag_ids);
                $count_tags_array = array_intersect($GET['Tags'], $tag_array);
                $count_match_array['tags'] = count($count_tags_array);
            }
        }
        if (isset($GET['books'])) {
            $books_id = $devotionalArray->book_ids;
            $books_array = [];
            if ($books_id != '') {
                $books_array = explode(",", $books_id);
                $count_book_array = array_intersect($GET['books'], $books_array);
                $count_match_array['books'] = count($count_book_array);
            }
        }
        if (isset($GET['author'])) {
            $author_ids = $devotionalArray->author_ids;
            $author_array = [];
            if ($author_ids != '') {
                $author_array = explode(",", $author_ids);
                $count_author_array = array_intersect($GET['author'], $author_array);
                $count_match_array['author'] = count($count_author_array);
            }
        }
        return array_sum($count_match_array);
    }
}