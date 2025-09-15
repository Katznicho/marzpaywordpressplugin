<?php
/**
 * Aggressive Payment Methods Fix
 * This will force MarzPay to appear in checkout
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Aggressive Payment Methods Fix</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>🎯 Force Payment Gateway Registration</h2>";

// Add aggressive filter to force MarzPay to be available
add_filter('woocommerce_available_payment_gateways', function($gateways) {
    echo "<p>🔍 Filter called - Current gateways: " . implode(', ', array_keys($gateways)) . "</p>";
    
    // Force add MarzPay if it's not there
    if (!isset($gateways['marzpay'])) {
        $all_gateways = WC()->payment_gateways()->payment_gateways();
        if (isset($all_gateways['marzpay'])) {
            $gateways['marzpay'] = $all_gateways['marzpay'];
            echo "<p style='color: green;'>✅ Force added MarzPay to available gateways</p>";
        } else {
            echo "<p style='color: red;'>❌ MarzPay gateway not found in all gateways</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ MarzPay already in available gateways</p>";
    }
    
    return $gateways;
}, 5); // High priority

// Force enable MarzPay gateway
$gateways = WC()->payment_gateways()->payment_gateways();
if (isset($gateways['marzpay'])) {
    $marzpay = $gateways['marzpay'];
    $marzpay->update_option('enabled', 'yes');
    echo "<p style='color: green;'>✅ MarzPay gateway enabled</p>";
    
    // Set API credentials
    $api_key = get_option('marzpay_api_user');
    $api_secret = get_option('marzpay_api_key');
    
    if (!empty($api_key) && !empty($api_secret)) {
        $marzpay->update_option('api_key', $api_key);
        $marzpay->update_option('api_secret', $api_secret);
        echo "<p style='color: green;'>✅ API credentials set</p>";
    }
}

// Clear cart and add product
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

// Force reload payment gateways
WC()->payment_gateways()->init();

// Test the filter
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h2>💳 Available Payment Methods After Fix</h2>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment methods available</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " ($id)</p>";
    }
}

// Add a more aggressive hook that runs on every page load
add_action('init', function() {
    if (is_admin()) return;
    
    // Force enable MarzPay on every page load
    $gateways = WC()->payment_gateways()->payment_gateways();
    if (isset($gateways['marzpay'])) {
        $marzpay = $gateways['marzpay'];
        $marzpay->update_option('enabled', 'yes');
    }
}, 999);

echo "<h2>🎯 Test Now</h2>";
echo "<p>Please try your checkout page now:</p>";
echo "<ol>";
echo "<li><strong>Go to:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Checkout Page</a></li>";
echo "<li><strong>Hard refresh the page (Ctrl+F5 or Cmd+Shift+R)</strong></li>";
echo "<li><strong>Look for payment methods</strong></li>";
echo "</ol>";

echo "<h2>🔍 Debug Information</h2>";
echo "<p><strong>WordPress Debug:</strong> " . (WP_DEBUG ? 'Enabled' : 'Disabled') . "</p>";
echo "<p><strong>WooCommerce Version:</strong> " . WC()->version . "</p>";
echo "<p><strong>MarzPay Plugin Version:</strong> " . (defined('MARZPAY_VERSION') ? MARZPAY_VERSION : 'Unknown') . "</p>";

// Check if the gateway class exists
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p style='color: green;'>✅ MarzPay_WooCommerce_Gateway class exists</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay_WooCommerce_Gateway class not found</p>";
}

// Check if the manager class exists
if (class_exists('MarzPay_WooCommerce_Manager')) {
    echo "<p style='color: green;'>✅ MarzPay_WooCommerce_Manager class exists</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay_WooCommerce_Manager class not found</p>";
}

echo "<hr>";
echo "<p><em>Aggressive fix applied at " . date('Y-m-d H:i:s') . "</em></p>";
?>
