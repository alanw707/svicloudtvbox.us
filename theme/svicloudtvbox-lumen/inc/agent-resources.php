<?php
/** Agent-readable hardware and store-policy resources. */
if (!defined('ABSPATH')) { exit; }

if (!function_exists('svic_agent_abs_url')) {
    function svic_agent_abs_url(string $path): string {
        return home_url($path);
    }
}

if (!function_exists('svic_agent_resource_map')) {
    function svic_agent_resource_map(): array {
        require_once __DIR__ . '/public-hardware-agent-resources.php';
        return svic_15p_price_tokens_recursive(svic_public_hardware_agent_resources());
    }
}

if (!function_exists('svic_agent_resource_request_key')) {
    function svic_agent_resource_request_key(): string {
        $path = parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
        $path = is_string($path) ? trim(rawurldecode($path), '/') : '';
        if (preg_match('#^(zh|zh-cn|zh-tw)/(.+)$#i', $path, $matches)) {
            $path = $matches[2];
        }
        return $path;
    }
}

if (!function_exists('svic_serve_agent_resource')) {
    function svic_serve_agent_resource(): void {
        if (is_admin()) { return; }
        $key = svic_agent_resource_request_key();
        if ($key === '') { return; }
        $resources = svic_agent_resource_map();
        if (!isset($resources[$key])) { return; }
        $content_type = substr($key, -4) === '.txt' ? 'text/plain' : 'text/markdown';
        status_header(200);
        nocache_headers();
        header('Content-Type: ' . $content_type . '; charset=utf-8');
        header('X-Robots-Tag: index, follow');
        echo $resources[$key]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
}
add_action('parse_request', 'svic_serve_agent_resource', 0);
add_action('template_redirect', 'svic_serve_agent_resource', -1000000);
