<?php
/**
 * Force Payment Methods in Checkout
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Force Payment Methods in Checkout</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

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
    } else {
        echo "<p style='color: red;'>❌ Failed to add product to cart</p>";
        exit;
    }
} else {
    echo "<p style='color: red;'>❌ No products found</p>";
    exit;
}

echo "<h2>🔧 Force Enable MarzPay Gateway</h2>";

// Get all gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();

if (isset($all_gateways['marzpay'])) {
    $marzpay = $all_gateways['marzpay'];
    
    // Force enable
    $marzpay->update_option('enabled', 'yes');
    
    // Set API credentials
    $api_key = get_option('marzpay_api_user');
    $api_secret = get_option('marzpay_api_key');
    
    if (!empty($api_key) && !empty($api_secret)) {
        $marzpay->update_option('api_key', $api_key);
        $marzpay->update_option('api_secret', $api_secret);
        echo "<p style='color: green;'>✅ Set API credentials</p>";
    }
    
    echo "<p style='color: green;'>✅ MarzPay gateway enabled</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay gateway not found</p>";
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

// Try a different approach - directly hook into WooCommerce
echo "<h2>🎯 Direct WooCommerce Hook</h2>";

// Add a filter to force MarzPay to be available
add_filter('woocommerce_available_payment_gateways', function($gateways) {
    // Force add MarzPay if it's not there
    if (!isset($gateways['marzpay'])) {
        $all_gateways = WC()->payment_gateways()->payment_gateways();
        if (isset($all_gateways['marzpay'])) {
            $gateways['marzpay'] = $all_gateways['marzpay'];
            echo "<p style='color: green;'>✅ Force added MarzPay to available gateways</p>";
        }
    }
    return $gateways;
}, 20);

// Force reload again
WC()->payment_gateways()->init();
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h3>After Direct Hook:</h3>";
if (isset($available_gateways['marzpay'])) {
    echo "<p style='color: green;'>✅ MarzPay is now available!</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay still not available</p>";
}

// Clear all caches
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p style='color: green;'>✅ Cleared WordPress cache</p>";
}

// Try to clear WooCommerce cache
if (function_exists('wc_delete_product_transients')) {
    wc_delete_product_transients();
    echo "<p style='color: green;'>✅ Cleared WooCommerce cache</p>";
}

echo "<h2>🎯 Test Checkout Now</h2>";
echo "<p>Please try your checkout page:</p>";
echo "<ol>";
echo "<li><strong>Go to:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Checkout Page</a></li>";
echo "<li><strong>Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)</strong></li>";
echo "<li><strong>Look for payment methods</strong></li>";
echo "</ol>";

echo "<h2>🔍 Alternative Test</h2>";
echo "<p>If still not working, try this direct checkout link with cart items:</p>";
echo "<p><a href='" . get_permalink(wc_get_page_id('checkout')) . "?add-to-cart=" . $product->get_id() . "' target='_blank'>Direct Checkout with Product</a></p>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test:</strong> 256700000000</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Force checkout completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
