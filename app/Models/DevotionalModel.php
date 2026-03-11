<?php

namespace App\Models;

use CodeIgniter\Model;

class DevotionalModel extends Model
{
    protected $table = 'tbl_devotional';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'subtitle', 'text', 'series_id', 'acknowledgements', 
                               'tag_ids', 'created_on', 'date_quarter', 'date_year', 'active',
                               'book_ids', 'author_ids', 'devotional_date', 'user_id'];
    
    /**
     * Get today's devotionals that haven't been synced to Milvus
     */
    public function get_today_devotionals()
    {
        $today = date('Y-m-d');
        
        return $this->where("DATE(created_on) = '$today'")
                    ->where('active', '1')
                    ->findAll();
    }
    
    /**
     * Get devotionals by date range
     */
    public function get_devotionals_by_date_range($start_date, $end_date)
    {
        return $this->where("DATE(created_on) >= '$start_date'")
                    ->where("DATE(created_on) <= '$end_date'")
                    ->where('active', '1')
                    ->findAll();
    }
    
    /**
     * Get last hour's devotionals
     */
    public function get_last_hour_devotionals()
    {
        $one_hour_ago = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        return $this->where("created_on >= '$one_hour_ago'")
                    ->where('active', '1')
                    ->findAll();
    }
    
    /**
     * Get weekly devotionals (last 7 days)
     */
    public function get_weekly_devotionals()
    {
        $one_week_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        return $this->where("created_on >= '$one_week_ago'")
                    ->where('active', '1')
                    ->findAll();
    }
    
    /**
     * Get tag titles for a devotional
     */
    public function get_tag_titles_for_devotional($devotional_id)
    {
        // First get tag IDs from devotional
        $devotional = $this->find($devotional_id);
        
        if (!$devotional || empty($devotional['tag_ids'])) {
            return '';
        }
        
        // Parse tag IDs (assuming comma-separated)
        $tag_ids = explode(',', $devotional['tag_ids']);
        $tag_ids = array_filter($tag_ids, 'is_numeric');
        
        if (empty($tag_ids)) {
            return '';
        }
        
        // Get tag titles using Query Builder
        $db = db_connect();
        $builder = $db->table('tbl_tags');
        $builder->select('GROUP_CONCAT(title) as tag_titles');
        $builder->whereIn('id', $tag_ids);
        $query = $builder->get();
        
        $result = $query->getRowArray();
        return $result['tag_titles'] ?? '';
    }
    
    /**
     * Prepare devotional data for Milvus
     */
    public function prepare_devotional_data($devotional)
    {
        // Get tag titles
        $tag_titles = $this->get_tag_titles_for_devotional($devotional['id']);
        
        // Prepare combined text
        $combined_parts = [
            $devotional['title'] ?? '',
            $devotional['subtitle'] ?? '',
            $devotional['text'] ?? ''
        ];
        
        // Add tag titles if available
        if ($tag_titles) {
            $combined_parts[] = "Tags: $tag_titles";
        }
        
        // Filter out empty parts
        $non_empty_parts = array_filter($combined_parts, function($part) {
            return !empty(trim($part));
        });
        
        $combined_text = implode(' | ', $non_empty_parts);
        
        // Truncate long texts
        $truncate = function($text, $max_length) {
            if (strlen($text) > $max_length) {
                return substr($text, 0, $max_length) . '... [truncated]';
            }
            return $text;
        };
        
        return [
            'id' => (int) $devotional['id'],
            'title' => $truncate($devotional['title'] ?? '', 500),
            'subtitle' => $truncate($devotional['subtitle'] ?? '', 65535),
            'text' => $truncate($devotional['text'] ?? '', 65535),
            'series_id' => (int) ($devotional['series_id'] ?? 0),
            'acknowledgements' => $truncate($devotional['acknowledgements'] ?? '', 65535),
            'tag_ids' => $truncate($devotional['tag_ids'] ?? '', 500),
            'tag_titles' => $truncate($tag_titles ?? '', 1000),
            'created_on' => $truncate($devotional['created_on'] ?? '', 100),
            'date_quarter' => $truncate($devotional['date_quarter'] ?? '', 50),
            'date_year' => $truncate($devotional['date_year'] ?? '', 20),
            'active' => $truncate($devotional['active'] ?? '0', 1),
            'book_ids' => $truncate($devotional['book_ids'] ?? '', 500),
            'author_ids' => $truncate($devotional['author_ids'] ?? '', 500),
            'devotional_date' => $truncate($devotional['devotional_date'] ?? '', 200),
            'user_id' => (int) ($devotional['user_id'] ?? 0),
            'combined_text' => $truncate($combined_text, 65535)
        ];
    }
}