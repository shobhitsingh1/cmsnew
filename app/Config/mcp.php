<?php
/**
 * Manus MCP Connector - Configuration File
 *
 * Place this file in: application/config/mcp.php
 *
 * ============================================================================
 * SETUP INSTRUCTIONS
 * ============================================================================
 *
 * 1. Generate a strong, random API key (at least 32 characters).
 *    You can generate one using: php -r "echo bin2hex(random_bytes(32));"
 *
 * 2. Set the API key below in $config['mcp_api_key'].
 *
 * 3. Copy this key — you will need to provide it to Manus when adding
 *    the custom MCP server in Settings → Integrations → Custom MCP Servers.
 *
 * ============================================================================
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| MCP API Key
|--------------------------------------------------------------------------
|
| This is the secret key that Manus will use to authenticate with your
| MCP endpoint. Keep this value secret and never commit it to version control.
|
| To generate a key, run in terminal:
|   php -r "echo bin2hex(random_bytes(32));"
|
*/
$config['mcp_api_key'] = 'e4ac762fba5ab9d50f29367927e77da34857c827297378599cb39634f8083065';

/*
|--------------------------------------------------------------------------
| Allow Write Operations
|--------------------------------------------------------------------------
|
| Set to TRUE to allow Manus to update CMS content via the update_content
| tool. Set to FALSE (default) to make the connector read-only.
|
| It is strongly recommended to start with FALSE and only enable writes
| after you have tested the read-only connection thoroughly.
|
*/
$config['mcp_allow_write'] = TRUE;

/*
|--------------------------------------------------------------------------
| Allowed IP Addresses (Optional)
|--------------------------------------------------------------------------
|
| If you want to restrict access to specific IP addresses (e.g., Manus
| servers), list them here. Leave as an empty array to allow all IPs
| (authentication via API key is still enforced).
|
| Example: $config['mcp_allowed_ips'] = ['1.2.3.4', '5.6.7.8'];
|
*/
$config['mcp_allowed_ips'] = [];
