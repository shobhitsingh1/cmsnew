<?php

namespace App\Controllers;

use App\Models\DevotionalModel;

class CronJob extends BaseController
{
    private $fastapi_url;

    public function __construct()
    {
        $this->fastapi_url = env('FASTAPI_BASE_URL') ?: 'http://localhost:9000';
    }
    
    /**
     * Add log message with timestamp
     */
    private function log($message, $level = 'info')
    {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [CRON $level] $message" . PHP_EOL;
        
        // Log to CodeIgniter system
        log_message($level, $message);
        
        // Also output to console if CLI
        if (is_cli()) {
            echo $logMessage;
        }
        
        // Log to file
        $logFile = WRITEPATH . 'logs/cron.log';
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
   
       /**
 * Generate secure HMAC headers
 */
    private function generate_secure_headers($payload = '')
    {
        $secret = env('HMAC_SECRET');

        if (!$secret) {
            throw new \Exception("HMAC_SECRET not configured in .env");
        }  // fetch from .env
        $timestamp = time();

        $signature = hash_hmac(
            'sha256',
            $payload . $timestamp,
            $secret
        );

        return [
            'Content-Type: application/json',
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature
        ];
    } 


    /**
     * Get devotional model instance
     */
    private function getDevotionalModel()
    {
        return new DevotionalModel();
    }
    
    /**
     * Check if running in CLI mode
     */
    private function checkCli()
    {
        if (!is_cli()) {
            die('This script can only be accessed via command line.');
        }
    }
    
    /**
     * Default index page
     */
    public function index()
    {
        $this->checkCli();
        $this->log('CronJob index accessed');
        echo "CronJob Controller - Use specific methods:\n";
        echo "php index.php cronjob daily\n";
        echo "php index.php cronjob hourly\n";
        echo "php index.php cronjob weekly\n";
        echo "php index.php cronjob manual 2024-01-01 2024-01-31\n";
        echo "php index.php cronjob test\n";
        echo "php index.php cronjob help\n";
    }
    
    /**
     * Run daily sync (for cron: 0 0 * * *)
     */
    public function daily()
    {
        $this->checkCli();
        $this->log('========== STARTING DAILY CRON JOB ==========');
        $this->log('Running daily sync at: ' . date('Y-m-d H:i:s'));
        
        try {
            $devotionalModel = $this->getDevotionalModel();
            $devotionals = $devotionalModel->get_today_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals for today');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found for today - nothing to sync');
                $this->log('========== DAILY CRON JOB COMPLETED ==========');
                return;
            }
            
            $results = $this->sync_devotionals_batch($devotionalModel, $devotionals, 'daily_cron');
            
            $this->log('Daily sync completed:');
            $this->log('- Total found: ' . $results['total_found']);
            $this->log('- Synced: ' . $results['synced']);
            $this->log('- Skipped: ' . $results['skipped']);
            $this->log('- Failed: ' . $results['failed']);
            
            $this->log('========== DAILY CRON JOB COMPLETED ==========');
            
        } catch (\Exception $e) {
            $this->log('ERROR in daily cron: ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
        }
    }
    
    /**
     * Run hourly sync (for cron: 0 * * * *)
     */
    public function hourly()
    {
        $this->checkCli();
        $this->log('========== STARTING HOURLY CRON JOB ==========');
        $this->log('Running hourly sync at: ' . date('Y-m-d H:i:s'));
        
        try {
            $devotionalModel = $this->getDevotionalModel();
            $devotionals = $devotionalModel->get_last_hour_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals in last hour');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found in last hour - nothing to sync');
                $this->log('========== HOURLY CRON JOB COMPLETED ==========');
                return;
            }
            
            $results = $this->sync_devotionals_batch($devotionalModel, $devotionals, 'hourly_cron');
            
            $this->log('Hourly sync completed:');
            $this->log('- Total found: ' . $results['total_found']);
            $this->log('- Synced: ' . $results['synced']);
            $this->log('- Skipped: ' . $results['skipped']);
            $this->log('- Failed: ' . $results['failed']);
            
            $this->log('========== HOURLY CRON JOB COMPLETED ==========');
            
        } catch (\Exception $e) {
            $this->log('ERROR in hourly cron: ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
        }
    }
    
    /**
     * Run weekly sync (for cron: 0 0 * * 0)
     */
    public function weekly()
    {
        $this->checkCli();
        $this->log('========== STARTING WEEKLY CRON JOB ==========');
        $this->log('Running weekly sync at: ' . date('Y-m-d H:i:s'));
        
        try {
            $devotionalModel = $this->getDevotionalModel();
            $devotionals = $devotionalModel->get_weekly_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals in last week');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found in last week - nothing to sync');
                $this->log('========== WEEKLY CRON JOB COMPLETED ==========');
                return;
            }
            
            $results = $this->sync_devotionals_batch($devotionalModel, $devotionals, 'weekly_cron');
            
            $this->log('Weekly sync completed:');
            $this->log('- Total found: ' . $results['total_found']);
            $this->log('- Synced: ' . $results['synced']);
            $this->log('- Skipped: ' . $results['skipped']);
            $this->log('- Failed: ' . $results['failed']);
            
            $this->log('========== WEEKLY CRON JOB COMPLETED ==========');
            
        } catch (\Exception $e) {
            $this->log('ERROR in weekly cron: ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
        }
    }
    
    /**
     * Manual sync by date range (for manual execution)
     */
    public function manual($start_date = null, $end_date = null)
    {
        $this->checkCli();
        $this->log('========== STARTING MANUAL CRON JOB ==========');
        
        if (!$start_date || !$end_date) {
            // Default to last 7 days
            $end_date = date('Y-m-d');
            $start_date = date('Y-m-d', strtotime('-7 days'));
        }
        
        $this->log("Running manual sync from $start_date to $end_date at: " . date('Y-m-d H:i:s'));
        
        try {
            $devotionalModel = $this->getDevotionalModel();
            $devotionals = $devotionalModel->get_devotionals_by_date_range($start_date, $end_date);
            $this->log('Found ' . count($devotionals) . ' devotionals in date range');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found in date range - nothing to sync');
                $this->log('========== MANUAL CRON JOB COMPLETED ==========');
                return;
            }
            
            $results = $this->sync_devotionals_batch($devotionalModel, $devotionals, 'manual_cron');
            
            $this->log('Manual sync completed:');
            $this->log('- Total found: ' . $results['total_found']);
            $this->log('- Synced: ' . $results['synced']);
            $this->log('- Skipped: ' . $results['skipped']);
            $this->log('- Failed: ' . $results['failed']);
            
            $this->log('========== MANUAL CRON JOB COMPLETED ==========');
            
        } catch (\Exception $e) {
            $this->log('ERROR in manual cron: ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
        }
    }
    
    /**
     * Test FastAPI connection
     */
    public function test()
    {
        $this->checkCli();
        $this->log('========== TESTING FASTAPI CONNECTION ==========');
        
        try {
            $url = $this->fastapi_url . "/health";
            $this->log('Testing URL: ' . $url);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                $this->log('SUCCESS: FastAPI connection working');
                $this->log('Response: ' . json_encode($result));
            } else {
                $this->log('ERROR: FastAPI connection failed (HTTP ' . $http_code . ')', 'error');
                $this->log('Response: ' . $response);
            }
            
        } catch (\Exception $e) {
            $this->log('ERROR testing connection: ' . $e->getMessage(), 'error');
        }
        
        $this->log('========== CONNECTION TEST COMPLETED ==========');
    }
    
    /**
     * Check if devotional exists in Milvus
     */
    private function check_devotional_in_milvus($devotional_id)
    {
        try {
            $url = $this->fastapi_url . "/devotional/check/$devotional_id";
            $headers = $this->generate_secure_headers('');
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                return $result['exists'] ?? false;
            }
            
            return false;
            
        } catch (\Exception $e) {
            $this->log('Error checking devotional ' . $devotional_id . ' in Milvus: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Sync multiple devotionals in batch
     */
    private function sync_devotionals_batch($devotionalModel, $devotionals, $sync_type = 'manual')
    {
        $results = [
            'total_found' => count($devotionals),
            'synced' => 0,
            'skipped' => 0,
            'failed' => 0,
            'sync_type' => $sync_type,
            'details' => []
        ];
        
        // Separate existing and new devotionals
        $to_sync = [];
        
        foreach ($devotionals as $devotional) {
            $devotional_id = $devotional['id'];
            
            // Check if exists in Milvus
            $exists = $this->check_devotional_in_milvus($devotional_id);
            
            if ($exists) {
                $results['details'][] = [
                    'id' => $devotional_id,
                    'title' => $devotional['title'],
                    'status' => 'skipped',
                    'reason' => 'Already exists in Milvus'
                ];
                $results['skipped']++;
                $this->log('Devotional ' . $devotional_id . ' already exists - skipping');
            } else {
                $to_sync[] = $devotional;
                $this->log('Devotional ' . $devotional_id . ' needs sync');
            }
        }
        
        $this->log('Batch check: ' . count($to_sync) . ' to sync, ' . $results['skipped'] . ' skipped');
        
        // If nothing to sync, return
        if (empty($to_sync)) {
            return $results;
        }
        
        // Prepare batch request
        $batch_request = [];
        foreach ($to_sync as $devotional) {
            $prepared_data = $devotionalModel->prepare_devotional_data($devotional);
            $batch_request[] = $prepared_data;
        }
        
        // Send batch request to FastAPI
        try {
            $url = $this->fastapi_url . "/devotional/batch-create";
            $this->log('Sending batch request to FastAPI: ' . $url);
            $jsonPayload = json_encode($batch_request);
            $headers = $this->generate_secure_headers($jsonPayload);
            $headers[] = 'Content-Length: ' . strlen($jsonPayload);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                
                if ($result['success']) {
                    $results['synced'] = $result['total_created'] ?? count($batch_request);
                    $this->log('Batch insert successful: ' . $results['synced'] . ' items created');
                    
                    foreach ($result['results'] ?? [] as $batch_result) {
                        $results['details'][] = [
                            'id' => $batch_result['assigned_id'] ?? $batch_result['milvus_id'] ?? 'unknown',
                            'title' => $batch_result['title'] ?? 'Unknown',
                            'status' => 'synced',
                            'message' => 'Batch inserted successfully'
                        ];
                    }
                } else {
                    $results['failed'] = count($to_sync);
                    $this->log('Batch insert failed: ' . ($result['error'] ?? 'Unknown error'), 'error');
                }
            } else {
                $results['failed'] = count($to_sync);
                $this->log('HTTP error in batch insert: ' . $http_code, 'error');
            }
            
        } catch (\Exception $e) {
            $results['failed'] = count($to_sync);
            $this->log('Exception in batch sync: ' . $e->getMessage(), 'error');
        }
        
        return $results;
    }
    
    /**
     * Show help information
     */
    public function help()
    {
        $this->checkCli();
        echo "=====================================\n";
        echo "CRON JOB HELP\n";
        echo "=====================================\n";
        echo "Usage: php index.php cronjob [method] [params]\n\n";
        echo "Available methods:\n";
        echo "  daily          - Run daily sync (today's devotionals)\n";
        echo "  hourly         - Run hourly sync (last hour devotionals)\n";
        echo "  weekly         - Run weekly sync (last 7 days)\n";
        echo "  manual [start] [end] - Manual sync with date range\n";
        echo "  test           - Test FastAPI connection\n";
        echo "  help           - Show this help\n\n";
        echo "Examples:\n";
        echo "  php index.php cronjob daily\n";
        echo "  php index.php cronjob hourly\n";
        echo "  php index.php cronjob weekly\n";
        echo "  php index.php cronjob manual 2024-01-01 2024-01-31\n";
        echo "  php index.php cronjob test\n";
        echo "=====================================\n";
    }
}