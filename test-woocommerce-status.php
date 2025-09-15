<?php
/**
 * Test WooCommerce Integration Status
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 MarzPay WooCommerce Integration Test</h1>";

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo "<p style='color: red;'>❌ You must be logged in as an administrator to run this test.</p>";
    echo "<p><a href='" . wp_login_url() . "'>Login here</a></p>";
    exit;
}

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
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
$all_gateways = WC()->payment_gateways()->payment_gateways();

echo "<h2>💳 Payment Gateways</h2>";

if (isset($all_gateways['marzpay'])) {
    echo "<p><strong>MarzPay Gateway:</strong> ✅ Registered</p>";
    
    $gateway = $all_gateways['marzpay'];
    echo "<p><strong>Gateway Title:</strong> " . esc_html($gateway->get_title()) . "</p>";
    echo "<p><strong>Gateway Description:</strong> " . esc_html($gateway->get_description()) . "</p>";
    echo "<p><strong>Gateway Enabled:</strong> " . ($gateway->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</p>";
    
    if ($gateway->enabled === 'yes') {
        echo "<p><strong>Gateway Available:</strong> " . ($gateway->is_available() ? '✅ Yes' : '❌ No') . "</p>";
    }
} else {
    echo "<p><strong>MarzPay Gateway:</strong> ❌ Not registered</p>";
}

echo "<h3>All Available Gateways:</h3>";
if (empty($available_gateways)) {
    echo "<p>No payment gateways available</p>";
} else {
    echo "<ul>";
    foreach ($available_gateways as $id => $gateway) {
        echo "<li>" . esc_html($gateway->get_title()) . " (" . esc_html($id) . ") - " . ($gateway->enabled === 'yes' ? 'Enabled' : 'Disabled') . "</li>";
    }
    echo "</ul>";
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
    echo "<p>Please configure your API credentials in <a href='" . admin_url('admin.php?page=marzpay-settings') . "'>MarzPay Settings</a></p>";
}

echo "<h2>🎯 Next Steps</h2>";
if (isset($all_gateways['marzpay'])) {
    if ($all_gateways['marzpay']->enabled === 'yes') {
        echo "<p>✅ <strong>Gateway is enabled!</strong> You can now:</p>";
        echo "<ol>";
        echo "<li>Create a test product</li>";
        echo "<li>Test the checkout process</li>";
        echo "<li>Verify payment processing</li>";
        echo "</ol>";
    } else {
        echo "<p>⚠️ <strong>Gateway is registered but not enabled.</strong> Please:</p>";
        echo "<ol>";
        echo "<li>Go to <a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "'>WooCommerce > Settings > Payments > MarzPay</a></li>";
        echo "<li>Enable the gateway</li>";
        echo "<li>Configure API credentials if not done already</li>";
        echo "</ol>";
    }
} else {
    echo "<p>❌ <strong>Gateway is not registered.</strong> Please check the plugin activation and WooCommerce compatibility.</p>";
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
