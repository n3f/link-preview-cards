<?php
/**
 * Plugin Name: Link Preview Cards
 * Plugin URI: https://github.com/n3f/link-preview-cards
 * Description: Gutenberg block for displaying link preview cards.
 * Version: 0.1.2
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 8.2
 * Author: Brent Nef
 * Author URI: https://github.com/n3f
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: link-preview-cards
 *
 * @package LinkPreviewCards
 */

/*
Link Preview Cards is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Link Preview Cards is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Link Preview Cards. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('LINKPREVIEWCARDS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LINKPREVIEWCARDS_NONCE', 'linkpreviewcards_nonce');

/**
 * Main plugin class
 */
class LinkPreviewCards {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Register block
        register_block_type(LINKPREVIEWCARDS_PLUGIN_DIR . 'build/block.json');

        // Enqueue scripts and localize data
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_editor_assets']);
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('linkpreviewcards/v1', '/fetch', [
            'methods' => 'GET',
            'callback' => [$this, 'fetch_link_preview_data'],
            'args' => [
                'url' => [
                    'required' => true,
                    'validate_callback' => [$this, 'validate_url'],
                    'sanitize_callback' => 'esc_url_raw',
                ],
                'nonce' => [
                    'required' => true,
                    'validate_callback' => [$this, 'validate_nonce'],
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ],
            'permission_callback' => [$this, 'check_permissions'],
        ]);
    }

    /**
     * Validate URL parameter
     */
    public function validate_url($param) {
        return filter_var($param, FILTER_VALIDATE_URL);
    }

    /**
     * Check user permissions
     */
    public function check_permissions() {
        return current_user_can('edit_posts');
    }

    /**
     * Validate nonce parameter
     */
    public function validate_nonce($param) {
        return wp_verify_nonce($param, LINKPREVIEWCARDS_NONCE);
    }

    /**
     * Fetch link preview data from URL
     */
    public function fetch_link_preview_data($request) {
        $url = $request->get_param('url');

        // Additional security check
        if (!wp_http_validate_url($url)) {
            return new WP_Error(
                'invalid_url',
                __('Invalid URL provided', 'link-preview-cards'),
                ['status' => 400]
            );
        }

        $response = wp_remote_get($url, [
            'timeout' => 8,
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url')
        ]);

        if (is_wp_error($response)) {
            return new WP_Error(
                'og_fetch_failed',
                __('Failed to fetch URL', 'link-preview-cards'),
                ['status' => 400]
            );
        }

        $html = wp_remote_retrieve_body($response);

        // Extract Open Graph data
        $og = [
            'title' => '',
            'description' => '',
            'image' => ''
        ];

        // Extract og:title
        if (preg_match('/<meta property="og:title" content="([^"]+)"/i', $html, $m)) {
            $og['title'] = sanitize_text_field($m[1]);
        }

        // Extract og:description
        if (preg_match('/<meta property="og:description" content="([^"]+)"/i', $html, $m)) {
            $og['description'] = sanitize_text_field($m[1]);
        }

        // Extract og:image
        if (preg_match('/<meta property="og:image" content="([^"]+)"/i', $html, $m)) {
            $og['image'] = esc_url_raw($m[1]);
        }

        return $og;
    }

    /**
     * Enqueue editor assets and localize data
     */
    public function enqueue_editor_assets() {
        // Get all registered blocks and find our block's editor script
        $block_types = WP_Block_Type_Registry::get_instance()->get_all_registered();
        foreach ($block_types as $block_name => $block_type) {
            if ($block_name === 'link-preview-cards/card' && $block_type->editor_script) {
                wp_localize_script($block_type->editor_script, 'linkpreviewcards', [
                    'nonce' => wp_create_nonce(LINKPREVIEWCARDS_NONCE),
                ]);
                break;
            }
        }
    }
}

// Initialize the plugin
new LinkPreviewCards();

// Activation hook
register_activation_hook(__FILE__, function() {
    // Add any activation tasks here
    flush_rewrite_rules();
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Add any cleanup tasks here
    flush_rewrite_rules();
});
