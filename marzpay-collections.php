<?php
/**
 * Plugin Name:       MarzPay
 * Plugin URI:        https://wearemarz.com/wordpress-plugin
 * Description:       Complete MarzPay integration for WordPress. Handle collections, withdrawals, webhooks, and transaction management with MTN and Airtel mobile money. Features include payment buttons, phone number validation, amount limits, UUID generation, configurable callback URLs, and comprehensive admin dashboard.
 * Version:           2.1.0
 * Requires at least: 5.0
 * Tested up to:     6.4
 * Requires PHP:      7.4
 * Author:            MarzPay
 * Author URI:        https://wearemarz.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       marzpay
 * Domain Path:       /languages
 * Network:           false
 * 
 * @package           MarzPay
 * @author            MarzPay
 * @copyright         2025 MarzPay
 * @license           GPL-2.0-or-later
 * 
 * MarzPay is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * MarzPay is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with MarzPay. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Define plugin constants
define( 'MARZPAY_VERSION', '2.1.0' );
define( 'MARZPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MARZPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MARZPAY_PLUGIN_FILE', __FILE__ );
define( 'MARZPAY_API_BASE_URL', 'https://wallet.wearemarz.com/api' );

// Include required files
require_once MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-api-client.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-database.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-webhooks.php';

// Include WooCommerce integration files only after plugins are loaded
add_action( 'plugins_loaded', function() {
    if ( class_exists( 'WooCommerce' ) ) {
        require_once MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-woocommerce-gateway.php';
        require_once MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-woocommerce-manager.php';
        
        // Initialize the WooCommerce manager
        MarzPay_WooCommerce_Manager::get_instance();
    }
});
require_once MARZPAY_PLUGIN_DIR . 'includes/admin-settings.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/shortcodes.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/admin-dashboard.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/functions.php';

// Add debug shortcode for testing
add_shortcode('marzpay_debug', 'marzpay_debug_shortcode');

function marzpay_debug_shortcode($atts) {
    if (!current_user_can('manage_options')) {
        return '<p>Debug information only available to administrators.</p>';
    }

    $api_user = get_option('marzpay_api_user');
    $api_key = get_option('marzpay_api_key');
    
    $output = '<div style="background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin: 20px 0;">';
    $output .= '<h3>MarzPay Debug Information</h3>';
    
    // Check API credentials
    if (empty($api_user) || empty($api_key)) {
        $output .= '<p style="color: red;">❌ API credentials not configured. Please go to Settings > MarzPay to configure.</p>';
    } else {
        $output .= '<p style="color: green;">✅ API credentials configured</p>';
        $output .= '<p><strong>API User:</strong> ' . esc_html($api_user) . '</p>';
        $output .= '<p><strong>API Key:</strong> ' . esc_html(substr($api_key, 0, 8)) . '...</p>';
    }
    
    // Check if WP_DEBUG is enabled
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $output .= '<p style="color: green;">✅ WP_DEBUG is enabled - check error logs for detailed API information</p>';
    } else {
        $output .= '<p style="color: orange;">⚠️ WP_DEBUG is disabled - enable it to see detailed API logs</p>';
    }
    
    // Test API connection
    if (!empty($api_user) && !empty($api_key)) {
        $output .= '<hr><h4>Test API Connection</h4>';
        $output .= '<button onclick="testMarzPayAPI()" style="background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;">Test API Connection</button>';
        $output .= '<div id="api-test-result" style="margin-top: 10px;"></div>';
        
        $output .= '<script>
        function testMarzPayAPI() {
            document.getElementById("api-test-result").innerHTML = "Testing...";
            
            // Create a test form submission
            var form = document.createElement("form");
            form.method = "POST";
            form.innerHTML = \'<input type="hidden" name="marzpay_phone" value="+256700000000"><input type="hidden" name="marzpay_amount" value="100">\';
            
            // Submit the form to trigger the API call
            document.body.appendChild(form);
            form.submit();
        }
        </script>';
    }
    
    $output .= '</div>';
    
    return $output;
}

/**
 * Main MarzPay Plugin Class
 */
class MarzPay_Plugin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }
    
    public function init() {
        // Load text domain
        load_plugin_textdomain( 'marzpay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        
        // Initialize components only if classes exist
        if ( class_exists( 'MarzPay_Database' ) ) {
            MarzPay_Database::get_instance();
        }
        
        if ( class_exists( 'MarzPay_Webhooks' ) ) {
            MarzPay_Webhooks::get_instance();
        }
        
        if ( class_exists( 'MarzPay_Admin_Settings' ) ) {
            MarzPay_Admin_Settings::get_instance();
        }
        
        if ( class_exists( 'MarzPay_Shortcodes' ) ) {
            MarzPay_Shortcodes::get_instance();
        }
    }
    
    public function enqueue_scripts() {
        wp_enqueue_script( 'marzpay-frontend', MARZPAY_PLUGIN_URL . 'assets/js/frontend.js', array( 'jquery' ), MARZPAY_VERSION, true );
        wp_enqueue_style( 'marzpay-frontend', MARZPAY_PLUGIN_URL . 'assets/css/frontend.css', array(), MARZPAY_VERSION );
        
        wp_localize_script( 'marzpay-frontend', 'marzpay_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'marzpay_nonce' )
        ));
    }
    
    public function admin_enqueue_scripts( $hook ) {
        if ( strpos( $hook, 'marzpay' ) !== false ) {
            wp_enqueue_script( 'marzpay-admin', MARZPAY_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), MARZPAY_VERSION, true );
            wp_enqueue_style( 'marzpay-admin', MARZPAY_PLUGIN_URL . 'assets/css/admin.css', array(), MARZPAY_VERSION );
        }
    }
}

// Initialize the plugin
function marzpay_init() {
    return MarzPay_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'marzpay_init' );

// Activation hook
function marzpay_activate() {
    // Create database tables only if class exists
    if ( class_exists( 'MarzPay_Database' ) ) {
        MarzPay_Database::create_tables();
    }
    
    // Set default options
    add_option( 'marzpay_version', MARZPAY_VERSION );
    
    // Flush rewrite rules for webhook endpoints
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'marzpay_activate' );

// Deactivation hook
function marzpay_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'marzpay_deactivate' );

// Uninstall hook
function marzpay_uninstall() {
    // Remove database tables only if class exists
    if ( class_exists( 'MarzPay_Database' ) ) {
        MarzPay_Database::drop_tables();
    }
    
    // Remove options
    delete_option( 'marzpay_version' );
    delete_option( 'marzpay_api_user' );
    delete_option( 'marzpay_api_key' );
}
register_uninstall_hook( __FILE__, 'marzpay_uninstall' );
