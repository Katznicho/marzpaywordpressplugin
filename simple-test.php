<?php
/**
 * Simple Test - Basic plugin functionality test
 */

echo "<h1>MarzPay Simple Test</h1>";

// Test 1: Check if WordPress is loaded
echo "<h2>1. WordPress Check</h2>";
if (function_exists('wp_get_current_user')) {
    echo "✅ WordPress loaded successfully<br>";
} else {
    echo "❌ WordPress not loaded<br>";
    exit;
}

// Test 2: Load the plugin manually
echo "<h2>2. Plugin Loading</h2>";
$plugin_file = __DIR__ . '/marzpay-collections.php';
if (file_exists($plugin_file)) {
    echo "✅ Plugin file exists<br>";
    require_once($plugin_file);
    echo "✅ Plugin file loaded<br>";
} else {
    echo "❌ Plugin file not found<br>";
    exit;
}

// Test 3: Check if classes exist
echo "<h2>3. Class Check</h2>";
$classes = ['MarzPay_Database', 'MarzPay_API_Client', 'MarzPay_Admin_Settings'];
foreach ($classes as $class) {
    if (class_exists($class)) {
        echo "✅ $class exists<br>";
    } else {
        echo "❌ $class missing<br>";
    }
}

// Test 4: Check database connection
echo "<h2>4. Database Connection</h2>";
global $wpdb;
if ($wpdb) {
    echo "✅ WordPress database connection active<br>";
    echo "Database prefix: " . $wpdb->prefix . "<br>";
} else {
    echo "❌ No database connection<br>";
}

// Test 5: Check if tables exist
echo "<h2>5. Database Tables</h2>";
$table_name = $wpdb->prefix . 'marzpay_transactions';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

if ($table_exists) {
    echo "✅ Table $table_name exists<br>";
    
    // Check table structure
    $columns = $wpdb->get_results("DESCRIBE $table_name");
    echo "Table columns:<br>";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})<br>";
    }
    
    // Check record count
    $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Records in table: $count<br>";
    
} else {
    echo "❌ Table $table_name does NOT exist<br>";
    
    // Try to create it
    echo "Attempting to create table...<br>";
    if (class_exists('MarzPay_Database')) {
        try {
            MarzPay_Database::create_tables();
            echo "✅ Table creation attempted<br>";
            
            // Check again
            $table_exists_after = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
            if ($table_exists_after) {
                echo "✅ Table created successfully!<br>";
            } else {
                echo "❌ Table creation failed<br>";
            }
        } catch (Exception $e) {
            echo "❌ Error creating table: " . $e->getMessage() . "<br>";
        }
    } else {
        echo "❌ MarzPay_Database class not available<br>";
    }
}

// Test 6: Try to insert a test record
echo "<h2>6. Test Record Insert</h2>";
if ($table_exists || $wpdb->get_var("SHOW TABLES LIKE '$table_name'")) {
    $test_data = array(
        'uuid' => 'test-' . time(),
        'reference' => 'TEST-' . time(),
        'type' => 'collection',
        'status' => 'successful',
        'amount' => 1000.00,
        'currency' => 'UGX',
        'phone_number' => '+256700000000',
        'description' => 'Test transaction',
        'provider' => 'mtn'
    );
    
    $result = $wpdb->insert($table_name, $test_data);
    
    if ($result) {
        echo "✅ Test record inserted successfully! ID: " . $wpdb->insert_id . "<br>";
    } else {
        echo "❌ Failed to insert test record<br>";
        echo "Error: " . $wpdb->last_error . "<br>";
    }
    
    // Check records again
    $new_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    echo "Records after insert: $new_count<br>";
    
    // Show recent records
    $recent = $wpdb->get_results("SELECT * FROM $table_name ORDER BY id DESC LIMIT 3");
    echo "Recent records:<br>";
    foreach ($recent as $record) {
        echo "- ID: {$record->id}, Reference: {$record->reference}, Amount: {$record->amount}<br>";
    }
}

// Test 7: Test the plugin's get_transactions method
echo "<h2>7. Plugin Method Test</h2>";
if (class_exists('MarzPay_Database')) {
    $database = MarzPay_Database::get_instance();
    $transactions = $database->get_transactions(array('limit' => 5));
    
    echo "Plugin get_transactions() returned: " . count($transactions) . " records<br>";
    
    if (!empty($transactions)) {
        echo "Sample transaction:<br>";
        $first = $transactions[0];
        echo "- Reference: {$first->reference}<br>";
        echo "- Type: {$first->type}<br>";
        echo "- Status: {$first->status}<br>";
        echo "- Amount: {$first->amount}<br>";
    }
} else {
    echo "❌ MarzPay_Database class not available for testing<br>";
}

echo "<h2>8. WordPress Admin Check</h2>";
if (function_exists('is_admin') && is_admin()) {
    echo "✅ Running in admin context<br>";
} else {
    echo "⚠️ Not in admin context<br>";
}

echo "<h2>9. Current User Check</h2>";
$current_user = wp_get_current_user();
if ($current_user && $current_user->ID > 0) {
    echo "✅ User logged in: " . $current_user->user_login . "<br>";
    echo "User capabilities: " . (current_user_can('manage_options') ? 'Admin' : 'Regular user') . "<br>";
} else {
    echo "❌ No user logged in<br>";
}

echo "<p><strong>Test completed!</strong></p>";
echo "<p><a href='debug-database.php'>Go to Database Debug</a></p>";
echo "<p><a href='../../../wp-admin/admin.php?page=marzpay-dashboard'>Go to Dashboard</a></p>";
?>
