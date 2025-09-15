<?php
/**
 * Apply the Payment Methods Fix
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Applying Payment Methods Fix</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>🔄 Reloading Plugin</h2>";

// Deactivate and reactivate the plugin
deactivate_plugins('marzpay-collections/marzpay-collections.php');
echo "<p style='color: orange;'>⚠️ Plugin deactivated</p>";

activate_plugin('marzpay-collections/marzpay-collections.php');
echo "<p style='color: green;'>✅ Plugin reactivated</p>";

echo "<h2>🛒 Setting up Cart</h2>";

// Clear and add product to cart
WC()->cart->empty_cart();
$products = wc_get_products(array('limit' => 1, 'status' => 'publish'));

if (!empty($products)) {
    $product = $products[0];
    $cart_item_key = WC()->cart->add_to_cart($product->get_id(), 1);
    
    if ($cart_item_key) {
        echo "<p style='color: green;'>✅ Added " . esc_html($product->get_name()) . " to cart</p>";
        echo "<p><strong>Cart Total:</strong> " . WC()->cart->get_total() . "</p>";
    }
}

echo "<h2>💳 Testing Payment Methods</h2>";

// Force reload payment gateways
WC()->payment_gateways()->init();

// Get available gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment methods available</p>";
} else {
    echo "<p style='color: green;'>✅ Available payment methods:</p>";
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>• " . esc_html($gateway->get_title()) . " ($id)</p>";
    }
}

// Check MarzPay specifically
$all_gateways = WC()->payment_gateways()->payment_gateways();
if (isset($all_gateways['marzpay'])) {
    $marzpay = $all_gateways['marzpay'];
    echo "<h3>🔧 MarzPay Gateway Status</h3>";
    echo "<p><strong>Enabled:</strong> " . $marzpay->get_option('enabled') . "</p>";
    echo "<p><strong>Available:</strong> " . ($marzpay->is_available() ? 'Yes' : 'No') . "</p>";
    echo "<p><strong>Title:</strong> " . $marzpay->get_title() . "</p>";
}

echo "<h2>🎯 Test Checkout Now</h2>";
echo "<p>Please try your checkout page:</p>";
echo "<ol>";
echo "<li><strong>Go to:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Checkout Page</a></li>";
echo "<li><strong>Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)</strong></li>";
echo "<li><strong>Look for payment methods</strong></li>";
echo "</ol>";

echo "<h2>🔍 Alternative Test</h2>";
echo "<p>If still not working, try this direct checkout link:</p>";
echo "<p><a href='" . get_permalink(wc_get_page_id('checkout')) . "?add-to-cart=" . $product->get_id() . "' target='_blank'>Direct Checkout with Product</a></p>";

echo "<hr>";
echo "<p><em>Fix applied at " . date('Y-m-d H:i:s') . "</em></p>";
?>
