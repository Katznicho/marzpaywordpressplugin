<?php
/**
 * Manual Plugin Activation - Force activate the plugin
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

echo "<h1>Manual Plugin Activation</h1>";

// Load the plugin
require_once(__DIR__ . '/marzpay-collections.php');

echo "<h2>1. Plugin Loaded</h2>";
echo "✅ Plugin file loaded successfully<br>";

echo "<h2>2. Running Activation Hook</h2>";

// Manually run the activation function
if (function_exists('marzpay_activate')) {
    echo "✅ Activation function found<br>";
    
    try {
        marzpay_activate();
        echo "✅ Activation function executed successfully<br>";
    } catch (Exception $e) {
        echo "❌ Error during activation: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Activation function not found<br>";
}

echo "<h2>3. Checking Database Tables</h2>";

global $wpdb;

$tables = [
    'marzpay_transactions',
    'marzpay_webhooks',
    'marzpay_webhook_logs', 
    'marzpay_api_logs'
];

foreach ($tables as $table) {
    $full_name = $wpdb->prefix . $table;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_name'");
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_name");
        echo "✅ $full_name exists ($count records)<br>";
    } else {
        echo "❌ $full_name missing<br>";
    }
}

echo "<h2>4. Testing Database Operations</h2>";

if (class_exists('MarzPay_Database')) {
    $database = MarzPay_Database::get_instance();
    
    // Test insert
    $test_data = array(
        'uuid' => 'manual-test-' . time(),
        'reference' => 'MANUAL-' . time(),
        'type' => 'collection',
        'status' => 'successful',
        'amount' => 2000.00,
        'currency' => 'UGX',
        'phone_number' => '+256700000001',
        'description' => 'Manual activation test',
        'provider' => 'airtel'
    );
    
    $insert_result = $database->insert_transaction($test_data);
    
    if ($insert_result) {
        echo "✅ Test transaction inserted! ID: $insert_result<br>";
    } else {
        echo "❌ Failed to insert test transaction<br>";
    }
    
    // Test retrieval
    $transactions = $database->get_transactions(array('limit' => 5));
    echo "✅ Retrieved " . count($transactions) . " transactions<br>";
    
    if (!empty($transactions)) {
        echo "Latest transaction: " . $transactions[0]->reference . "<br>";
    }
    
} else {
    echo "❌ MarzPay_Database class not available<br>";
}

echo "<h2>5. Plugin Status</h2>";

// Check if plugin is active
$active_plugins = get_option('active_plugins');
$plugin_file = 'marzpay-collections/marzpay-collections.php';

if (in_array($plugin_file, $active_plugins)) {
    echo "✅ Plugin is active in WordPress<br>";
} else {
    echo "❌ Plugin is NOT active in WordPress<br>";
    echo "Active plugins:<br>";
    foreach ($active_plugins as $plugin) {
        echo "- $plugin<br>";
    }
}

echo "<h2>6. WordPress Options</h2>";

$options = [
    'marzpay_version',
    'marzpay_api_user',
    'marzpay_api_key'
];

foreach ($options as $option) {
    $value = get_option($option);
    if ($value) {
        echo "✅ $option: " . (strlen($value) > 20 ? substr($value, 0, 20) . '...' : $value) . "<br>";
    } else {
        echo "❌ $option: Not set<br>";
    }
}

echo "<p><strong>Manual activation completed!</strong></p>";
echo "<p><a href='simple-test.php'>Run Simple Test</a></p>";
echo "<p><a href='../../../wp-admin/admin.php?page=marzpay-dashboard'>Go to Dashboard</a></p>";
?>
