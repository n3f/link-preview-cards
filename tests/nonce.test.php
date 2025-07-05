<?php
/**
 * Test cases for WP Open Graph Card nonce functionality
 */

// Mock WordPress functions for testing
if (!function_exists('wp_verify_nonce')) {
    function wp_verify_nonce($nonce, $action) {
        // Simple mock - in real WP this would validate against stored nonces
        return $nonce === 'valid_nonce_' . $action;
    }
}

if (!function_exists('wp_create_nonce')) {
    function wp_create_nonce($action) {
        return 'valid_nonce_' . $action;
    }
}

if (!function_exists('current_user_can')) {
    function current_user_can($capability) {
        return true; // Mock as admin user
    }
}

if (!function_exists('sanitize_text_field')) {
    function sanitize_text_field($str) {
        // WordPress sanitize_text_field removes script content too
        $str = preg_replace('/<script[^>]*>.*?<\/script>/is', '', $str);
        return trim(strip_tags($str));
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
        // Mock successful response
        return [
            'body' => '<html><head><meta property="og:title" content="Test Title"></head></html>',
            'response' => ['code' => 200]
        ];
    }
}

if (!function_exists('is_wp_error')) {
    function is_wp_error($thing) {
        return $thing instanceof WP_Error;
    }
}

if (!function_exists('wp_remote_retrieve_body')) {
    function wp_remote_retrieve_body($response) {
        return $response['body'] ?? '';
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
 * Test nonce validation
 */
function test_nonce_validation() {
    $validator = new MockNonceValidator();

    // Test valid nonce
    $result = $validator->validate_nonce('valid_nonce_wpogc_og_nonce');
    assert($result === true, 'Valid nonce should pass validation');

    // Test invalid nonce
    $result = $validator->validate_nonce('invalid_nonce');
    assert($result === false, 'Invalid nonce should fail validation');

    // Test empty nonce
    $result = $validator->validate_nonce('');
    assert($result === false, 'Empty nonce should fail validation');

    // Test null nonce
    $result = $validator->validate_nonce(null);
    assert($result === false, 'Null nonce should fail validation');

    echo "✓ Nonce validation test passed\n";
}

/**
 * Test API endpoint with nonce
 */
function test_api_with_nonce() {
    $api = new MockAPI();

    // Test successful request with valid nonce
    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => 'valid_nonce_wpogc_og_nonce'
    ]);

    $response = $api->fetch_og_data($request);
    assert(!is_wp_error($response), 'Valid request should not return error');
    assert(isset($response['title']), 'Response should contain title');

    // Test request with invalid nonce
    $request = new MockRequest([
        'url' => 'https://example.com',
        'nonce' => 'invalid_nonce'
    ]);

    $response = $api->fetch_og_data($request);
    assert(is_wp_error($response), 'Invalid nonce should return error');
    assert($response->code === 'rest_invalid_param', 'Should return invalid param error');

    // Test request without nonce
    $request = new MockRequest([
        'url' => 'https://example.com'
    ]);

    $response = $api->fetch_og_data($request);
    assert(is_wp_error($response), 'Missing nonce should return error');
    assert($response->code === 'rest_invalid_param', 'Missing nonce should return invalid param error');

    echo "✓ API endpoint with nonce test passed\n";
}

/**
 * Test nonce creation and verification cycle
 */
function test_nonce_cycle() {
    $action = 'wpogc_og_nonce';
    $nonce = wp_create_nonce($action);

    assert($nonce === 'valid_nonce_' . $action, 'Nonce creation should work');

    $is_valid = wp_verify_nonce($nonce, $action);
    assert($is_valid === true, 'Created nonce should be valid');

    $is_invalid = wp_verify_nonce('invalid_nonce', $action);
    assert($is_invalid === false, 'Invalid nonce should fail verification');

    echo "✓ Nonce creation and verification cycle test passed\n";
}

/**
 * Test nonce with different actions
 */
function test_nonce_action_specificity() {
    $action1 = 'wpogc_og_nonce';
    $action2 = 'different_action';

    $nonce1 = wp_create_nonce($action1);
    $nonce2 = wp_create_nonce($action2);

    // Nonces should be different for different actions
    assert($nonce1 !== $nonce2, 'Nonces for different actions should be different');

    // Nonce should only be valid for its specific action
    assert(wp_verify_nonce($nonce1, $action1) === true, 'Nonce should be valid for correct action');
    assert(wp_verify_nonce($nonce1, $action2) === false, 'Nonce should be invalid for wrong action');

    echo "✓ Nonce action specificity test passed\n";
}

/**
 * Test nonce sanitization
 */
function test_nonce_sanitization() {
    $sanitizer = function($str) {
        return sanitize_text_field($str);
    };

    // Test normal nonce
    $result = $sanitizer('valid_nonce_wpogc_og_nonce');
    assert($result === 'valid_nonce_wpogc_og_nonce', 'Normal nonce should not be changed');

    // Test nonce with whitespace
    $result = $sanitizer('  valid_nonce_wpogc_og_nonce  ');
    assert($result === 'valid_nonce_wpogc_og_nonce', 'Whitespace should be trimmed');

    // Test nonce with HTML
    $result = $sanitizer('<script>alert("xss")</script>valid_nonce_wpogc_og_nonce');
    assert($result === 'valid_nonce_wpogc_og_nonce', 'HTML should be stripped');

    echo "✓ Nonce sanitization test passed\n";
}

// Mock classes for testing
class MockNonceValidator {
    public function validate_nonce($param) {
        return wp_verify_nonce($param, 'wpogc_og_nonce');
    }
}

class MockRequest {
    private $params;

    public function __construct($params = []) {
        $this->params = $params;
    }

    public function get_param($key) {
        return $this->params[$key] ?? null;
    }
}

class MockAPI {
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

        // Mock successful response
        return [
            'title' => 'Test Title',
            'description' => 'Test Description',
            'image' => 'https://example.com/image.jpg'
        ];
    }
}

// Run all tests
echo "Running nonce tests...\n";
test_nonce_validation();
test_api_with_nonce();
test_nonce_cycle();
test_nonce_action_specificity();
test_nonce_sanitization();
echo "All nonce tests passed!\n";