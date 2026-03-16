<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use App\Models\JobModel;
use App\Models\DevotionalModel;

class QueueWorker extends BaseCommand
{
    protected $group = 'Custom';
    protected $name = 'queue:work';
    protected $description = 'Process queue jobs';

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

 public function run(array $params)
{
    $jobModel = new JobModel();
    $devotionalModel = new DevotionalModel();

    // Get all pending jobs
    $jobs = $jobModel->where('status', 'pending')->findAll();

    if (!$jobs) {
        echo "No jobs found\n";
        return;
    }

    foreach ($jobs as $job) {

        // Mark job as processing
        $jobModel->update($job['id'], [
            'status' => 'processing',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $payload = json_decode($job['payload'], true);

        try {

            if ($job['type'] == 'send_devotional') {

                $results = [
                    'synced' => 0,
                    'failed' => 0,
                    'details' => []
                ];

                $batch_request = [];

                // Prepare devotional data
                $prepared_data = $devotionalModel->prepare_devotional_data($payload);

                if (!$prepared_data) {
                    throw new \Exception("Prepared data is empty");
                }

                $batch_request[] = $prepared_data;

                // FastAPI URL
                $fastapi_url = env('FASTAPI_BASE_URL') ?: 'http://localhost:9000';
                $url = $fastapi_url . "/devotional/batch-create";

                $this->log('Sending batch request to FastAPI: ' . $url);

                $jsonPayload = json_encode($batch_request);

                $headers = $this->generate_secure_headers($jsonPayload);
                $headers[] = 'Content-Length: ' . strlen($jsonPayload);

                $ch = curl_init();

                curl_setopt_array($ch, [
                    CURLOPT_URL => $url,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $jsonPayload,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_TIMEOUT => 60
                ]);

                $response = curl_exec($ch);

                if ($response === false) {
                    throw new \Exception(curl_error($ch));
                }

                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                curl_close($ch);

                if ($http_code === 200) {

                    $result = json_decode($response, true);

                    if (!empty($result['success'])) {

                        $results['synced'] = $result['total_created'] ?? count($batch_request);

                        foreach ($result['results'] ?? [] as $batch_result) {

                            $results['details'][] = [
                                'id' => $batch_result['assigned_id'] ?? $batch_result['milvus_id'] ?? 'unknown',
                                'title' => $batch_result['title'] ?? 'Unknown',
                                'status' => 'synced',
                                'message' => 'Batch inserted successfully'
                            ];
                        }

                        $this->log('Batch insert successful: ' . $results['synced'] . ' items created');

                    } else {

                        $results['failed'] = count($batch_request);
                        $this->log('Batch insert failed: ' . ($result['error'] ?? 'Unknown error'), 'error');
                    }

                } else {

                    $results['failed'] = count($batch_request);
                    $this->log('HTTP error in batch insert: ' . $http_code, 'error');
                }

                print_r($results);
            }

            // Mark job completed
            $jobModel->update($job['id'], [
                'status' => 'completed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            echo "Job completed ID: " . $job['id'] . "\n";

        } catch (\Exception $e) {

            // Mark job failed
            $jobModel->update($job['id'], [
                'status' => 'failed',
                'attempts' => $job['attempts'] + 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->log('Job failed: ' . $e->getMessage(), 'error');

            echo "Job failed ID " . $job['id'] . ": " . $e->getMessage() . "\n";
        }
    }

    echo "All pending jobs processed\n";
}
}