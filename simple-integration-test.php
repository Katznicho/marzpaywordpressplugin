<?php
/**
 * Simple WooCommerce Integration Test
 * No authentication required
 */

echo "<h1>🔧 MarzPay WooCommerce Integration Test</h1>";

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>📊 System Status</h2>";

// Check WordPress
echo "<p><strong>WordPress:</strong> ✅ Active (Version " . get_bloginfo('version') . ")</p>";

// Check WooCommerce
if (class_exists('WooCommerce')) {
    $wc = WC();
    echo "<p><strong>WooCommerce:</strong> ✅ Active (Version " . WC()->version . ")</p>";
} else {
    echo "<p><strong>WooCommerce:</strong> ❌ Not active</p>";
}

// Check MarzPay Plugin
if (class_exists('MarzPay_Plugin')) {
    echo "<p><strong>MarzPay Plugin:</strong> ✅ Active</p>";
} else {
    echo "<p><strong>MarzPay Plugin:</strong> ❌ Not active</p>";
}

// Check MarzPay Gateway
if (class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p><strong>MarzPay Gateway:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Gateway:</strong> ❌ Not available</p>";
}

// Check MarzPay Manager
if (class_exists('MarzPay_WooCommerce_Manager')) {
    echo "<p><strong>MarzPay Manager:</strong> ✅ Available</p>";
} else {
    echo "<p><strong>MarzPay Manager:</strong> ❌ Not available</p>";
}

// Check if gateway is registered
if (class_exists('WooCommerce') && class_exists('MarzPay_WooCommerce_Gateway')) {
    $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
    
    echo "<h2>💳 Available Payment Gateways</h2>";
    if (isset($available_gateways['marzpay'])) {
        echo "<p><strong>MarzPay Gateway:</strong> ✅ Registered and available</p>";
    } else {
        echo "<p><strong>MarzPay Gateway:</strong> ❌ Not registered</p>";
        
        // Check all available gateways
        echo "<h3>Available Gateways:</h3>";
        if (empty($available_gateways)) {
            echo "<p>No payment gateways available</p>";
        } else {
            echo "<ul>";
            foreach ($available_gateways as $id => $gateway) {
                echo "<li>" . $gateway->get_title() . " (" . $id . ")</li>";
            }
            echo "</ul>";
        }
    }
}

echo "<h2>🎯 Next Steps</h2>";
if (class_exists('WooCommerce') && class_exists('MarzPay_WooCommerce_Gateway')) {
    echo "<p>✅ <strong>Integration is working!</strong> You can now:</p>";
    echo "<ol>";
    echo "<li>Go to <strong>WooCommerce > Settings > Payments</strong></li>";
    echo "<li>Enable the <strong>MarzPay Mobile Money</strong> gateway</li>";
    echo "<li>Configure your API credentials</li>";
    echo "<li>Test with a product purchase</li>";
    echo "</ol>";
} else {
    echo "<p>❌ <strong>Integration needs attention:</strong></p>";
    if (!class_exists('WooCommerce')) {
        echo "<p>- Install and activate WooCommerce</p>";
    }
    if (!class_exists('MarzPay_WooCommerce_Gateway')) {
        echo "<p>- Check MarzPay plugin activation</p>";
    }
}

echo "<hr>";
echo "<p><em>Test completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
