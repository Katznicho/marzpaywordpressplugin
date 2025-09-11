<?php
/**
 * Force Create Tables - Manually create MarzPay database tables
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

echo "<h1>Force Create MarzPay Tables</h1>";

// Check if classes exist
if (!class_exists('MarzPay_Database')) {
    echo "❌ MarzPay_Database class not found. Loading plugin files...<br>";
    
    // Try to load the plugin files manually
    $plugin_dir = plugin_dir_path(__FILE__);
    require_once($plugin_dir . 'includes/class-marzpay-database.php');
    
    if (!class_exists('MarzPay_Database')) {
        die("❌ Still can't load MarzPay_Database class. Plugin may be corrupted.");
    }
    echo "✅ MarzPay_Database class loaded successfully.<br>";
}

echo "<h2>Creating Database Tables...</h2>";

try {
    MarzPay_Database::create_tables();
    echo "✅ Database tables creation attempted.<br>";
} catch (Exception $e) {
    echo "❌ Error creating tables: " . $e->getMessage() . "<br>";
}

echo "<h2>Verifying Tables...</h2>";

global $wpdb;

$tables_to_check = array(
    'marzpay_transactions',
    'marzpay_webhooks', 
    'marzpay_webhook_logs',
    'marzpay_api_logs'
);

foreach ($tables_to_check as $table_name) {
    $full_table_name = $wpdb->prefix . $table_name;
    $exists = $wpdb->get_var("SHOW TABLES LIKE '$full_table_name'");
    
    if ($exists) {
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $full_table_name");
        echo "✅ Table <strong>$full_table_name</strong> exists with <strong>$count</strong> records<br>";
    } else {
        echo "❌ Table <strong>$full_table_name</strong> does NOT exist<br>";
    }
}

echo "<h2>Testing Transaction Insert...</h2>";

$database = MarzPay_Database::get_instance();
$test_data = array(
    'uuid' => 'test-uuid-' . time(),
    'reference' => 'TEST-' . time(),
    'type' => 'collection',
    'status' => 'successful',
    'amount' => 1000.00,
    'currency' => 'UGX',
    'phone_number' => '+256700000000',
    'description' => 'Test transaction for debugging',
    'provider' => 'mtn',
    'metadata' => array('test' => true, 'created_by' => 'debug_tool')
);

$insert_result = $database->insert_transaction($test_data);

if ($insert_result) {
    echo "✅ Test transaction inserted successfully! ID: $insert_result<br>";
} else {
    echo "❌ Failed to insert test transaction<br>";
    echo "Last error: " . $wpdb->last_error . "<br>";
}

echo "<h2>Checking Recent Transactions...</h2>";

$recent_transactions = $database->get_transactions(array('limit' => 5));
echo "Found " . count($recent_transactions) . " recent transactions:<br>";

if (!empty($recent_transactions)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Reference</th><th>Type</th><th>Status</th><th>Amount</th><th>Phone</th></tr>";
    foreach ($recent_transactions as $txn) {
        echo "<tr>";
        echo "<td>{$txn->id}</td>";
        echo "<td>{$txn->reference}</td>";
        echo "<td>{$txn->type}</td>";
        echo "<td>{$txn->status}</td>";
        echo "<td>{$txn->amount} {$txn->currency}</td>";
        echo "<td>{$txn->phone_number}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No transactions found in database.<br>";
}

echo "<h2>Plugin Status Check...</h2>";
echo "Plugin file exists: " . (file_exists(__FILE__) ? "✅ YES" : "❌ NO") . "<br>";
echo "Plugin directory: " . plugin_dir_path(__FILE__) . "<br>";
echo "WordPress version: " . get_bloginfo('version') . "<br>";
echo "PHP version: " . phpversion() . "<br>";

echo "<p><a href='debug-database.php'>Go to Database Debug Tool</a></p>";
echo "<p><a href='../../../wp-admin/admin.php?page=marzpay-dashboard'>Go to MarzPay Dashboard</a></p>";
?>
