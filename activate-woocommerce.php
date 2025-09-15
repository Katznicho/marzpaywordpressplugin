<?php
/**
 * Activate WooCommerce Plugin
 * 
 * This script activates WooCommerce if it's installed but not active
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to run this script.');
}

echo "<h1>🛒 Activating WooCommerce</h1>";

// Check if WooCommerce is installed
$woocommerce_plugin = 'woocommerce/woocommerce.php';

if (!file_exists(WP_PLUGIN_DIR . '/' . $woocommerce_plugin)) {
    echo "<p style='color: red;'>❌ WooCommerce plugin not found. Please install it first.</p>";
    echo "<p><a href='setup-woocommerce.php' class='button'>Install WooCommerce</a></p>";
    exit;
}

// Check if WooCommerce is already active
if (is_plugin_active($woocommerce_plugin)) {
    echo "<p style='color: green;'>✅ WooCommerce is already active!</p>";
} else {
    // Activate WooCommerce
    $result = activate_plugin($woocommerce_plugin);
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>❌ Failed to activate WooCommerce: " . $result->get_error_message() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ WooCommerce activated successfully!</p>";
    }
}

// Check if WooCommerce is now active
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✅ WooCommerce class is available!</p>";
    echo "<p><strong>WooCommerce Version:</strong> " . WC()->version . "</p>";
} else {
    echo "<p style='color: red;'>❌ WooCommerce class is not available.</p>";
}

// Check if MarzPay WooCommerce Gateway is available
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p style='color: green;'>✅ MarzPay WooCommerce Gateway is available!</p>";
} else {
    echo "<p style='color: orange;'>⚠️ MarzPay WooCommerce Gateway is not available. This is normal if WooCommerce was just activated.</p>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><a href='" . admin_url('admin.php?page=wc-admin') . "'>Complete WooCommerce Setup</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "'>Configure MarzPay Gateway</a></li>";
echo "<li><a href='test-woocommerce-integration.php'>Test Integration</a></li>";
echo "<li><a href='setup-test-products.php'>Create Test Products</a></li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔗 Useful Links</h2>";
echo "<ul>";
echo "<li><a href='" . admin_url('plugins.php') . "'>Manage Plugins</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=wc-settings') . "'>WooCommerce Settings</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=marzpay-dashboard') . "'>MarzPay Dashboard</a></li>";
echo "<li><a href='" . home_url('/shop') . "'>Shop Page</a></li>";
echo "</ul>";

echo "<p><small>Script completed on " . date('Y-m-d H:i:s') . "</small></p>";
?>
