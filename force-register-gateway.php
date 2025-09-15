<?php
/**
 * Force Register MarzPay Gateway
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Force Register MarzPay Gateway</h1>";

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo "<p style='color: red;'>❌ You must be logged in as an administrator to run this test.</p>";
    echo "<p><a href='" . wp_login_url() . "'>Login here</a></p>";
    exit;
}

echo "<h2>📊 Current Status</h2>";

// Check if classes exist
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p><strong>MarzPay Gateway Class:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Gateway Class:</strong> ❌ Not available</p>";
    exit;
}

if (class_exists('MarzPay_WooCommerce_Manager')) {
    echo "<p><strong>MarzPay Manager Class:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Manager Class:</strong> ❌ Not available</p>";
    exit;
}

// Get current gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();
echo "<p><strong>Current Gateways:</strong> " . count($all_gateways) . "</p>";

// Force register the gateway
echo "<h2>🔧 Force Registration</h2>";

// Create a new instance of the manager
$manager = MarzPay_WooCommerce_Manager::get_instance();

// Manually call the add_gateway method
$gateways = array();
$gateways = $manager->add_gateway($gateways);

echo "<p><strong>Gateways after manual registration:</strong> " . count($gateways) . "</p>";

if (in_array('MarzPay_WooCommerce_Gateway', $gateways)) {
    echo "<p><strong>Manual Registration:</strong> ✅ Success</p>";
} else {
    echo "<p><strong>Manual Registration:</strong> ❌ Failed</p>";
}

// Try to add the gateway directly to WooCommerce
echo "<h2>🎯 Direct Registration</h2>";

// Add the gateway directly
add_filter('woocommerce_payment_gateways', function($gateways) {
    $gateways[] = 'MarzPay_WooCommerce_Gateway';
    return $gateways;
});

// Force WooCommerce to reload gateways
WC()->payment_gateways()->init();

// Check again
$all_gateways = WC()->payment_gateways()->payment_gateways();
echo "<p><strong>Gateways after direct registration:</strong> " . count($all_gateways) . "</p>";

if (isset($all_gateways['marzpay'])) {
    echo "<p><strong>Direct Registration:</strong> ✅ Success</p>";
    echo "<p><strong>Gateway Title:</strong> " . esc_html($all_gateways['marzpay']->get_title()) . "</p>";
    echo "<p><strong>Gateway Enabled:</strong> " . ($all_gateways['marzpay']->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</p>";
} else {
    echo "<p><strong>Direct Registration:</strong> ❌ Failed</p>";
}

echo "<h2>🎯 Next Steps</h2>";
if (isset($all_gateways['marzpay'])) {
    echo "<p>✅ <strong>Gateway is now registered!</strong> You can:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "'>WooCommerce > Settings > Payments > MarzPay</a></li>";
    echo "<li>Enable the gateway</li>";
    echo "<li>Configure settings</li>";
    echo "<li>Test with a product</li>";
    echo "</ol>";
} else {
    echo "<p>❌ <strong>Gateway registration failed.</strong> Please check the plugin files and WooCommerce compatibility.</p>";
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
