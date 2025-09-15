<?php
/**
 * Checkout Debug - See exactly what's happening
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔍 Checkout Debug</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>🛒 Cart Status</h2>";
$cart_count = WC()->cart->get_cart_contents_count();
echo "<p><strong>Cart Items:</strong> $cart_count</p>";

if ($cart_count == 0) {
    echo "<p style='color: orange;'>⚠️ Cart is empty - adding test product</p>";
    $products = wc_get_products(array('limit' => 1, 'status' => 'publish'));
    if (!empty($products)) {
        $product = $products[0];
        WC()->cart->add_to_cart($product->get_id(), 1);
        echo "<p style='color: green;'>✅ Added " . esc_html($product->get_name()) . " to cart</p>";
    }
}

echo "<h2>💳 Payment Gateways</h2>";

// Get all gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();
echo "<h3>All Gateways:</h3>";
foreach ($all_gateways as $id => $gateway) {
    $enabled = $gateway->get_option('enabled');
    echo "<p><strong>$id:</strong> " . esc_html($gateway->get_title()) . " (Enabled: $enabled)</p>";
}

// Get available gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
echo "<h3>Available Gateways:</h3>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No available gateways</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p style='color: green;'>✅ $id: " . esc_html($gateway->get_title()) . "</p>";
    }
}

echo "<h2>🔧 MarzPay Specific Debug</h2>";

if (isset($all_gateways['marzpay'])) {
    $marzpay = $all_gateways['marzpay'];
    echo "<p><strong>MarzPay Gateway Found:</strong> ✅</p>";
    echo "<p><strong>Enabled:</strong> " . $marzpay->get_option('enabled') . "</p>";
    echo "<p><strong>Title:</strong> " . $marzpay->get_title() . "</p>";
    echo "<p><strong>Description:</strong> " . $marzpay->get_description() . "</p>";
    
    // Test is_available method
    $is_available = $marzpay->is_available();
    echo "<p><strong>Is Available:</strong> " . ($is_available ? '✅ Yes' : '❌ No') . "</p>";
    
    // Check API credentials
    $api_key = $marzpay->get_option('api_key');
    $api_secret = $marzpay->get_option('api_secret');
    echo "<p><strong>API Key Set:</strong> " . (!empty($api_key) ? '✅ Yes' : '❌ No') . "</p>";
    echo "<p><strong>API Secret Set:</strong> " . (!empty($api_secret) ? '✅ Yes' : '❌ No') . "</p>";
    
    // Check global settings
    $global_api_key = get_option('marzpay_api_user');
    $global_api_secret = get_option('marzpay_api_key');
    echo "<p><strong>Global API Key:</strong> " . (!empty($global_api_key) ? '✅ Yes' : '❌ No') . "</p>";
    echo "<p><strong>Global API Secret:</strong> " . (!empty($global_api_secret) ? '✅ Yes' : '❌ No') . "</p>";
    
} else {
    echo "<p style='color: red;'>❌ MarzPay gateway not found</p>";
}

echo "<h2>🎯 Test Checkout Page</h2>";
echo "<p>Now visit the checkout page and see what happens:</p>";
echo "<p><a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Go to Checkout</a></p>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test:</strong> 256700000000</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Debug completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
