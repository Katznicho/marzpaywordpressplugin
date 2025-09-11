<?php
/**
 * Debug API endpoint to test data structure
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is logged in and has proper permissions
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to access this debug page.');
}

// Test data structure
$test_data = array(
    'amount' => 1000,
    'phone_number' => '256759983853',
    'reference' => wp_generate_uuid4(),
    'description' => 'Test payment collection request',
    'callback_url' => home_url('/marzpay-callback'),
    'country' => 'UG'
);

echo "<h1>MarzPay API Debug</h1>";
echo "<h2>Test Data Structure:</h2>";
echo "<pre>" . json_encode($test_data, JSON_PRETTY_PRINT) . "</pre>";

echo "<h2>API Client Test:</h2>";
$api_client = MarzPay_API_Client::get_instance();

if (!$api_client->is_configured()) {
    echo "<p style='color: red;'>❌ API not configured</p>";
} else {
    echo "<p style='color: green;'>✅ API configured</p>";
    
    // Test phone validation
    $phone_test = $api_client->validate_phone_number('+256759983853', 'UG');
    echo "<p>Phone validation test: " . ($phone_test ? $phone_test : 'FAILED') . "</p>";
    
    // Test the actual API call
    echo "<h3>Testing API Call:</h3>";
    $result = $api_client->collect_money($test_data);
    echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT) . "</pre>";
}
?>
