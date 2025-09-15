<?php
/**
 * Test Gateway Fix
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Test Gateway Fix</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

// Add product to cart
WC()->cart->empty_cart();
$products = wc_get_products(array('limit' => 1, 'status' => 'publish'));
if (!empty($products)) {
    $product = $products[0];
    WC()->cart->add_to_cart($product->get_id(), 1);
    echo "<p style='color: green;'>✅ Added product to cart</p>";
}

// Force reload payment gateways
WC()->payment_gateways()->init();

// Check available gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h2>💳 Available Payment Methods</h2>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment methods available</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " ($id)</p>";
    }
}

// Check MarzPay specifically
if (isset($available_gateways['marzpay'])) {
    echo "<p style='color: green;'>✅ MarzPay is available for checkout!</p>";
    
    $marzpay = $available_gateways['marzpay'];
    echo "<h3>MarzPay Gateway Details:</h3>";
    echo "<ul>";
    echo "<li>Title: " . esc_html($marzpay->get_title()) . "</li>";
    echo "<li>Description: " . esc_html($marzpay->get_description()) . "</li>";
    echo "<li>Enabled: " . ($marzpay->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>Available: " . ($marzpay->is_available() ? '✅ Yes' : '❌ No') . "</li>";
    echo "</ul>";
} else {
    echo "<p style='color: red;'>❌ MarzPay is not available</p>";
}

// Clear caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p style='color: green;'>✅ Cleared WordPress cache</p>";
}

echo "<h2>🎯 Test Your Checkout</h2>";
echo "<p>Now try your checkout page:</p>";
echo "<ol>";
echo "<li><strong>Go to:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Checkout Page</a></li>";
echo "<li><strong>Look for 'Mobile Money (Airtel & MTN)' in payment options</strong></li>";
echo "<li><strong>If you see it, select it and enter a test phone number</strong></li>";
echo "</ol>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test:</strong> 256700000000</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
