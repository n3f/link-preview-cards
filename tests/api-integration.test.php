<?php
/**
 * Integration tests for Link Preview Cards API
 * Run with: wp-env run tests-cli bash -c "php wp-content/plugins/link-preview-cards/tests/api-integration.test.php"
 */

// Prevent direct access (except for CLI test execution)
if ( ! defined( 'ABSPATH' ) && php_sapi_name() !== 'cli' ) exit;

// Load WordPress
require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/wp-load.php';

// Load the plugin
require_once dirname(__DIR__) . '/link-preview-cards.php';

// Set up a user with edit_posts capability for testing
function setup_test_user() {
    // Create or get a test user with editor role
    $user = get_user_by('login', 'test_editor');
    if (!$user) {
        $user_id = wp_create_user('test_editor', 'password123', 'test@example.com');
        $user = get_user_by('ID', $user_id);
        $user->set_role('editor');
    }

    // Log in as this user
    wp_set_current_user($user->ID);
    return $user;
}

// Set up test user
setup_test_user();

/**
 * Test complete API flow with valid nonce
 */
function linkpreviewcards_test_api_flow_with_valid_nonce() {
    // Create a valid nonce
    $nonce = wp_create_nonce(LINKPREVIEWCARDS_NONCE);

    // Test the actual REST API endpoint
    $request = new WP_REST_Request('GET', '/linkpreviewcards/v1/fetch');
    $request->set_param('url', 'https://example.com');
    $request->set_param('nonce', $nonce);

        $response = rest_do_request($request);

    // The response should be successful
    assert($response->status === 200, 'Valid request should return 200 status');

    $data = $response->get_data();
    assert(isset($data['title']), 'Response should contain title');
    assert(isset($data['description']), 'Response should contain description');
    assert(isset($data['image']), 'Response should contain image');

    echo "✓ API flow with valid nonce test passed\n";
}

/**
 * Test API flow with invalid nonce
 */
function linkpreviewcards_test_api_flow_with_invalid_nonce() {
    $request = new WP_REST_Request('GET', '/linkpreviewcards/v1/fetch');
    $request->set_param('url', 'https://example.com');
    $request->set_param('nonce', 'invalid_nonce');

    $response = rest_do_request($request);

    // Should return 400 for invalid nonce
    assert($response->status === 400, 'Invalid nonce should return 400 status');

    echo "✓ API flow with invalid nonce test passed\n";
}

/**
 * Test API flow without nonce
 */
function linkpreviewcards_test_api_flow_without_nonce() {
    $request = new WP_REST_Request('GET', '/linkpreviewcards/v1/fetch');
    $request->set_param('url', 'https://example.com');

    $response = rest_do_request($request);

    // Should return 400 for missing nonce
    assert($response->status === 400, 'Missing nonce should return 400 status');

    echo "✓ API flow without nonce test passed\n";
}

/**
 * Test API flow with invalid URL
 */
function linkpreviewcards_test_api_flow_with_invalid_url() {
    $nonce = wp_create_nonce(LINKPREVIEWCARDS_NONCE);

    $request = new WP_REST_Request('GET', '/linkpreviewcards/v1/fetch');
    $request->set_param('url', 'not-a-valid-url');
    $request->set_param('nonce', $nonce);

    $response = rest_do_request($request);

    // Should return 400 for invalid URL
    assert($response->status === 400, 'Invalid URL should return 400 status');

    echo "✓ API flow with invalid URL test passed\n";
}

/**
 * Test API response structure
 */
function linkpreviewcards_test_api_response_structure() {
    $nonce = wp_create_nonce(LINKPREVIEWCARDS_NONCE);

    $request = new WP_REST_Request('GET', '/linkpreviewcards/v1/fetch');
    $request->set_param('url', 'https://example.com');
    $request->set_param('nonce', $nonce);

    $response = rest_do_request($request);
    $data = $response->get_data();

    // Check response structure
    assert(is_array($data), 'Response should be an array');
    assert(array_key_exists('title', $data), 'Response should have title key');
    assert(array_key_exists('description', $data), 'Response should have description key');
    assert(array_key_exists('image', $data), 'Response should have image key');

    // Check data types
    assert(is_string($data['title']), 'Title should be string');
    assert(is_string($data['description']), 'Description should be string');
    assert(is_string($data['image']), 'Image should be string');

    echo "✓ API response structure test passed\n";
}

// Run all tests
echo "Running API integration tests...\n";
linkpreviewcards_test_api_flow_with_valid_nonce();
linkpreviewcards_test_api_flow_with_invalid_nonce();
linkpreviewcards_test_api_flow_without_nonce();
linkpreviewcards_test_api_flow_with_invalid_url();
linkpreviewcards_test_api_response_structure();
echo "All API integration tests passed!\n";