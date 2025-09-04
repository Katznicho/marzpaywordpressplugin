<?php
/**
 * Plugin Name:       MarzPay Collections
 * Plugin URI:        https://wearemarz.com/wordpress-plugin
 * Description:       Accept mobile money payments via MarzPay Collections API in WordPress. Features include payment buttons, phone number validation, amount limits, UUID generation, and configurable callback URLs. Perfect for businesses in Uganda accepting mobile payments.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Tested up to:     6.4
 * Requires PHP:      7.4
 * Author:            MarzPay
 * Author URI:        https://wearemarz.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       marzpay-collections
 * Domain Path:       /languages
 * Network:           false
 * 
 * @package           MarzPayCollections
 * @author            MarzPay
 * @copyright         2025 MarzPay
 * @license           GPL-2.0-or-later
 * 
 * MarzPay Collections is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * 
 * MarzPay Collections is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with MarzPay Collections. If not, see https://www.gnu.org/licenses/gpl-2.0.html.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'MARZPAY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MARZPAY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Includes
require_once MARZPAY_PLUGIN_DIR . 'includes/admin-settings.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/api-client.php';
require_once MARZPAY_PLUGIN_DIR . 'includes/shortcodes.php';

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

// Activation hook
function marzpay_activate() {
    // Maybe create DB tables or default options later
}
register_activation_hook( __FILE__, 'marzpay_activate' );

// Deactivation hook
function marzpay_deactivate() {
    // Cleanup or disable CRON jobs if any
}
register_deactivation_hook( __FILE__, 'marzpay_deactivate' );
