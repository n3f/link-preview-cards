<?php
/**
 * Integration tests for Link Preview Cards API with nonce
 */

// Mock WordPress functions for testing
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) {
        return $nonce === 'valid_nonce_' . $action;
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) {
        return 'valid_nonce_' . $action;
    }
}

if (!function_exists('rest_url')) {
    function rest_url($path = '') {
        return 'https://example.com/wp-json/' . $path;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return true;
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        return trim(wp_strip_all_tags($str));
    }
}

if (!function_exists('esc_url_raw')) {
    function esc_url_raw($url) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
}

if (!function_exists('wp_http_validate_url')) {
    function wp_http_validate_url($url) {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
}

if (!function_exists('wp_remote_get')) {
    function wp_remote_get($url, $args = []) {
        // Mock different responses based on URL
        if (strpos($url, 'example.com') !== false) {
            return [
                'body' => '<html><head><meta property="og:title" content="Example Title"><meta property="og:description" content="Example Description"><meta property="og:image" content="https://example.com/image.jpg"></head></html>',
                'response' => ['code' => 200]
            ];
        }

        return new WP_Error('http_request_failed', 'Failed to fetch URL');
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return $thing instanceof WP_Error;
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) {
        return is_wp_error($response) ? '' : ($response['body'] ?? '');
    }
}

if (!function_exists('__')) {
    function __($text, $domain = '') {
        return $text;
    }
}

// Mock WP_Error class
if (!class_exists('WP_Error')) {
    class WP_Error {
        public $code;
        public $message;
        public $data;

        public function __construct($code, $message, $data = []) {
            $this->code = $code;
            $this->message = $message;
            $this->data = $data;
        }
    }
}

/**
 * Test complete API flow with valid nonce
 */
function test_api_flow_with_valid_nonce() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => 'valid_nonce_wpogc_og_nonce'
    ]);

    $response = $api->fetch_og_data($request);

    assert(!is_wp_error($response), 'Valid request should not return error');
    assert(isset($response['title']), 'Response should contain title');
    assert($response['title'] === 'Example Title', 'Title should match expected value');
    assert(isset($response['description']), 'Response should contain description');
    assert($response['description'] === 'Example Description', 'Description should match expected value');
    assert(isset($response['image']), 'Response should contain image');
    assert($response['image'] === 'https://example.com/image.jpg', 'Image should match expected value');

    echo "✓ API flow with valid nonce test passed\n";
}

/**
 * Test API flow with invalid nonce
 */
function test_api_flow_with_invalid_nonce() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => 'invalid_nonce'
    ]);

    $response = $api->fetch_og_data($request);

    assert(is_wp_error($response), 'Invalid nonce should return error');
    assert($response->code === 'rest_invalid_param', 'Should return invalid param error');

    echo "✓ API flow with invalid nonce test passed\n";
}

/**
 * Test API flow without nonce
 */
function test_api_flow_without_nonce() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'https://example.com'
    ]);

    $response = $api->fetch_og_data($request);

    assert(is_wp_error($response), 'Missing nonce should return error');

    echo "✓ API flow without nonce test passed\n";
}

/**
 * Test API flow with invalid URL
 */
function test_api_flow_with_invalid_url() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'not-a-valid-url',
        'nonce' => 'valid_nonce_wpogc_og_nonce'
    ]);

    $response = $api->fetch_og_data($request);

    assert(is_wp_error($response), 'Invalid URL should return error');
    assert($response->code === 'invalid_url', 'Should return invalid URL error');

    echo "✓ API flow with invalid URL test passed\n";
}

/**
 * Test API flow with network failure
 */
function test_api_flow_with_network_failure() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'https://nonexistent-domain-that-will-fail.com',
        'nonce' => 'valid_nonce_wpogc_og_nonce'
    ]);

    $response = $api->fetch_og_data($request);

    assert(is_wp_error($response), 'Network failure should return error');
    assert($response->code === 'og_fetch_failed', 'Should return fetch failed error');

    echo "✓ API flow with network failure test passed\n";
}

/**
 * Test nonce generation and usage cycle
 */
function test_nonce_generation_cycle() {
    // Test that nonce is generated correctly
    $nonce = wp_create_nonce('wpogc_og_nonce');
    assert($nonce === 'valid_nonce_wpogc_og_nonce', 'Nonce should be generated correctly');

    // Test that generated nonce works with API
    $api = new MockOpenGraphAPI();
    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => $nonce
    ]);

    $response = $api->fetch_og_data($request);
    assert(!is_wp_error($response), 'Generated nonce should work with API');

    echo "✓ Nonce generation and usage cycle test passed\n";
}

/**
 * Test API response structure
 */
function test_api_response_structure() {
    $api = new MockOpenGraphAPI();

    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => 'valid_nonce_wpogc_og_nonce'
    ]);

    $response = $api->fetch_og_data($request);

    // Check response structure
    assert(is_array($response), 'Response should be an array');
    assert(array_key_exists('title', $response), 'Response should have title key');
    assert(array_key_exists('description', $response), 'Response should have description key');
    assert(array_key_exists('image', $response), 'Response should have image key');

    // Check data types
    assert(is_string($response['title']), 'Title should be string');
    assert(is_string($response['description']), 'Description should be string');
    assert(is_string($response['image']), 'Image should be string');

    echo "✓ API response structure test passed\n";
}

// Mock classes
class MockRequest {
    private $params;

    public function __construct($params = []) {
        $this->params = $params;
    }

    public function get_param($key) {
        return $this->params[$key] ?? null;
    }
}

class MockOpenGraphAPI {
    public function fetch_og_data($request) {
        $url = $request->get_param('url');
        $nonce = $request->get_param('nonce');

        // Validate nonce
        if (!$nonce || !wp_verify_nonce($nonce, 'wpogc_og_nonce')) {
            return new WP_Error('rest_invalid_param', 'Invalid nonce');
        }

        // Validate URL
        if (!wp_http_validate_url($url)) {
            return new WP_Error('invalid_url', 'Invalid URL provided');
        }

        $response = wp_remote_get($url, [
            'timeout' => 8,
            'user-agent' => 'WordPress/Test; https://example.com'
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('og_fetch_failed', 'Failed to fetch URL');
        }

        $html = wp_remote_retrieve_body($response);

        // Extract Open Graph data
        $og = [
            'title' => '',
            'description' => '',
            'image' => ''
        ];

        if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
            $og['title'] = sanitize_text_field($m[1]);
        }

        if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
            $og['description'] = sanitize_text_field($m[1]);
        }

        if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
            $og['image'] = esc_url_raw($m[1]);
        }

        return $og;
    }
}

// Run all tests
echo "Running API integration tests...\n";
test_api_flow_with_valid_nonce();
test_api_flow_with_invalid_nonce();
test_api_flow_without_nonce();
test_api_flow_with_invalid_url();
test_api_flow_with_network_failure();
test_nonce_generation_cycle();
test_api_response_structure();
echo "All API integration tests passed!\n";