<?php
/**
 * Test Checkout with Cart Items
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🛒 Test Checkout with Cart Items</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

// Clear any existing cart
WC()->cart->empty_cart();

// Get a test product
$products = wc_get_products(array(
    'limit' => 1,
    'status' => 'publish'
));

if (empty($products)) {
    echo "<p style='color: red;'>❌ No products found. Please create a product first.</p>";
    exit;
}

$product = $products[0];
echo "<p>Using test product: <strong>" . esc_html($product->get_name()) . "</strong> - " . $product->get_price() . " UGX</p>";

// Add product to cart
WC()->cart->add_to_cart($product->get_id(), 1);
echo "<p style='color: green;'>✅ Added product to cart</p>";

// Check cart contents
$cart_items = WC()->cart->get_cart();
echo "<p><strong>Cart Items:</strong> " . count($cart_items) . "</p>";
echo "<p><strong>Cart Total:</strong> " . WC()->cart->get_total() . "</p>";

// Check available payment gateways with cart
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
echo "<h2>💳 Available Payment Methods (with cart):</h2>";

if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment methods available</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " (" . esc_html($id) . ")</p>";
    }
}

// Check if MarzPay is available
if (isset($available_gateways['marzpay'])) {
    echo "<p style='color: green;'>✅ MarzPay is available for checkout!</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay is not available</p>";
}

echo "<h2>🎯 Test Checkout</h2>";
echo "<p>Now try checkout with items in cart:</p>";
echo "<ol>";
echo "<li><strong>Go to checkout:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Visit Checkout</a></li>";
echo "<li><strong>You should see payment methods now</strong></li>";
echo "<li><strong>Select 'Mobile Money (Airtel & MTN)'</strong></li>";
echo "<li><strong>Enter a test phone number</strong></li>";
echo "</ol>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test:</strong> 256700000000</li>";
echo "</ul>";

echo "<h2>🛒 Cart Management</h2>";
echo "<p><strong>Current Cart:</strong></p>";
echo "<ul>";
foreach ($cart_items as $cart_item_key => $cart_item) {
    $product = $cart_item['data'];
    echo "<li>" . esc_html($product->get_name()) . " - " . $product->get_price() . " UGX (Qty: " . $cart_item['quantity'] . ")</li>";
}
echo "</ul>";

echo "<p><a href='" . get_permalink(wc_get_page_id('cart')) . "' target='_blank'>View Cart</a> | <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Go to Checkout</a></p>";

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
