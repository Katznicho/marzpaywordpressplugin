<?php
/**
 * Test MarzPay Shortcode
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. Admin privileges required.');
}

echo "<h1>MarzPay Shortcode Test</h1>";

echo "<h2>1. Collect Money Shortcode</h2>";
echo do_shortcode('[marzpay_collect amount="1000" phone="0701234567" reference="TEST-001" description="Test payment" button_text="Test Payment"]');

echo "<h2>2. Send Money Shortcode</h2>";
echo do_shortcode('[marzpay_send amount="500" phone="0701234567" reference="SEND-001" description="Test send" button_text="Test Send"]');

echo "<h2>3. Balance Shortcode</h2>";
echo do_shortcode('[marzpay_balance]');

echo "<h2>4. Transactions Shortcode</h2>";
echo do_shortcode('[marzpay_transactions limit="5"]');

echo "<h2>5. Payment Button Shortcode</h2>";
echo do_shortcode('[marzpay_button amount="2000" phone="0701234567" reference="BTN-001" description="Test button"]');

echo "<p><a href='test-collect.html'>Go to HTML Test Form</a></p>";
echo "<p><a href='../../../wp-admin/admin.php?page=marzpay-dashboard'>Go to Dashboard</a></p>";
?>
