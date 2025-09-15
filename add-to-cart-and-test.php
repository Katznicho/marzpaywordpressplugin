<?php
/**
 * Add Items to Cart and Test Checkout
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🛒 Add Items to Cart and Test Checkout</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>🛍️ Adding Test Product to Cart</h2>";

// Clear any existing cart first
WC()->cart->empty_cart();

// Get a test product
$products = wc_get_products(array(
    'limit' => 1,
    'status' => 'publish'
));

if (empty($products)) {
    echo "<p style='color: red;'>❌ No products found. Creating a test product...</p>";
    
    // Create a simple test product
    $product = new WC_Product_Simple();
    $product->set_name('MarzPay Test Product');
    $product->set_description('Test product for MarzPay integration');
    $product->set_short_description('Test product');
    $product->set_regular_price('1000');
    $product->set_status('publish');
    $product->set_manage_stock(false);
    $product->set_stock_status('instock');
    
    $product_id = $product->save();
    
    if ($product_id) {
        echo "<p style='color: green;'>✅ Created test product (ID: $product_id)</p>";
        $product = wc_get_product($product_id);
    } else {
        echo "<p style='color: red;'>❌ Failed to create test product</p>";
        exit;
    }
} else {
    $product = $products[0];
    echo "<p>Using existing product: <strong>" . esc_html($product->get_name()) . "</strong></p>";
}

// Add product to cart
$cart_item_key = WC()->cart->add_to_cart($product->get_id(), 1);

if ($cart_item_key) {
    echo "<p style='color: green;'>✅ Added product to cart successfully</p>";
} else {
    echo "<p style='color: red;'>❌ Failed to add product to cart</p>";
    exit;
}

// Check cart contents
$cart_items = WC()->cart->get_cart();
echo "<p><strong>Cart Items:</strong> " . count($cart_items) . "</p>";
echo "<p><strong>Cart Total:</strong> " . WC()->cart->get_total() . "</p>";

// Force reload payment gateways
WC()->payment_gateways()->init();

// Check available payment gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h2>💳 Available Payment Methods</h2>";
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

// Create a direct checkout link with cart items
$checkout_url = wc_get_checkout_url();
echo "<h2>🎯 Direct Checkout Links</h2>";
echo "<p><strong>Checkout URL:</strong> <a href='$checkout_url' target='_blank'>$checkout_url</a></p>";

// Also create a direct link with cart items
$cart_url = wc_get_cart_url();
echo "<p><strong>Cart URL:</strong> <a href='$cart_url' target='_blank'>$cart_url</a></p>";

echo "<h2>📱 Test Instructions</h2>";
echo "<ol>";
echo "<li><strong>Click the checkout link above</strong></li>";
echo "<li><strong>You should see the product in your cart</strong></li>";
echo "<li><strong>Look for 'Mobile Money (Airtel & MTN)' in payment options</strong></li>";
echo "<li><strong>If you see it, select it and enter a test phone number</strong></li>";
echo "</ol>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test:</strong> 256700000000</li>";
echo "</ul>";

// Clear caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p style='color: green;'>✅ Cleared WordPress cache</p>";
}

echo "<h2>🔍 If Still Not Working</h2>";
echo "<p>Try these steps:</p>";
echo "<ol>";
echo "<li>Clear your browser cache (Ctrl+F5 or Cmd+Shift+R)</li>";
echo "<li>Try incognito/private browsing mode</li>";
echo "<li>Try a different browser</li>";
echo "<li>Check if you have any caching plugins active</li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
