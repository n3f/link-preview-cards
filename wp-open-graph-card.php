<?php
/*
Plugin Name: WP Open Graph Card
Description: Gutenberg block for displaying Open Graph cards.
Version: 0.2
Author: You
*/

add_action('init', function() {
    register_block_type(__DIR__ . '/build/block.json');
});

add_action('rest_api_init', function() {
    register_rest_route('wpogc/v1', '/og', [
        'methods' => 'GET',
        'callback' => 'wpogc_fetch_og_data',
        'args' => [
            'url' => [
                'required' => true,
                'validate_callback' => function($param) {
                    return filter_var($param, FILTER_VALIDATE_URL);
                }
            ]
        ],
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        },
    ]);
});

function wpogc_fetch_og_data($request) {
    $url = esc_url_raw($request->get_param('url'));
    $response = wp_remote_get($url, ['timeout' => 8]);
    if (is_wp_error($response)) {
        return new WP_Error('og_fetch_failed', 'Failed to fetch URL', ['status' => 400]);
    }
    $html = wp_remote_retrieve_body($response);

    // Simple OG tag extraction
    $og = [
        'title' => '',
        'description' => '',
        'image' => ''
    ];
    if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
        $og['title'] = $m[1];
    }
    if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
        $og['description'] = $m[1];
    }
    if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
        $og['image'] = $m[1];
    }
    return $og;
}