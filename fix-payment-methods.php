<?php
/**
 * Fix Payment Methods - Enable MarzPay Gateway
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h1>🔧 Fix Payment Methods - Enable MarzPay Gateway</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>📊 Current Status</h2>";

// Get all payment gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();

if (isset($all_gateways['marzpay'])) {
    echo "<p style='color: green;'>✅ MarzPay gateway is registered</p>";
    
    $marzpay_gateway = $all_gateways['marzpay'];
    echo "<p><strong>Current Status:</strong> " . ($marzpay_gateway->enabled === 'yes' ? '✅ Enabled' : '❌ Disabled') . "</p>";
    
    if ($marzpay_gateway->enabled !== 'yes') {
        echo "<h2>🔧 Enabling MarzPay Gateway...</h2>";
        
        // Enable the gateway
        $marzpay_gateway->update_option('enabled', 'yes');
        
        // Also update the WooCommerce settings directly
        $payment_gateways = get_option('woocommerce_payment_gateways', array());
        if (isset($payment_gateways['marzpay'])) {
            $payment_gateways['marzpay']['enabled'] = 'yes';
            update_option('woocommerce_payment_gateways', $payment_gateways);
        }
        
        echo "<p style='color: green;'>✅ MarzPay gateway enabled!</p>";
        
        // Check if API credentials are set
        $api_key = get_option('marzpay_api_user');
        $api_secret = get_option('marzpay_api_key');
        
        if (!empty($api_key) && !empty($api_secret)) {
            echo "<p style='color: green;'>✅ API credentials are configured</p>";
            
            // Set the API credentials in the gateway
            $marzpay_gateway->update_option('api_key', $api_key);
            $marzpay_gateway->update_option('api_secret', $api_secret);
            
            echo "<p style='color: green;'>✅ API credentials synced to gateway</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ API credentials not configured</p>";
            echo "<p>Please configure your API credentials in <a href='" . admin_url('admin.php?page=marzpay-settings') . "' target='_blank'>MarzPay Settings</a></p>";
        }
        
    } else {
        echo "<p style='color: green;'>✅ MarzPay gateway is already enabled</p>";
    }
    
    // Force WooCommerce to reload gateways
    WC()->payment_gateways()->init();
    
    // Check available gateways again
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    
    echo "<h2>📱 Available Payment Methods</h2>";
    if (isset($available_gateways['marzpay'])) {
        echo "<p style='color: green;'>✅ MarzPay is now available for checkout!</p>";
        echo "<p><strong>Gateway Title:</strong> " . esc_html($available_gateways['marzpay']->get_title()) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ MarzPay is still not available</p>";
        
        // Check why
        if (empty($api_key) || empty($api_secret)) {
            echo "<p style='color: red;'>❌ API credentials are missing</p>";
        }
        if (!$marzpay_gateway->is_available()) {
            echo "<p style='color: red;'>❌ Gateway is not available (check API credentials)</p>";
        }
    }
    
} else {
    echo "<p style='color: red;'>❌ MarzPay gateway is not registered</p>";
    echo "<p>This might be a plugin loading issue. Please check:</p>";
    echo "<ol>";
    echo "<li>MarzPay plugin is activated</li>";
    echo "<li>WooCommerce is activated</li>";
    echo "<li>No PHP errors in the error log</li>";
    echo "</ol>";
}

echo "<h2>🎯 Test Your Checkout</h2>";
echo "<p>Now try your checkout again:</p>";
echo "<ol>";
echo "<li><strong>Go to your shop:</strong> <a href='" . get_permalink(wc_get_page_id('shop')) . "' target='_blank'>Visit Shop</a></li>";
echo "<li><strong>Add a product to cart</strong></li>";
echo "<li><strong>Go to checkout:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Go to Checkout</a></li>";
echo "<li><strong>Look for 'Mobile Money (Airtel & MTN)' in payment options</strong></li>";
echo "</ol>";

echo "<h2>📱 Test Phone Numbers</h2>";
echo "<ul>";
echo "<li><strong>MTN:</strong> 256781230949</li>";
echo "<li><strong>Airtel:</strong> 256759983853</li>";
echo "<li><strong>Test Number:</strong> 256700000000</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Fix completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
