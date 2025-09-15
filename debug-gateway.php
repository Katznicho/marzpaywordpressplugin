<?php
/**
 * Debug MarzPay WooCommerce Gateway
 * 
 * This script helps debug why the gateway class is not available
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to run this script.');
}

echo "<h1>🔍 Debug MarzPay WooCommerce Gateway</h1>";

echo "<h2>📋 System Check</h2>";

// Check WordPress
echo "<p><strong>WordPress Version:</strong> " . get_bloginfo('version') . "</p>";

// Check WooCommerce
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✅ WooCommerce is available (Version: " . WC()->version . ")</p>";
} else {
    echo "<p style='color: red;'>❌ WooCommerce is not available</p>";
}

// Check WC_Payment_Gateway
if (class_exists('WC_Payment_Gateway')) {
    echo "<p style='color: green;'>✅ WC_Payment_Gateway is available</p>";
} else {
    echo "<p style='color: red;'>❌ WC_Payment_Gateway is not available</p>";
}

// Check MarzPay API Client
if (class_exists('MarzPay_API_Client')) {
    echo "<p style='color: green;'>✅ MarzPay_API_Client is available</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay_API_Client is not available</p>";
}

// Check if gateway file exists
$gateway_file = MARZPAY_PLUGIN_DIR . 'includes/class-marzpay-woocommerce-gateway.php';
if (file_exists($gateway_file)) {
    echo "<p style='color: green;'>✅ Gateway file exists</p>";
} else {
    echo "<p style='color: red;'>❌ Gateway file does not exist</p>";
}

// Try to include the gateway file manually
echo "<h2>🔧 Manual File Inclusion</h2>";

try {
    if (file_exists($gateway_file)) {
        include_once $gateway_file;
        echo "<p style='color: green;'>✅ Gateway file included successfully</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error including gateway file: " . $e->getMessage() . "</p>";
}

// Check if gateway class is now available
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p style='color: green;'>✅ MarzPay_WooCommerce_Gateway class is now available</p>";
    
    // Try to instantiate the gateway
    try {
        $gateway = new MarzPay_WooCommerce_Gateway();
        echo "<p style='color: green;'>✅ Gateway instance created successfully</p>";
        echo "<p><strong>Gateway ID:</strong> " . $gateway->id . "</p>";
        echo "<p><strong>Gateway Title:</strong> " . $gateway->method_title . "</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Error creating gateway instance: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ MarzPay_WooCommerce_Gateway class is still not available</p>";
}

// Check WooCommerce gateways
echo "<h2>🛒 WooCommerce Gateways</h2>";

if (class_exists('WooCommerce')) {
    $gateways = WC()->payment_gateways()->payment_gateways();
    echo "<p><strong>Available Gateways:</strong></p>";
    echo "<ul>";
    foreach ($gateways as $id => $gateway) {
        $class = get_class($gateway);
        echo "<li><strong>{$id}:</strong> {$class}</li>";
    }
    echo "</ul>";
    
    if (isset($gateways['marzpay'])) {
        echo "<p style='color: green;'>✅ MarzPay gateway is registered in WooCommerce</p>";
    } else {
        echo "<p style='color: red;'>❌ MarzPay gateway is not registered in WooCommerce</p>";
    }
}

// Check plugin loading order
echo "<h2>📦 Plugin Loading</h2>";

$active_plugins = get_option('active_plugins');
echo "<p><strong>Active Plugins:</strong></p>";
echo "<ul>";
foreach ($active_plugins as $plugin) {
    $status = '';
    if (strpos($plugin, 'woocommerce') !== false) {
        $status = ' (WooCommerce)';
    } elseif (strpos($plugin, 'marzpay') !== false) {
        $status = ' (MarzPay)';
    }
    echo "<li>{$plugin}{$status}</li>";
}
echo "</ul>";

// Check for PHP errors
echo "<h2>🐛 PHP Errors</h2>";

$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $errors = file_get_contents($error_log);
    $recent_errors = array_slice(explode("\n", $errors), -10);
    echo "<p><strong>Recent PHP Errors:</strong></p>";
    echo "<pre>" . implode("\n", $recent_errors) . "</pre>";
} else {
    echo "<p>No PHP error log found or accessible</p>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li><a href='reload-plugin.php'>Reload Plugin</a></li>";
echo "<li><a href='test-woocommerce-integration.php'>Test Integration</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=wc-settings&tab=checkout') . "'>Check WooCommerce Payment Settings</a></li>";
echo "</ol>";

echo "<p><small>Debug completed on " . date('Y-m-d H:i:s') . "</small></p>";
?>
