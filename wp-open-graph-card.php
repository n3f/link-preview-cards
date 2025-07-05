<?php
/**
 * Plugin Name: WP Open Graph Card
 * Plugin URI: https://github.com/n3f/wp-open-graph-card
 * Description: Gutenberg block for displaying Open Graph cards.
 * Version: 0.1.0
 * Requires at least: 5.0
 * Tested up to: 6.8.1
 * Requires PHP: 8.2
 * Author: Brent Nef
 * Author URI: https://github.com/n3f
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-open-graph-card
 * Domain Path: /languages
 * Network: false
 *
 * @package WPOpenGraphCard
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WPOG_CARD_VERSION', '0.1.0');
define('WPOG_CARD_PLUGIN_FILE', __FILE__);
define('WPOG_CARD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WPOG_CARD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WPOG_CARD_TEXT_DOMAIN', 'wp-open-graph-card');

/**
 * Main plugin class
 */
class WP_Open_Graph_Card {

    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('rest_api_init', [$this, 'register_rest_routes']);
        add_action('plugins_loaded', [$this, 'load_textdomain']);
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Register block
        register_block_type(WPOG_CARD_PLUGIN_DIR . 'build/block.json');
    }

    /**
     * Load text domain for internationalization
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            WPOG_CARD_TEXT_DOMAIN,
            false,
            dirname(plugin_basename(WPOG_CARD_PLUGIN_FILE)) . '/languages'
        );
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('wpogc/v1', '/og', [
            'methods' => 'GET',
            'callback' => [$this, 'fetch_og_data'],
            'args' => [
                'url' => [
                    'required' => true,
                    'validate_callback' => [$this, 'validate_url'],
                    'sanitize_callback' => 'esc_url_raw',
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
     * Fetch Open Graph data from URL
     */
    public function fetch_og_data($request) {
        $url = $request->get_param('url');

        // Additional security check
        if (!wp_http_validate_url($url)) {
            return new WP_Error(
                'invalid_url',
                __('Invalid URL provided', WPOG_CARD_TEXT_DOMAIN),
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
                __('Failed to fetch URL', WPOG_CARD_TEXT_DOMAIN),
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
}

// Initialize the plugin
new WP_Open_Graph_Card();

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