<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\McpModel;

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
        $id     = $body['id'] ?? null;

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

    private function handleToolsCall($params, $id)
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

            default:
                return $this->sendError(-32602, 'Unknown tool', $id);
        }

        return $this->sendResult([
            'content' => [
                [
                    'type' => 'text',
                    'text' => json_encode($result, JSON_PRETTY_PRINT)
                ]
            ]
        ], $id);
    }

    // =========================================================
    // TOOLS
    // =========================================================
    private function toolListContent($input)
    {
        $limit  = min((int)($input['limit'] ?? 10), 100);
        $offset = (int)($input['offset'] ?? 0);

        return $this->mcpModel->listContent($limit, $offset);
    }

    private function toolGetContent($input)
    {
        if (empty($input['id'])) {
            return ['error' => 'ID required'];
        }

        $data = $this->mcpModel->getContentById((int)$input['id']);

        return $data ?: ['error' => 'Not found'];
    }

    private function toolSearchContent($input)
    {
        if (empty($input['query'])) {
            return ['error' => 'Query required'];
        }

        return $this->mcpModel->searchContent(
            $input['query'],
            min((int)($input['limit'] ?? 10), 50)
        );
    }

    private function toolUpdateContent($input)
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
            $data['status'] = esc($input['status']);
        }

        if (empty($data)) {
            return ['error' => 'No data to update'];
        }

        $success = $this->mcpModel->updateContent($input['id'], $data);

        return $success
            ? ['success' => true]
            : ['error' => 'Update failed'];
    }

    // =========================================================
    // TOOL DEFINITIONS
    // =========================================================
    private function getToolDefinitions()
    {
        return [
            [
                'name' => 'list_content',
                'description' => 'List content',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'limit' => ['type' => 'integer'],
                        'offset' => ['type' => 'integer'],
                    ],
                ],
            ],
            [
                'name' => 'get_content',
                'description' => 'Get content by ID',
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['id'],
                    'properties' => [
                        'id' => ['type' => 'integer']
                    ],
                ],
            ],
            [
                'name' => 'search_content',
                'description' => 'Search content',
                'inputSchema' => [
                    'type' => 'object',
                    'required' => ['query'],
                    'properties' => [
                        'query' => ['type' => 'string']
                    ],
                ],
            ],
            [
                'name' => 'list_tags',
                'description' => 'List tags',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => new \stdClass()
                ],
            ],
        ];
    }

    // =========================================================
    // AUTH
    // =========================================================
    private function authenticate()
    {
        $expected = env('mcp.apiKey');

        if (!$expected) return false;

        $header = $this->request->getHeaderLine('Authorization');

        if (!$header) return false;

        $key = str_replace('Bearer ', '', $header);

        return hash_equals($expected, trim($key));
    }

    // =========================================================
    // RESPONSE HELPERS
    // =========================================================
    private function sendResult($result, $id)
    {
        return $this->response->setJSON([
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result
        ]);
    }

    private function sendError($code, $message, $id)
    {
        return $this->response->setJSON([
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message
            ]
        ]);
    }
}