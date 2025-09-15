<?php
/**
 * Reload MarzPay Plugin
 * 
 * This script deactivates and reactivates the MarzPay plugin to reload all files
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to run this script.');
}

echo "<h1>🔄 Reloading MarzPay Plugin</h1>";

$plugin_file = 'marzpay-collections/marzpay-collections.php';

// Check if plugin is active
if (is_plugin_active($plugin_file)) {
    echo "<p>Deactivating MarzPay plugin...</p>";
    deactivate_plugins($plugin_file);
    echo "<p style='color: orange;'>⚠️ Plugin deactivated</p>";
    
    echo "<p>Reactivating MarzPay plugin...</p>";
    $result = activate_plugin($plugin_file);
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>❌ Failed to reactivate plugin: " . $result->get_error_message() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Plugin reactivated successfully!</p>";
    }
} else {
    echo "<p>Activating MarzPay plugin...</p>";
    $result = activate_plugin($plugin_file);
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>❌ Failed to activate plugin: " . $result->get_error_message() . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Plugin activated successfully!</p>";
    }
}

// Check if WooCommerce is active
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✅ WooCommerce is active</p>";
} else {
    echo "<p style='color: red;'>❌ WooCommerce is not active</p>";
}

// Check if MarzPay API Client is available
if (class_exists('MarzPay_API_Client')) {
    echo "<p style='color: green;'>✅ MarzPay API Client is available</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay API Client is not available</p>";
}

// Check if MarzPay WooCommerce Gateway is available
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p style='color: green;'>✅ MarzPay WooCommerce Gateway is available</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay WooCommerce Gateway is not available</p>";
}

// Check if MarzPay WooCommerce Manager is available
if (class_exists('MarzPay_WooCommerce_Manager')) {
    echo "<p style='color: green;'>✅ MarzPay WooCommerce Manager is available</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay WooCommerce Manager is not available</p>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><a href='test-woocommerce-integration.php'>Test Integration</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "'>Configure MarzPay Gateway</a></li>";
echo "<li><a href='setup-test-products.php'>Create Test Products</a></li>";
echo "</ol>";

echo "<p><small>Script completed on " . date('Y-m-d H:i:s') . "</small></p>";
?>
