<?php
/**
 * Debug Checkout Payment Methods
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔍 Debug Checkout Payment Methods</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>📊 Payment Gateway Status</h2>";

// Get all payment gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h3>All Registered Gateways:</h3>";
foreach ($all_gateways as $id => $gateway) {
    echo "<p><strong>" . esc_html($gateway->get_title()) . " (" . esc_html($id) . "):</strong></p>";
    echo "<ul>";
    echo "<li>Enabled: " . ($gateway->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>Available: " . ($gateway->is_available() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>In Available List: " . (isset($available_gateways[$id]) ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
}

echo "<h3>Available Gateways for Checkout:</h3>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment gateways available for checkout</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " (" . esc_html($id) . ")</p>";
    }
}

// Check MarzPay specifically
echo "<h2>🔍 MarzPay Gateway Debug</h2>";

if (isset($all_gateways['marzpay'])) {
    $marzpay = $all_gateways['marzpay'];
    
    echo "<h3>MarzPay Configuration:</h3>";
    echo "<ul>";
    echo "<li>Enabled: " . ($marzpay->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>API Key: " . (!empty($marzpay->api_key) ? '✅ Set (' . substr($marzpay->api_key, 0, 8) . '...)' : '❌ Not set') . "</li>";
    echo "<li>API Secret: " . (!empty($marzpay->api_secret) ? '✅ Set' : '❌ Not set') . "</li>";
    echo "<li>Test Mode: " . ($marzpay->testmode === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
    
    echo "<h3>is_available() Check:</h3>";
    $is_available = $marzpay->is_available();
    echo "<p>Result: " . ($is_available ? '✅ Available' : '❌ Not Available') . "</p>";
    
    if (!$is_available) {
        echo "<h3>Why MarzPay is not available:</h3>";
        echo "<ul>";
        if ($marzpay->enabled !== 'yes') {
            echo "<li style='color: red;'>❌ Gateway is not enabled</li>";
        }
        if (empty($marzpay->api_key)) {
            echo "<li style='color: red;'>❌ API Key is empty</li>";
        }
        if (empty($marzpay->api_secret)) {
            echo "<li style='color: red;'>❌ API Secret is empty</li>";
        }
        echo "</ul>";
    }
    
} else {
    echo "<p style='color: red;'>❌ MarzPay gateway not found in registered gateways</p>";
}

// Check global MarzPay settings
echo "<h2>🔑 Global MarzPay Settings</h2>";
$global_api_key = get_option('marzpay_api_user');
$global_api_secret = get_option('marzpay_api_key');

echo "<ul>";
echo "<li>Global API Key: " . (!empty($global_api_key) ? '✅ Set (' . substr($global_api_key, 0, 8) . '...)' : '❌ Not set') . "</li>";
echo "<li>Global API Secret: " . (!empty($global_api_secret) ? '✅ Set' : '❌ Not set') . "</li>";
echo "</ul>";

// Force sync settings
echo "<h2>🔧 Force Sync Settings</h2>";

if (isset($all_gateways['marzpay'])) {
    $marzpay = $all_gateways['marzpay'];
    
    // Sync global settings to gateway
    if (!empty($global_api_key) && !empty($global_api_secret)) {
        $marzpay->update_option('api_key', $global_api_key);
        $marzpay->update_option('api_secret', $global_api_secret);
        echo "<p style='color: green;'>✅ Synced global API credentials to gateway</p>";
    }
    
    // Force enable if not enabled
    if ($marzpay->enabled !== 'yes') {
        $marzpay->update_option('enabled', 'yes');
        echo "<p style='color: green;'>✅ Force enabled MarzPay gateway</p>";
    }
    
    // Force WooCommerce to reload
    WC()->payment_gateways()->init();
    
    // Check again
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    
    echo "<h3>After Force Sync:</h3>";
    if (isset($available_gateways['marzpay'])) {
        echo "<p style='color: green;'>✅ MarzPay is now available for checkout!</p>";
    } else {
        echo "<p style='color: red;'>❌ MarzPay is still not available</p>";
        
        // Check the is_available method again
        $is_available = $marzpay->is_available();
        echo "<p>is_available() result: " . ($is_available ? '✅ Available' : '❌ Not Available') . "</p>";
    }
}

echo "<h2>🎯 Test Checkout Again</h2>";
echo "<p>Now try your checkout:</p>";
echo "<ol>";
echo "<li><strong>Go to checkout:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Visit Checkout</a></li>";
echo "<li><strong>Look for payment methods</strong></li>";
echo "<li><strong>If still not working, try adding a product to cart first</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Debug completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
