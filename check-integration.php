<?php
/**
 * Simple WooCommerce Integration Check
 * No authentication required
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 MarzPay WooCommerce Integration Check</h1>";

echo "<h2>📊 System Status</h2>";

// Check WordPress
echo "<p><strong>WordPress:</strong> ✅ Active (Version " . get_bloginfo('version') . ")</p>";

// Check WooCommerce
if (class_exists('WooCommerce')) {
    $wc = WC();
    echo "<p><strong>WooCommerce:</strong> ✅ Active (Version " . WC()->version . ")</p>";
} else {
    echo "<p><strong>WooCommerce:</strong> ❌ Not active</p>";
    exit;
}

// Check MarzPay Plugin
if (class_exists('MarzPay_Plugin')) {
    echo "<p><strong>MarzPay Plugin:</strong> ✅ Active</p>";
} else {
    echo "<p><strong>MarzPay Plugin:</strong> ❌ Not active</p>";
}

// Check MarzPay Gateway
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p><strong>MarzPay Gateway:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Gateway:</strong> ❌ Not available</p>";
}

// Check MarzPay Manager
if (class_exists('MarzPay_WooCommerce_Manager')) {
    echo "<p><strong>MarzPay Manager:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Manager:</strong> ❌ Not available</p>";
}

// Check if gateway is registered
$all_gateways = WC()->payment_gateways()->payment_gateways();

echo "<h2>💳 Payment Gateways</h2>";

if (isset($all_gateways['marzpay'])) {
    echo "<p><strong>MarzPay Gateway:</strong> ✅ Registered</p>";
    
    $gateway = $all_gateways['marzpay'];
    echo "<p><strong>Gateway Title:</strong> " . esc_html($gateway->get_title()) . "</p>";
    echo "<p><strong>Gateway Enabled:</strong> " . ($gateway->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</p>";
} else {
    echo "<p><strong>MarzPay Gateway:</strong> ❌ Not registered</p>";
}

echo "<h3>All Registered Gateways:</h3>";
if (empty($all_gateways)) {
    echo "<p>No payment gateways registered</p>";
} else {
    echo "<ul>";
    foreach ($all_gateways as $id => $gateway) {
        echo "<li>" . esc_html($gateway->get_title()) . " (" . esc_html($id) . ") - " . ($gateway->enabled === 'yes' ? 'Enabled' : 'Disabled') . "</li>";
    }
    echo "</ul>";
}

// Check API credentials
echo "<h2>🔑 API Credentials</h2>";
$api_key = get_option('marzpay_api_user');
$api_secret = get_option('marzpay_api_key');

if (!empty($api_key) && !empty($api_secret)) {
    echo "<p><strong>API Credentials:</strong> ✅ Configured</p>";
    echo "<p><strong>API Key:</strong> " . esc_html(substr($api_key, 0, 8)) . "...</p>";
} else {
    echo "<p><strong>API Credentials:</strong> ❌ Not configured</p>";
}

echo "<h2>🎯 Manual Testing Steps</h2>";
echo "<ol>";
echo "<li><strong>Login to WordPress Admin:</strong> <a href='" . admin_url() . "' target='_blank'>" . admin_url() . "</a></li>";
echo "<li><strong>Go to WooCommerce Settings:</strong> <a href='" . admin_url('admin.php?page=wc-settings&tab=checkout') . "' target='_blank'>WooCommerce > Settings > Payments</a></li>";
echo "<li><strong>Look for 'MarzPay Mobile Money'</strong> in the payment methods list</li>";
echo "<li><strong>Click 'Set up'</strong> next to MarzPay Mobile Money</li>";
echo "<li><strong>Enable the gateway</strong> and configure settings</li>";
echo "<li><strong>Save changes</strong></li>";
echo "</ol>";

echo "<h2>🧪 Test Product Creation</h2>";
echo "<p>After enabling the gateway, you can:</p>";
echo "<ol>";
echo "<li><strong>Create a test product:</strong> <a href='" . admin_url('post-new.php?post_type=product') . "' target='_blank'>Add New Product</a></li>";
echo "<li><strong>Set a simple price</strong> (e.g., 1000 UGX)</li>";
echo "<li><strong>Publish the product</strong></li>";
echo "<li><strong>Test checkout:</strong> Visit your shop and try to purchase the product</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Check completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
