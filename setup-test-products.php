<?php
/**
 * Setup Test Products for MarzPay WooCommerce Testing
 * 
 * This script creates test products for testing the WooCommerce integration
 * Run this once to set up test products
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    wp_die('You must be an administrator to run this script.');
}

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    wp_die('WooCommerce must be active to create test products.');
}

echo "<h1>🛍️ Setting up Test Products for MarzPay WooCommerce Testing</h1>";

// Test products to create
$test_products = array(
    array(
        'name' => 'Test Product - Small',
        'price' => 1000,
        'description' => 'Small test product for MarzPay integration testing. Perfect for testing minimum amount payments.',
        'short_description' => 'Small test product (1000 UGX)'
    ),
    array(
        'name' => 'Test Product - Medium',
        'price' => 5000,
        'description' => 'Medium test product for MarzPay integration testing. Good for testing regular amount payments.',
        'short_description' => 'Medium test product (5000 UGX)'
    ),
    array(
        'name' => 'Test Product - Large',
        'price' => 10000,
        'description' => 'Large test product for MarzPay integration testing. Ideal for testing higher amount payments.',
        'short_description' => 'Large test product (10000 UGX)'
    ),
    array(
        'name' => 'Test Product - Minimum',
        'price' => 500,
        'description' => 'Minimum amount test product for MarzPay integration testing. Tests the minimum payment limit.',
        'short_description' => 'Minimum test product (500 UGX)'
    )
);

$created_count = 0;
$updated_count = 0;

foreach ($test_products as $product_data) {
    // Check if product already exists
    $existing_product = get_page_by_title($product_data['name'], OBJECT, 'product');
    
    if ($existing_product) {
        // Update existing product
        $product = wc_get_product($existing_product->ID);
        $product->set_regular_price($product_data['price']);
        $product->set_description($product_data['description']);
        $product->set_short_description($product_data['short_description']);
        $product->save();
        
        echo "<p>✅ Updated existing product: <strong>{$product_data['name']}</strong> - {$product_data['price']} UGX</p>";
        $updated_count++;
    } else {
        // Create new product
        $product = new WC_Product_Simple();
        $product->set_name($product_data['name']);
        $product->set_regular_price($product_data['price']);
        $product->set_description($product_data['description']);
        $product->set_short_description($product_data['short_description']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_featured(false);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        
        $product_id = $product->save();
        
        if ($product_id) {
            echo "<p>✅ Created new product: <strong>{$product_data['name']}</strong> - {$product_data['price']} UGX (ID: {$product_id})</p>";
            $created_count++;
        } else {
            echo "<p>❌ Failed to create product: <strong>{$product_data['name']}</strong></p>";
        }
    }
}

echo "<hr>";
echo "<h2>📊 Summary</h2>";
echo "<p><strong>Products Created:</strong> {$created_count}</p>";
echo "<p><strong>Products Updated:</strong> {$updated_count}</p>";
echo "<p><strong>Total Products:</strong> " . ($created_count + $updated_count) . "</p>";

echo "<hr>";
echo "<h2>🧪 Next Steps</h2>";
echo "<ol>";
echo "<li>Go to your <a href='" . home_url('/shop') . "'>shop page</a> to see the test products</li>";
echo "<li>Add a product to cart and test the checkout process</li>";
echo "<li>Select 'Mobile Money (Airtel & MTN)' as payment method</li>";
echo "<li>Use test phone number: <strong>256759983853</strong></li>";
echo "<li>Complete the order and check the admin interface</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>🔗 Useful Links</h2>";
echo "<ul>";
echo "<li><a href='" . admin_url('edit.php?post_type=product') . "'>Manage Products</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=wc-settings&tab=checkout&section=marzpay') . "'>MarzPay Gateway Settings</a></li>";
echo "<li><a href='" . admin_url('admin.php?page=marzpay-woocommerce-orders') . "'>MarzPay Orders</a></li>";
echo "<li><a href='" . home_url('/shop') . "'>Shop Page</a></li>";
echo "<li><a href='" . home_url('/cart') . "'>Cart Page</a></li>";
echo "<li><a href='" . home_url('/checkout') . "'>Checkout Page</a></li>";
echo "</ul>";

echo "<hr>";
echo "<p><small>Script completed on " . date('Y-m-d H:i:s') . "</small></p>";
?>
