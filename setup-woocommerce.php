<?php
/**
 * Setup WooCommerce Plugin
 * 
 * This script downloads and installs WooCommerce if it's not already installed
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to run this script.');
}

echo "<h1>🛒 Setting up WooCommerce</h1>";

// Check if WooCommerce is already installed
$woocommerce_plugin = 'woocommerce/woocommerce.php';

if (file_exists(WP_PLUGIN_DIR . '/' . $woocommerce_plugin)) {
    echo "<p style='color: green;'>✅ WooCommerce is already installed!</p>";
    
    // Check if it's active
    if (is_plugin_active($woocommerce_plugin)) {
        echo "<p style='color: green;'>✅ WooCommerce is already active!</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ WooCommerce is installed but not active.</p>";
        echo "<p><a href='activate-woocommerce.php' class='button'>Activate WooCommerce</a></p>";
    }
    exit;
}

echo "<p>📥 WooCommerce not found. Installing...</p>";

// Download WooCommerce
$download_url = 'https://downloads.wordpress.org/plugin/woocommerce.latest-stable.zip';
$temp_file = WP_CONTENT_DIR . '/woocommerce-temp.zip';

echo "<p>Downloading WooCommerce from WordPress.org...</p>";

$response = wp_remote_get($download_url);
if (is_wp_error($response)) {
    echo "<p style='color: red;'>❌ Failed to download WooCommerce: " . $response->get_error_message() . "</p>";
    exit;
}

$zip_content = wp_remote_retrieve_body($response);
if (empty($zip_content)) {
    echo "<p style='color: red;'>❌ Downloaded file is empty.</p>";
    exit;
}

// Save zip file
file_put_contents($temp_file, $zip_content);

echo "<p>📦 Extracting WooCommerce...</p>";

// Extract zip file
$zip = new ZipArchive();
if ($zip->open($temp_file) === TRUE) {
    $zip->extractTo(WP_PLUGIN_DIR);
    $zip->close();
    
    // Clean up temp file
    unlink($temp_file);
    
    echo "<p style='color: green;'>✅ WooCommerce installed successfully!</p>";
    
    // Try to activate
    $result = activate_plugin($woocommerce_plugin);
    if (is_wp_error($result)) {
        echo "<p style='color: orange;'>⚠️ WooCommerce installed but activation failed: " . $result->get_error_message() . "</p>";
        echo "<p><a href='activate-woocommerce.php' class='button'>Try to Activate WooCommerce</a></p>";
    } else {
        echo "<p style='color: green;'>✅ WooCommerce activated successfully!</p>";
    }
    
} else {
    echo "<p style='color: red;'>❌ Failed to extract WooCommerce zip file.</p>";
    unlink($temp_file);
    exit;
}

// Check if WooCommerce is now available
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✅ WooCommerce class is available!</p>";
    echo "<p><strong>WooCommerce Version:</strong> " . WC()->version . "</p>";
} else {
    echo "<p style='color: orange;'>⚠️ WooCommerce class is not available yet. Try refreshing the page.</p>";
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
