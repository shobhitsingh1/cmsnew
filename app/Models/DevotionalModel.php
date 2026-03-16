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

        //$devotional = $this->find($devotional_id);

        $devotional =  $this->where("id = $devotional_id")
                    ->findAll();
        
    
        // if (!$devotional || empty($devotional['tag_ids'])) {
        //     return '';
        // }
        
        // Parse tag IDs (assuming comma-separated)

        if(isset($devotional['tag_ids'])){
            $tag_ids = explode(',', $devotional['tag_ids']);
            $tag_ids = array_filter($tag_ids, 'is_numeric');
            $db = db_connect();
            $builder = $db->table('tbl_tags');
            $builder->select('GROUP_CONCAT(title) as tag_titles');
            $builder->whereIn('id', $tag_ids);
            $query = $builder->get();
            
            $result = $query->getRowArray();

        }
        
        return $result['tag_titles'] ?? '';
    }
    
    /**
     * Prepare devotional data for Milvus
     */
  public function prepare_devotional_data($devotional)
{
    // Get tag titles
    $tag_titles = $this->get_tag_titles_for_devotional($devotional['devotional_id']);
    
    $devotional_id = $devotional['devotional_id'];
    
    $devotional_data = $this->where('id', $devotional_id)->first();
    
    $combined_parts = [];
    
    if (!empty($devotional_data['title'])) {
        $combined_parts[] = trim($devotional_data['title']);
    }
    
    if (!empty($devotional_data['subtitle'])) {
        $combined_parts[] = trim($devotional_data['subtitle']);
    }
    
    if (!empty($devotional_data['text'])) {
        $text = trim($devotional_data['text']);
        if (strlen($text) > 500) {
            $text = substr($text, 0, 500) . '...';
        }
        $combined_parts[] = $text;
    }
    
    if (!empty($tag_titles)) {
        $combined_parts[] = "Tags: " . trim($tag_titles);
    }
    
    $combined_text = !empty($combined_parts) ? implode(' | ', $combined_parts) : '';
    
    $truncate = function($text, $max_length) {
        if (empty($text)) return '';
        $text = trim($text);
        if (strlen($text) > $max_length) {
            return substr($text, 0, $max_length) . '... [truncated]';
        }
        return $text;
    };
    
     return [
        'id' => (int) $devotional_data['id'],
        'title' => $truncate($devotional_data['title'] ?? '', 500),
        'subtitle' => $truncate($devotional_data['subtitle'] ?? '', 65535),
        'text' => $truncate($devotional_data['text'] ?? '', 65535),
        'series_id' => (int) ($devotional_data['series_id'] ?? 0),
        'lang' => $devotional_data['lang'] ?? '',
        'acknowledgements' => $truncate($devotional_data['acknowledgements'] ?? '', 65535),
        'tag_ids' => $truncate($devotional_data['tag_ids'] ?? '', 500),
        'tag_titles' => $truncate($tag_titles ?? '', 1000),
        'created_on' => $truncate($devotional_data['created_on'] ?? '', 100),
        'date_quarter' => $truncate($devotional_data['date_quarter'] ?? '', 50),
        'date_year' => $truncate($devotional_data['date_year'] ?? '', 20),
        'active' => $truncate($devotional_data['active'] ?? '0', 1),
        'book_ids' => $truncate($devotional_data['book_ids'] ?? '', 500),
        'author_ids' => $truncate($devotional_data['author_ids'] ?? '', 500),
        'devotional_date' => $truncate($devotional_data['devotional_date'] ?? '', 200),
        'user_id' => (int) ($devotional_data['user_id'] ?? 0),
        'combined_text' => $combined_text,  
    ];
  
}
}