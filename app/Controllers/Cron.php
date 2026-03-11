<?php

namespace App\Controllers;

use App\Models\DevotionalModel;

class Cron extends BaseController
{
    // private $fastapi_url = env('FASTAPI_URL', 'http://localhost:9000');
    private $fastapi_url;

    public function __construct()
    {
        $this->fastapi_url = env('FASTAPI_BASE_URL') ?: 'http://localhost:5000';
    }
    private $devotionalModel;
    
    /**
     * Add log message
     */
    private function log($message, $level = 'info')
    {
        log_message($level, '[CRON] ' . $message);
        error_log('[CRON ' . strtoupper($level) . '] ' . $message);
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
     * Main cron page - PUBLIC ACCESS
     */
    public function index()
    {
        $this->log('Accessing cron index page');
        
        try {
            // Initialize model
            $this->devotionalModel = new DevotionalModel();
            $this->log('DevotionalModel initialized');
            
            $data['title'] = 'Cron Sync';
            $data['class_name'] = 'cron';
            $data['active_menu'] = 'cron';
            
            // Get stats
            $this->log('Getting sync statistics...');
            $stats = $this->get_sync_stats();
            $data['stats'] = $stats;
            $this->log('Stats retrieved: ' . json_encode($stats));
            
            // Load view
            $this->log('Loading cron view');
            return view('content/cron_view', $data);
                
        } catch (\Exception $e) {
            $this->log('Error in index(): ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
            return 'Error loading cron page: ' . $e->getMessage();
        }
    }
    
    /**
     * Sync today's devotionals - PUBLIC ACCESS
     */
    public function sync_today()
    {
        $this->log('Starting sync_today()');
        
        try {
            $this->devotionalModel = new DevotionalModel();
            $this->log('Model initialized for sync_today');
            
            $devotionals = $this->devotionalModel->get_today_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals for today');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found for today');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No devotionals found for today',
                    'stats' => ['total_found' => 0, 'synced' => 0, 'skipped' => 0]
                ]);
            }
            
            $results = [
                'total_found' => count($devotionals),
                'synced' => 0,
                'skipped' => 0,
                'failed' => 0,
                'details' => []
            ];
            
            $this->log('Processing ' . count($devotionals) . ' devotionals');
            
            foreach ($devotionals as $index => $devotional) {
                $this->log('Processing devotional ' . ($index + 1) . ' of ' . count($devotionals) . ': ID=' . $devotional['id'] . ', Title=' . $devotional['title']);
                
                $result = $this->sync_single_devotional($devotional);
                $results['details'][] = $result;
                
                if ($result['status'] === 'synced') {
                    $results['synced']++;
                    $this->log('Devotional ID ' . $devotional['id'] . ' synced successfully');
                } elseif ($result['status'] === 'skipped') {
                    $results['skipped']++;
                    $this->log('Devotional ID ' . $devotional['id'] . ' skipped: ' . ($result['reason'] ?? 'No reason'));
                } else {
                    $results['failed']++;
                    $this->log('Devotional ID ' . $devotional['id'] . ' failed: ' . ($result['reason'] ?? 'No reason'), 'error');
                }
            }
            
            $this->log('Sync completed: ' . $results['synced'] . ' synced, ' . $results['skipped'] . ' skipped, ' . $results['failed'] . ' failed');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Sync completed: {$results['synced']} synced, {$results['skipped']} skipped, {$results['failed']} failed",
                'stats' => $results
            ]);
                
        } catch (\Exception $e) {
            $this->log('Exception in sync_today: ' . $e->getMessage(), 'error');
            $this->log('Trace: ' . $e->getTraceAsString(), 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sync last hour's devotionals - PUBLIC ACCESS
     */
    public function sync_hourly()
    {
        $this->log('Starting sync_hourly()');
        
        try {
            $this->devotionalModel = new DevotionalModel();
            $devotionals = $this->devotionalModel->get_last_hour_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals in last hour');
            
            if (empty($devotionals)) {
                $this->log('No devotionals found in last hour');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No devotionals found in the last hour',
                    'stats' => ['total_found' => 0, 'synced' => 0, 'skipped' => 0]
                ]);
            }
            
            $results = $this->sync_devotionals_batch($devotionals, 'hourly');
            $this->log('Hourly sync completed with results: ' . json_encode($results));
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Hourly sync completed: {$results['synced']} synced",
                'stats' => $results
            ]);
                
        } catch (\Exception $e) {
            $this->log('Exception in sync_hourly: ' . $e->getMessage(), 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Hourly sync failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Sync weekly devotionals - PUBLIC ACCESS
     */
    public function sync_weekly()
    {
        $this->log('Starting sync_weekly()');
        
        try {
            $this->devotionalModel = new DevotionalModel();
            $devotionals = $this->devotionalModel->get_weekly_devotionals();
            $this->log('Found ' . count($devotionals) . ' devotionals in last week');
            if (empty($devotionals)) {
                $this->log('No devotionals found in last week');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'No devotionals found in the last week',
                    'stats' => ['total_found' => 0, 'synced' => 0, 'skipped' => 0]
                ]);
            }
            
            $results = $this->sync_devotionals_batch($devotionals, 'weekly');
            $this->log('Weekly sync completed with results: ' . json_encode($results));
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Weekly sync completed: {$results['synced']} synced",
                'stats' => $results
            ]);
                
        } catch (\Exception $e) {
            $this->log('Exception in sync_weekly: ' . $e->getMessage(), 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Weekly sync failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Manual sync with date range - PUBLIC ACCESS
     */
    public function sync_manual()
    {
        $this->log('Starting sync_manual()');
        
        $request = service('request');
        $start_date = $request->getPost('start_date');
        $end_date = $request->getPost('end_date');
        
        $this->log('Received dates - Start: ' . $start_date . ', End: ' . $end_date);
        
        if (!$start_date || !$end_date) {
            $this->log('Validation failed: Start or end date missing', 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Start date and end date are required'
            ]);
        }
        
        try {
            $this->devotionalModel = new DevotionalModel();
            $devotionals = $this->devotionalModel->get_devotionals_by_date_range($start_date, $end_date);
            $this->log('Found ' . count($devotionals) . ' devotionals between ' . $start_date . ' and ' . $end_date);
            
            if (empty($devotionals)) {
                $this->log('No devotionals found in date range');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "No devotionals found between $start_date and $end_date",
                    'stats' => ['total_found' => 0, 'synced' => 0, 'skipped' => 0]
                ]);
            }
            
            $results = $this->sync_devotionals_batch($devotionals, 'manual');
            $this->log('Manual sync completed with results: ' . json_encode($results));
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "Manual sync completed: {$results['synced']} synced",
                'stats' => $results,
                'date_range' => [
                    'start_date' => $start_date,
                    'end_date' => $end_date
                ]
            ]);
                
        } catch (\Exception $e) {
            $this->log('Exception in sync_manual: ' . $e->getMessage(), 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Manual sync failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Check if devotional exists in Milvus
     */
    private function check_devotional_in_milvus($devotional_id)
    {
        $this->log('Checking if devotional ' . $devotional_id . ' exists in Milvus');
        
        try {
            $url = $this->fastapi_url . "/devotional/check/$devotional_id";
            $this->log('Calling FastAPI URL: ' . $url);
            
            // $url = $this->fastapi_url . "/devotional/check/$devotional_id";

            $headers = $this->generate_secure_headers('');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            $this->log('FastAPI check response - HTTP Code: ' . $http_code . ', Response: ' . $response);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                $exists = $result['exists'] ?? false;
                $this->log('Devotional ' . $devotional_id . ' exists in Milvus: ' . ($exists ? 'YES' : 'NO'));
                return $exists;
            } else {
                $this->log('FastAPI check failed with HTTP ' . $http_code . ': ' . $curl_error, 'error');
                return false;
            }
            
        } catch (\Exception $e) {
            $this->log('Error checking devotional in Milvus: ' . $e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Sync a single devotional to Milvus
     */
    private function sync_single_devotional($devotional)
    {
        $devotional_id = $devotional['id'];
        $this->log('Syncing single devotional ID: ' . $devotional_id . ', Title: ' . ($devotional['title'] ?? 'N/A'));
        
        try {
            // Check if already exists in Milvus
            $exists = $this->check_devotional_in_milvus($devotional_id);
            
            if ($exists) {
                $this->log('Devotional ' . $devotional_id . ' already exists in Milvus - skipping');
                return [
                    'id' => $devotional_id,
                    'title' => $devotional['title'],
                    'status' => 'skipped',
                    'reason' => 'Already exists in Milvus'
                ];
            }
            
            // Prepare data for Milvus
            $this->log('Preparing data for devotional ' . $devotional_id);
            $prepared_data = $this->devotionalModel->prepare_devotional_data($devotional);
            $this->log('Data prepared successfully for devotional ' . $devotional_id);
            
            // Send to FastAPI for insertion
            $url = $this->fastapi_url . "/devotional/create";
            $this->log('Sending to FastAPI: ' . $url);
            
            $json_data = json_encode($prepared_data);
            $this->log('JSON data length: ' . strlen($json_data) . ' bytes');
            $headers = $this->generate_secure_headers($json_data);
            $headers[] = 'Content-Length: ' . strlen($json_data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $this->log('FastAPI response - HTTP Code: ' . $http_code . ', Response: ' . $response);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                
                if ($result['success']) {
                    $this->log('Devotional ' . $devotional_id . ' synced successfully to Milvus');
                    return [
                        'id' => $devotional_id,
                        'title' => $devotional['title'],
                        'status' => 'synced',
                        'milvus_id' => $result['milvus_id'] ?? $devotional_id,
                        'message' => $result['message']
                    ];
                } else {
                    $this->log('FastAPI returned error for devotional ' . $devotional_id . ': ' . ($result['error'] ?? 'Unknown error'), 'error');
                    return [
                        'id' => $devotional_id,
                        'title' => $devotional['title'],
                        'status' => 'failed',
                        'reason' => $result['error'] ?? 'Unknown error from FastAPI'
                    ];
                }
            } else {
                $this->log('HTTP error for devotional ' . $devotional_id . ': ' . $http_code . ' - ' . $error, 'error');
                return [
                    'id' => $devotional_id,
                    'title' => $devotional['title'],
                    'status' => 'failed',
                    'reason' => "HTTP $http_code: " . ($error ?: $response)
                ];
            }
            
        } catch (\Exception $e) {
            $this->log('Exception syncing devotional ' . $devotional_id . ': ' . $e->getMessage(), 'error');
            return [
                'id' => $devotional_id,
                'title' => $devotional['title'],
                'status' => 'failed',
                'reason' => 'Exception: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Sync multiple devotionals in batch
     */
    private function sync_devotionals_batch($devotionals, $sync_type = 'manual')
    {
        $this->log('Starting batch sync for ' . count($devotionals) . ' devotionals (type: ' . $sync_type . ')');
        
        $results = [
            'total_found' => count($devotionals),
            'synced' => 0,
            'skipped' => 0,
            'failed' => 0,
            'sync_type' => $sync_type,
            'details' => []
        ];
        
        // Prepare batch data
        $batch_data = [];
        $to_sync = [];
        
        $this->log('Checking which devotionals need to be synced...');
        
        foreach ($devotionals as $index => $devotional) {
            $devotional_id = $devotional['id'];
            $this->log('Checking devotional ' . ($index + 1) . ' of ' . count($devotionals) . ': ID=' . $devotional_id);
            
            // Check if exists in Milvus
            $exists = $this->check_devotional_in_milvus($devotional_id);
            
            if ($exists) {
                $this->log('Devotional ' . $devotional_id . ' exists in Milvus - skipping');
                $results['details'][] = [
                    'id' => $devotional_id,
                    'title' => $devotional['title'],
                    'status' => 'skipped',
                    'reason' => 'Already exists in Milvus'
                ];
                $results['skipped']++;
            } else {
                $this->log('Devotional ' . $devotional_id . ' needs to be synced');
                $to_sync[] = $devotional;
            }
        }
        
        $this->log('Batch check complete: ' . count($to_sync) . ' to sync, ' . $results['skipped'] . ' skipped');
        
        // If nothing to sync, return
        if (empty($to_sync)) {
            $this->log('No devotionals to sync - returning');
            return $results;
        }
        
        // Prepare batch request
        $this->log('Preparing batch request for ' . count($to_sync) . ' devotionals');
        $batch_request = [];
        foreach ($to_sync as $devotional) {
            $prepared_data = $this->devotionalModel->prepare_devotional_data($devotional);
            $batch_request[] = $prepared_data;
        }
        
        $this->log('Batch request prepared with ' . count($batch_request) . ' items');
        
        // Send batch request to FastAPI
        try {
            $url = $this->fastapi_url . "/devotional/batch-create";
            $this->log('Sending batch request to FastAPI: ' . $url);
            
            $json_data = json_encode($batch_request);
            $this->log('Batch JSON data length: ' . strlen($json_data) . ' bytes');
            $headers = $this->generate_secure_headers($json_data);
            $headers[] = 'Content-Length: ' . strlen($json_data);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $this->log('Batch FastAPI response - HTTP Code: ' . $http_code . ', Response length: ' . strlen($response));
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                
                if ($result['success']) {
                    $results['synced'] = $result['total_created'] ?? count($batch_request);
                    $this->log('Batch insert successful: ' . $results['synced'] . ' items created');
                    
                    // Add successful sync details
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
                    foreach ($to_sync as $devotional) {
                        $results['details'][] = [
                            'id' => $devotional['id'],
                            'title' => $devotional['title'],
                            'status' => 'failed',
                            'reason' => $result['error'] ?? 'Batch insert failed'
                        ];
                    }
                }
            } else {
                $results['failed'] = count($to_sync);
                $this->log('HTTP error in batch insert: ' . $http_code, 'error');
                foreach ($to_sync as $devotional) {
                    $results['details'][] = [
                        'id' => $devotional['id'],
                        'title' => $devotional['title'],
                        'status' => 'failed',
                        'reason' => "HTTP $http_code"
                    ];
                }
            }
            
        } catch (\Exception $e) {
            $results['failed'] = count($to_sync);
            $this->log('Exception in batch sync: ' . $e->getMessage(), 'error');
            foreach ($to_sync as $devotional) {
                $results['details'][] = [
                    'id' => $devotional['id'],
                    'title' => $devotional['title'],
                    'status' => 'failed',
                    'reason' => 'Exception: ' . $e->getMessage()
                ];
            }
        }
        
        $this->log('Batch sync complete: ' . $results['synced'] . ' synced, ' . $results['skipped'] . ' skipped, ' . $results['failed'] . ' failed');
        return $results;
    }
    
    /**
     * Get sync statistics
     */
    private function get_sync_stats()
    {
        $this->log('Getting sync statistics');
        
        try {
            $this->devotionalModel = new DevotionalModel();
            
            $today_count = count($this->devotionalModel->get_today_devotionals());
            $hourly_count = count($this->devotionalModel->get_last_hour_devotionals());
            $weekly_count = count($this->devotionalModel->get_weekly_devotionals());
            
            $db = db_connect();
            $builder = $db->table('tbl_devotional');
            $total_count = $builder->countAll();
            
            $this->log('Stats calculated - Today: ' . $today_count . ', Hourly: ' . $hourly_count . ', Weekly: ' . $weekly_count . ', Total: ' . $total_count);
            
            $stats = [
                'today' => [
                    'count' => $today_count,
                    'label' => 'Today\'s Devotionals'
                ],
                'hourly' => [
                    'count' => $hourly_count,
                    'label' => 'Last Hour'
                ],
                'weekly' => [
                    'count' => $weekly_count,
                    'label' => 'Last 7 Days'
                ],
                'total' => [
                    'count' => $total_count,
                    'label' => 'Total Devotionals'
                ]
            ];
            
            return $stats;
            
        } catch (\Exception $e) {
            $this->log('Error getting stats: ' . $e->getMessage(), 'error');
            // Return default stats on error
            return [
                'today' => ['count' => 0, 'label' => 'Today\'s Devotionals'],
                'hourly' => ['count' => 0, 'label' => 'Last Hour'],
                'weekly' => ['count' => 0, 'label' => 'Last 7 Days'],
                'total' => ['count' => 0, 'label' => 'Total Devotionals']
            ];
        }
    }
    
    /**
     * Test FastAPI connection - PUBLIC ACCESS
     */
    public function test_connection()
    {
        $this->log('Testing FastAPI connection to: ' . $this->fastapi_url);
        
        try {
            $url = $this->fastapi_url . "/health";
            $this->log('Testing URL: ' . $url);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);
            
            $this->log('Connection test response - HTTP Code: ' . $http_code . ', Response: ' . $response);
            
            if ($http_code === 200) {
                $this->log('FastAPI connection successful');
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'FastAPI connection successful',
                    'health' => json_decode($response, true)
                ]);
            } else {
                $this->log('FastAPI connection failed with HTTP ' . $http_code . ': ' . $curl_error, 'error');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "FastAPI connection failed (HTTP $http_code)",
                    'response' => $response,
                    'curl_error' => $curl_error
                ]);
            }
            
        } catch (\Exception $e) {
            $this->log('Exception testing connection: ' . $e->getMessage(), 'error');
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
                'error' => $e->getTraceAsString()
            ]);
        }
    }
}