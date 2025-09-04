<?php
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Clean up all plugin options
delete_option('marzpay_api_user');
delete_option('marzpay_api_key');
delete_option('marzpay_callback_url');

// Clean up any transients that might exist
delete_transient('marzpay_test_error_details');

// Clean up any scheduled events (if we add them in future versions)
wp_clear_scheduled_hook('marzpay_cleanup_transients');

// Note: If we add database tables in future versions, they would be dropped here
// global $wpdb;
// $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}marzpay_payments");
