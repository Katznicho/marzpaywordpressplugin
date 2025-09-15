<?php
/**
 * MarzPay WooCommerce Integration Test Page
 * 
 * This file helps you test the WooCommerce integration
 * Access it via: yoursite.com/wp-content/plugins/marzpay-collections/test-woocommerce-integration.php
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to access this page.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>MarzPay WooCommerce Integration Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { background: #f9f9f9; padding: 20px; margin: 20px 0; border-radius: 5px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        .test-result { margin: 10px 0; padding: 10px; border-radius: 3px; }
        .test-result.pass { background: #d4edda; border: 1px solid #c3e6cb; }
        .test-result.fail { background: #f8d7da; border: 1px solid #f5c6cb; }
        .test-result.warning { background: #fff3cd; border: 1px solid #ffeaa7; }
        .button { background: #0073aa; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; margin: 5px; }
        .button:hover { background: #005a87; }
        .code { background: #f1f1f1; padding: 10px; border-radius: 3px; font-family: monospace; }
    </style>
</head>
<body>
    <h1>🧪 MarzPay WooCommerce Integration Test</h1>
    
    <div class="test-section">
        <h2>📋 Prerequisites Check</h2>
        
        <?php
        // Test 1: WordPress
        $wp_version = get_bloginfo('version');
        $wp_ok = version_compare($wp_version, '5.0', '>=');
        echo '<div class="test-result ' . ($wp_ok ? 'pass' : 'fail') . '">';
        echo '<strong>WordPress Version:</strong> ' . $wp_version . ' ' . ($wp_ok ? '✅' : '❌');
        echo '</div>';
        
        // Test 2: WooCommerce
        $wc_active = class_exists('WooCommerce');
        $wc_version = $wc_active ? WC()->version : 'Not installed';
        echo '<div class="test-result ' . ($wc_active ? 'pass' : 'fail') . '">';
        echo '<strong>WooCommerce:</strong> ' . ($wc_active ? 'Active (v' . $wc_version . ')' : 'Not installed') . ' ' . ($wc_active ? '✅' : '❌');
        echo '</div>';
        
        // Test 3: MarzPay Plugin
        $marzpay_active = class_exists('MarzPay_API_Client');
        echo '<div class="test-result ' . ($marzpay_active ? 'pass' : 'fail') . '">';
        echo '<strong>MarzPay Plugin:</strong> ' . ($marzpay_active ? 'Active' : 'Not active') . ' ' . ($marzpay_active ? '✅' : '❌');
        echo '</div>';
        
        // Test 4: MarzPay WooCommerce Gateway
        $gateway_exists = class_exists('MarzPay_WooCommerce_Gateway');
        echo '<div class="test-result ' . ($gateway_exists ? 'pass' : 'fail') . '">';
        echo '<strong>MarzPay WooCommerce Gateway:</strong> ' . ($gateway_exists ? 'Available' : 'Not available') . ' ' . ($gateway_exists ? '✅' : '❌');
        echo '</div>';
        ?>
    </div>
    
    <div class="test-section">
        <h2>⚙️ Configuration Check</h2>
        
        <?php
        if ($marzpay_active) {
            $api_client = MarzPay_API_Client::get_instance();
            $configured = $api_client->is_configured();
            
            echo '<div class="test-result ' . ($configured ? 'pass' : 'fail') . '">';
            echo '<strong>API Credentials:</strong> ' . ($configured ? 'Configured' : 'Not configured') . ' ' . ($configured ? '✅' : '❌');
            echo '</div>';
            
            if ($configured) {
                // Test API connection
                $account = $api_client->get_account();
                $api_working = isset($account['status']) && $account['status'] === 'success';
                
                echo '<div class="test-result ' . ($api_working ? 'pass' : 'fail') . '">';
                echo '<strong>API Connection:</strong> ' . ($api_working ? 'Working' : 'Failed') . ' ' . ($api_working ? '✅' : '❌');
                if (!$api_working && isset($account['message'])) {
                    echo '<br><small>Error: ' . esc_html($account['message']) . '</small>';
                }
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>🛒 WooCommerce Gateway Check</h2>
        
        <?php
        if ($wc_active) {
            $gateways = WC()->payment_gateways()->payment_gateways();
            $marzpay_gateway = isset($gateways['marzpay']) ? $gateways['marzpay'] : null;
            
            if ($marzpay_gateway) {
                $enabled = $marzpay_gateway->enabled === 'yes';
                echo '<div class="test-result ' . ($enabled ? 'pass' : 'warning') . '">';
                echo '<strong>MarzPay Gateway:</strong> ' . ($enabled ? 'Enabled' : 'Disabled') . ' ' . ($enabled ? '✅' : '⚠️');
                echo '</div>';
                
                if ($enabled) {
                    $available = $marzpay_gateway->is_available();
                    echo '<div class="test-result ' . ($available ? 'pass' : 'fail') . '">';
                    echo '<strong>Gateway Available:</strong> ' . ($available ? 'Yes' : 'No') . ' ' . ($available ? '✅' : '❌');
                    echo '</div>';
                }
            } else {
                echo '<div class="test-result fail">';
                echo '<strong>MarzPay Gateway:</strong> Not found in WooCommerce gateways ❌';
                echo '</div>';
            }
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>🧪 Quick Test Actions</h2>
        
        <p><strong>Test the integration with these quick actions:</strong></p>
        
        <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay'); ?>" class="button">
            ⚙️ Configure MarzPay Gateway
        </a>
        
        <a href="<?php echo admin_url('post-new.php?post_type=product'); ?>" class="button">
            🛍️ Create Test Product
        </a>
        
        <a href="<?php echo admin_url('admin.php?page=marzpay-woocommerce-orders'); ?>" class="button">
            📊 View MarzPay Orders
        </a>
        
        <a href="<?php echo home_url('/shop'); ?>" class="button">
            🛒 Go to Shop
        </a>
    </div>
    
    <div class="test-section">
        <h2>📝 Test Instructions</h2>
        
        <h3>1. Create a Test Product</h3>
        <div class="code">
            - Go to Products → Add New
            - Name: "Test Product"
            - Price: 1000 UGX
            - Publish
        </div>
        
        <h3>2. Test Checkout</h3>
        <div class="code">
            - Add product to cart
            - Go to checkout
            - Fill billing details
            - Select "Mobile Money (Airtel & MTN)"
            - Enter phone: 256759983853
            - Place order
        </div>
        
        <h3>3. Check Order</h3>
        <div class="code">
            - Go to WooCommerce → Orders
            - Find your test order
            - Check MarzPay payment details
            - Test "Check Payment Status" button
        </div>
        
        <h3>4. Test Admin Interface</h3>
        <div class="code">
            - Go to MarzPay → WooCommerce Orders
            - View statistics
            - Test bulk actions
        </div>
    </div>
    
    <div class="test-section">
        <h2>🔧 Troubleshooting</h2>
        
        <?php if (!$wc_active): ?>
        <div class="test-result fail">
            <strong>WooCommerce Not Active:</strong> Install and activate WooCommerce first.
        </div>
        <?php endif; ?>
        
        <?php if (!$marzpay_active): ?>
        <div class="test-result fail">
            <strong>MarzPay Plugin Not Active:</strong> Activate the MarzPay plugin.
        </div>
        <?php endif; ?>
        
        <?php if ($marzpay_active && !$gateway_exists): ?>
        <div class="test-result fail">
            <strong>WooCommerce Gateway Not Found:</strong> Check if WooCommerce is active and plugin is properly loaded.
        </div>
        <?php endif; ?>
        
        <h3>Common Issues:</h3>
        <ul>
            <li><strong>Gateway not showing:</strong> Check if WooCommerce is active and MarzPay plugin is activated</li>
            <li><strong>API connection failed:</strong> Verify API credentials in MarzPay settings</li>
            <li><strong>Payment not processing:</strong> Check phone number format and API status</li>
            <li><strong>Orders not updating:</strong> Configure webhook URL in MarzPay dashboard</li>
        </ul>
    </div>
    
    <div class="test-section">
        <h2>📞 Need Help?</h2>
        
        <p>If you encounter issues:</p>
        <ul>
            <li>Check the <a href="TESTING-GUIDE.md">Testing Guide</a></li>
            <li>Review <a href="WOOCOMMERCE-INTEGRATION.md">WooCommerce Integration Guide</a></li>
            <li>Enable WordPress debug mode for detailed logs</li>
            <li>Contact MarzPay support for API issues</li>
        </ul>
    </div>
    
    <p><small>Test page generated on <?php echo date('Y-m-d H:i:s'); ?></small></p>
</body>
</html>
