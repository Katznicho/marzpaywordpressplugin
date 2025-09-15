<?php
/**
 * Deep Debug Checkout Payment Methods
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔍 Deep Debug Checkout Payment Methods</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>📊 System Check</h2>";

// Check if we're in checkout context
echo "<p><strong>Is Checkout:</strong> " . (is_checkout() ? '✅ Yes' : '❌ No') . "</p>";
echo "<p><strong>Is Cart:</strong> " . (is_cart() ? '✅ Yes' : '❌ No') . "</p>";

// Check cart contents
$cart_items = WC()->cart->get_cart();
echo "<p><strong>Cart Items:</strong> " . count($cart_items) . "</p>";

if (count($cart_items) > 0) {
    echo "<p><strong>Cart Total:</strong> " . WC()->cart->get_total() . "</p>";
    foreach ($cart_items as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        echo "<p>- " . esc_html($product->get_name()) . " - " . $product->get_price() . " UGX</p>";
    }
} else {
    echo "<p style='color: red;'>❌ No items in cart</p>";
    
    // Add a product to cart
    echo "<h2>🛒 Adding Product to Cart</h2>";
    $products = wc_get_products(array('limit' => 1, 'status' => 'publish'));
    
    if (!empty($products)) {
        $product = $products[0];
        $cart_item_key = WC()->cart->add_to_cart($product->get_id(), 1);
        
        if ($cart_item_key) {
            echo "<p style='color: green;'>✅ Added " . esc_html($product->get_name()) . " to cart</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to add product to cart</p>";
        }
    }
}

// Check payment gateways
echo "<h2>💳 Payment Gateway Analysis</h2>";

// Get all gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();
echo "<p><strong>Total Registered Gateways:</strong> " . count($all_gateways) . "</p>";

foreach ($all_gateways as $id => $gateway) {
    echo "<h3>" . esc_html($gateway->get_title()) . " ($id)</h3>";
    echo "<ul>";
    echo "<li>Enabled: " . ($gateway->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>Available: " . ($gateway->is_available() ? '✅ Yes' : '❌ No') . "</li>";
    
    // For MarzPay, check specific conditions
    if ($id === 'marzpay') {
        echo "<li>API Key: " . (!empty($gateway->api_key) ? '✅ Set' : '❌ Not set') . "</li>";
        echo "<li>API Secret: " . (!empty($gateway->api_secret) ? '✅ Set' : '❌ Not set') . "</li>";
        echo "<li>Test Mode: " . ($gateway->testmode === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
        
        // Check the is_available method conditions
        if ($gateway->enabled === 'no') {
            echo "<li style='color: red;'>❌ Not available: Gateway disabled</li>";
        }
        if (empty($gateway->api_key) || empty($gateway->api_secret)) {
            echo "<li style='color: red;'>❌ Not available: Missing API credentials</li>";
        }
    }
    echo "</ul>";
}

// Get available gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
echo "<h3>Available Gateways for Checkout:</h3>";

if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment gateways available for checkout</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " ($id)</p>";
    }
}

// Check if MarzPay is in available gateways
if (isset($available_gateways['marzpay'])) {
    echo "<p style='color: green;'>✅ MarzPay is available for checkout!</p>";
} else {
    echo "<p style='color: red;'>❌ MarzPay is NOT available for checkout</p>";
    
    // Try to force it
    echo "<h2>🔧 Force Enable MarzPay</h2>";
    
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
        
        // Force reload
        WC()->payment_gateways()->init();
        
        // Check again
        $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
        
        if (isset($available_gateways['marzpay'])) {
            echo "<p style='color: green;'>✅ MarzPay is now available after force enable!</p>";
        } else {
            echo "<p style='color: red;'>❌ MarzPay still not available</p>";
        }
    }
}

// Check WooCommerce settings
echo "<h2>⚙️ WooCommerce Settings</h2>";
echo "<ul>";
echo "<li>Currency: " . get_option('woocommerce_currency') . "</li>";
echo "<li>Country: " . get_option('woocommerce_default_country') . "</li>";
echo "<li>Checkout Page: " . (wc_get_page_id('checkout') ? '✅ Exists' : '❌ Missing') . "</li>";
echo "</ul>";

// Check if there are any PHP errors
echo "<h2>🐛 Error Check</h2>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $errors = file_get_contents($error_log);
    $recent_errors = array_slice(explode("\n", $errors), -10);
    echo "<p><strong>Recent PHP Errors:</strong></p>";
    echo "<pre>" . implode("\n", $recent_errors) . "</pre>";
} else {
    echo "<p>No error log found or accessible</p>";
}

// Try to simulate checkout page
echo "<h2>🎯 Simulate Checkout Page</h2>";

// Set up checkout context
global $woocommerce, $wp_query;
$wp_query->is_checkout = true;
$wp_query->is_page = true;

// Force WooCommerce to think we're on checkout
add_filter('woocommerce_is_checkout', '__return_true');

// Get available gateways again
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h3>Available Gateways in Checkout Context:</h3>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment gateways available in checkout context</p>";
} else {
    foreach ($available_gateways as $id => $gateway) {
        echo "<p>✅ " . esc_html($gateway->get_title()) . " ($id)</p>";
    }
}

echo "<h2>🎯 Final Test</h2>";
echo "<p>Now try your checkout page:</p>";
echo "<ol>";
echo "<li><strong>Go to:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Checkout Page</a></li>";
echo "<li><strong>Look for payment methods</strong></li>";
echo "<li><strong>If still not working, try a different browser</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Deep debug completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
