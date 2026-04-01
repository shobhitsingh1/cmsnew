#!/usr/bin/env php
<?php
/**
 * Manus MCP Connector - Endpoint Test Script
 *
 * Run this script from the command line to test your MCP endpoint
 * before connecting it to Manus.
 *
 * Usage:
 *   php test_mcp_endpoint.php <endpoint_url> <api_key>
 *
 * Example:
 *   php test_mcp_endpoint.php https://your-cms.com/api/mcp your-secret-api-key
 */

if ($argc < 3) {
    echo "Usage: php test_mcp_endpoint.php <endpoint_url> <api_key>\n";
    echo "Example: php test_mcp_endpoint.php https://your-cms.com/api/mcp your-secret-key\n";
    exit(1);
}

$endpoint = rtrim($argv[1], '/');
$api_key  = $argv[2];

$tests = [
    [
        'name'    => 'Initialize Handshake',
        'payload' => ['jsonrpc' => '2.0', 'id' => 1, 'method' => 'initialize', 'params' => []],
    ],
    [
        'name'    => 'List Tools',
        'payload' => ['jsonrpc' => '2.0', 'id' => 2, 'method' => 'tools/list', 'params' => []],
    ],
    [
        'name'    => 'List Content (first 5 items)',
        'payload' => [
            'jsonrpc' => '2.0',
            'id'      => 3,
            'method'  => 'tools/call',
            'params'  => ['name' => 'list_content', 'arguments' => ['limit' => 5, 'offset' => 0]],
        ],
    ],
    [
        'name'    => 'List tags',
        'payload' => [
            'jsonrpc' => '2.0',
            'id'      => 4,
            'method'  => 'tools/call',
            'params'  => ['name' => 'list_tags', 'arguments' => []],
        ],
    ],
    [
        'name'    => 'Search Content (keyword: "test")',
        'payload' => [
            'jsonrpc' => '2.0',
            'id'      => 5,
            'method'  => 'tools/call',
            'params'  => ['name' => 'search_content', 'arguments' => ['query' => 'test', 'limit' => 3]],
        ],
    ],
];

echo "\n=================================================================\n";
echo " Manus MCP Connector — Endpoint Test\n";
echo " Endpoint: {$endpoint}\n";
echo "=================================================================\n\n";

$pass = 0;
$fail = 0;

foreach ($tests as $test) {
    echo "Testing: {$test['name']}...\n";

    $response = _call_mcp($endpoint, $api_key, $test['payload']);

    if ($response === false) {
        echo "  FAIL: Could not connect to endpoint.\n\n";
        $fail++;
        continue;
    }

    $data = json_decode($response, true);

    // print_r($data);exit;

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "  FAIL: Response is not valid JSON.\n";
        echo "  Raw response: " . substr($response, 0, 200) . "\n\n";
        $fail++;
        continue;
    }


    if (isset($data['error'])) {
        echo "  FAIL: MCP error {$data['error']['code']}: {$data['error']['message']}\n\n";
        $fail++;
        continue;
    }

    echo "  PASS\n";
    echo "  Response preview: " . substr(json_encode($data['result']), 0, 150) . "...\n\n";
    $pass++;
}

echo "=================================================================\n";
echo " Results: {$pass} passed, {$fail} failed\n";
echo "=================================================================\n\n";

if ($fail === 0) {
    echo "All tests passed! Your MCP endpoint is ready to connect to Manus.\n\n";
    echo "Next step: In Manus, go to Settings → Integrations → Custom MCP Servers\n";
    echo "  Server Name: CMS MCP Connector\n";
    echo "  Server URL:  {$endpoint}\n";
    echo "  Auth Header: Authorization: Bearer {$api_key}\n\n";
} else {
    echo "Some tests failed. Please check your configuration and try again.\n\n";
}

// -------------------------------------------------------------------------

function _call_mcp($endpoint, $api_key, $payload)
{
    $ch = curl_init($endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key,
        ],
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $error    = curl_error($ch);
    curl_close($ch);

    if ($error) {
        echo "  cURL error: {$error}\n";
        return false;
    }

    return $response;
}
