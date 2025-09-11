<?php
/**
 * Debug Database - Check MarzPay Database Status
 * 
 * This file helps debug database issues
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

echo "<h1>MarzPay Database Debug</h1>";

global $wpdb;
$table = $wpdb->prefix . 'marzpay_transactions';

echo "<h2>1. Table Existence Check</h2>";
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
echo "Table exists: " . ($table_exists ? "✅ YES" : "❌ NO") . "<br>";

if ($table_exists) {
    echo "<h2>2. Table Structure</h2>";
    $structure = $wpdb->get_results("DESCRIBE $table");
    echo "<pre>";
    foreach ($structure as $column) {
        echo "{$column->Field} - {$column->Type} - {$column->Null} - {$column->Key} - {$column->Default}\n";
    }
    echo "</pre>";
    
    echo "<h2>3. Transaction Count</h2>";
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
    echo "Total transactions: <strong>$count</strong><br>";
    
    if ($count > 0) {
        echo "<h2>4. Recent Transactions</h2>";
        $transactions = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC LIMIT 5");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>UUID</th><th>Reference</th><th>Type</th><th>Status</th><th>Amount</th><th>Phone</th><th>Created</th></tr>";
        foreach ($transactions as $txn) {
            echo "<tr>";
            echo "<td>{$txn->id}</td>";
            echo "<td>{$txn->uuid}</td>";
            echo "<td>{$txn->reference}</td>";
            echo "<td>{$txn->type}</td>";
            echo "<td>{$txn->status}</td>";
            echo "<td>{$txn->amount} {$txn->currency}</td>";
            echo "<td>{$txn->phone_number}</td>";
            echo "<td>{$txn->created_at}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<h2>4. No Transactions Found</h2>";
        echo "<p>Let's test inserting a transaction...</p>";
        
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
            'metadata' => json_encode(array('test' => true))
        );
        
        $insert_result = $wpdb->insert($table, $test_data);
        
        if ($insert_result) {
            echo "✅ Test transaction inserted successfully!<br>";
            echo "New transaction ID: " . $wpdb->insert_id . "<br>";
            echo "<a href='?refresh=1'>Refresh page to see the transaction</a>";
        } else {
            echo "❌ Failed to insert test transaction<br>";
            echo "Error: " . $wpdb->last_error;
        }
    }
} else {
    echo "<h2>2. Table Creation</h2>";
    echo "<p>Table doesn't exist. Let's try to create it...</p>";
    
    // Try to create the table
    if (class_exists('MarzPay_Database')) {
        MarzPay_Database::create_tables();
        echo "✅ Attempted to create tables. <a href='?refresh=1'>Refresh page</a>";
    } else {
        echo "❌ MarzPay_Database class not found. Plugin may not be loaded properly.";
    }
}

echo "<h2>5. Plugin Status</h2>";
echo "MarzPay_Database class exists: " . (class_exists('MarzPay_Database') ? "✅ YES" : "❌ NO") . "<br>";
echo "MarzPay_API_Client class exists: " . (class_exists('MarzPay_API_Client') ? "✅ YES" : "❌ NO") . "<br>";

echo "<h2>6. WordPress Debug Status</h2>";
echo "WP_DEBUG enabled: " . (defined('WP_DEBUG') && WP_DEBUG ? "✅ YES" : "❌ NO") . "<br>";

if (defined('WP_DEBUG') && WP_DEBUG) {
    echo "<p><strong>Debug logs should be available at:</strong> " . WP_CONTENT_DIR . "/debug.log</p>";
}
?>
