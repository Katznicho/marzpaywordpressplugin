<?php
/**
 * Diagnose Payment Methods Issue
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    echo "<p style='color: red;'>❌ You must be logged in as an administrator to run diagnostics.</p>";
    echo "<p><a href='" . wp_login_url() . "'>Login here</a></p>";
    exit;
}

echo "<h1>🔍 Diagnose Payment Methods Issue</h1>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active.</p>";
    exit;
}

echo "<h2>📊 Payment Gateway Status</h2>";

// Get all payment gateways
$all_gateways = WC()->payment_gateways()->payment_gateways();
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

echo "<h3>All Registered Gateways:</h3>";
if (empty($all_gateways)) {
    echo "<p style='color: red;'>❌ No payment gateways registered</p>";
} else {
    echo "<table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>";
    echo "<tr><th>Gateway ID</th><th>Title</th><th>Enabled</th><th>Available</th><th>Status</th></tr>";
    foreach ($all_gateways as $id => $gateway) {
        $enabled = $gateway->enabled === 'yes' ? '✅ Yes' : '❌ No';
        $available = isset($available_gateways[$id]) ? '✅ Yes' : '❌ No';
        $status = $gateway->enabled === 'yes' ? 'Enabled' : 'Disabled';
        
        echo "<tr>";
        echo "<td>" . esc_html($id) . "</td>";
        echo "<td>" . esc_html($gateway->get_title()) . "</td>";
        echo "<td>" . $enabled . "</td>";
        echo "<td>" . $available . "</td>";
        echo "<td>" . $status . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>Available Gateways (for checkout):</h3>";
if (empty($available_gateways)) {
    echo "<p style='color: red;'>❌ No payment gateways available for checkout</p>";
} else {
    echo "<ul>";
    foreach ($available_gateways as $id => $gateway) {
        echo "<li>" . esc_html($gateway->get_title()) . " (" . esc_html($id) . ")</li>";
    }
    echo "</ul>";
}

// Check MarzPay specifically
echo "<h2>🔍 MarzPay Gateway Details</h2>";

if (isset($all_gateways['marzpay'])) {
    $marzpay_gateway = $all_gateways['marzpay'];
    echo "<p style='color: green;'>✅ MarzPay gateway is registered</p>";
    
    echo "<h3>MarzPay Configuration:</h3>";
    echo "<ul>";
    echo "<li><strong>Title:</strong> " . esc_html($marzpay_gateway->get_title()) . "</li>";
    echo "<li><strong>Enabled:</strong> " . ($marzpay_gateway->enabled === 'yes' ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>Available:</strong> " . ($marzpay_gateway->is_available() ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li><strong>API Key:</strong> " . (!empty($marzpay_gateway->api_key) ? '✅ Set' : '❌ Not set') . "</li>";
    echo "<li><strong>API Secret:</strong> " . (!empty($marzpay_gateway->api_secret) ? '✅ Set' : '❌ Not set') . "</li>";
    echo "</ul>";
    
    // Check why it might not be available
    if (!$marzpay_gateway->is_available()) {
        echo "<h3>🚨 Why MarzPay is not available:</h3>";
        echo "<ul>";
        if ($marzpay_gateway->enabled !== 'yes') {
            echo "<li style='color: red;'>❌ Gateway is not enabled</li>";
        }
        if (empty($marzpay_gateway->api_key)) {
            echo "<li style='color: red;'>❌ API Key is not set</li>";
        }
        if (empty($marzpay_gateway->api_secret)) {
            echo "<li style='color: red;'>❌ API Secret is not set</li>";
        }
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>❌ MarzPay gateway is not registered</p>";
}

// Check WooCommerce settings
echo "<h2>⚙️ WooCommerce Settings</h2>";

$currency = get_option('woocommerce_currency');
$country = get_option('woocommerce_default_country');
$checkout_page = wc_get_page_id('checkout');

echo "<ul>";
echo "<li><strong>Currency:</strong> " . esc_html($currency) . "</li>";
echo "<li><strong>Default Country:</strong> " . esc_html($country) . "</li>";
echo "<li><strong>Checkout Page:</strong> " . ($checkout_page ? '✅ Exists' : '❌ Missing') . "</li>";
echo "</ul>";

// Check if we're on the right page
echo "<h2>🔧 Quick Fixes</h2>";

if (isset($all_gateways['marzpay']) && $all_gateways['marzpay']->enabled !== 'yes') {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffeaa7; border-radius: 5px;'>";
    echo "<h3>🚨 MarzPay Gateway is Disabled</h3>";
    echo "<p>To fix this:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "' target='_blank'>WooCommerce > Settings > Payments > MarzPay</a></li>";
    echo "<li>Check the 'Enable MarzPay Mobile Money' checkbox</li>";
    echo "<li>Save changes</li>";
    echo "</ol>";
    echo "</div>";
}

if (isset($all_gateways['marzpay']) && (empty($all_gateways['marzpay']->api_key) || empty($all_gateways['marzpay']->api_secret))) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h3>🚨 API Credentials Missing</h3>";
    echo "<p>To fix this:</p>";
    echo "<ol>";
    echo "<li>Go to <a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "' target='_blank'>WooCommerce > Settings > Payments > MarzPay</a></li>";
    echo "<li>Enter your API Key and API Secret</li>";
    echo "<li>Save changes</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>🎯 Test Payment Methods</h2>";
echo "<p>After fixing the issues above, test the payment methods:</p>";
echo "<ol>";
echo "<li><strong>Visit your shop:</strong> <a href='" . get_permalink(wc_get_page_id('shop')) . "' target='_blank'>Go to Shop</a></li>";
echo "<li><strong>Add a product to cart</strong></li>";
echo "<li><strong>Go to checkout:</strong> <a href='" . get_permalink(wc_get_page_id('checkout')) . "' target='_blank'>Go to Checkout</a></li>";
echo "<li><strong>Check if payment methods appear</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><em>Diagnosis completed at " . date('Y-m-d H:i:s') . "</em></p>";
?>
