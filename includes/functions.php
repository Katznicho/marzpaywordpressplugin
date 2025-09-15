<?php
/**
 * MarzPay Helper Functions
 * 
 * Utility functions for the MarzPay plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get MarzPay API client instance
 */
function marzpay_get_api_client() {
    return MarzPay_API_Client::get_instance();
}

/**
 * Get MarzPay database instance
 */
function marzpay_get_database() {
    return MarzPay_Database::get_instance();
}

/**
 * Format currency amount
 */
function marzpay_format_amount( $amount, $currency = 'UGX' ) {
    // Handle API response format where amount is an array
    if ( is_array( $amount ) ) {
        if ( isset( $amount['formatted'] ) && isset( $amount['currency'] ) ) {
            return $amount['formatted'] . ' ' . $amount['currency'];
        } elseif ( isset( $amount['raw'] ) && isset( $amount['currency'] ) ) {
            return number_format( $amount['raw'], 0, '.', ',' ) . ' ' . $amount['currency'];
        } elseif ( isset( $amount['raw'] ) ) {
            return number_format( $amount['raw'], 0, '.', ',' ) . ' ' . $currency;
        }
    }
    
    // Handle simple numeric amount
    return number_format( $amount, 0, '.', ',' ) . ' ' . $currency;
}

/**
 * Validate phone number for Uganda
 */
function marzpay_validate_phone( $phone ) {
    $phone = preg_replace( '/[^0-9]/', '', $phone );
    
    // Check if it's a valid Uganda phone number
    if ( strlen( $phone ) === 9 && substr( $phone, 0, 1 ) === '7' ) {
        return '256' . $phone;
    } elseif ( strlen( $phone ) === 12 && substr( $phone, 0, 3 ) === '256' ) {
        return $phone;
    }
    
    return false;
}

/**
 * Get transaction status badge HTML
 */
function marzpay_get_status_badge( $status ) {
    $status_classes = array(
        'pending' => 'marzpay-status-pending',
        'processing' => 'marzpay-status-processing',
        'successful' => 'marzpay-status-successful',
        'failed' => 'marzpay-status-failed',
        'cancelled' => 'marzpay-status-cancelled'
    );
    
    $class = isset( $status_classes[ $status ] ) ? $status_classes[ $status ] : 'marzpay-status-unknown';
    $label = ucfirst( $status );
    
    return '<span class="marzpay-status-badge ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Get transaction type badge HTML
 */
function marzpay_get_type_badge( $type ) {
    $type_classes = array(
        'collection' => 'marzpay-type-collection',
        'withdrawal' => 'marzpay-type-withdrawal',
        'charge' => 'marzpay-type-charge',
        'refund' => 'marzpay-type-refund'
    );
    
    $class = isset( $type_classes[ $type ] ) ? $type_classes[ $type ] : 'marzpay-type-unknown';
    $label = ucfirst( $type );
    
    return '<span class="marzpay-type-badge ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Get provider badge HTML
 */
function marzpay_get_provider_badge( $provider ) {
    $provider_classes = array(
        'mtn' => 'marzpay-provider-mtn',
        'airtel' => 'marzpay-provider-airtel'
    );
    
    $class = isset( $provider_classes[ $provider ] ) ? $provider_classes[ $provider ] : 'marzpay-provider-unknown';
    $label = strtoupper( $provider );
    
    return '<span class="marzpay-provider-badge ' . esc_attr( $class ) . '">' . esc_html( $label ) . '</span>';
}

/**
 * Log MarzPay activity
 */
function marzpay_log( $message, $level = 'info' ) {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
        error_log( sprintf( '[MarzPay %s] %s', strtoupper( $level ), $message ) );
    }
}

/**
 * Get webhook URL for a specific type
 */
function marzpay_get_webhook_url( $type ) {
    return home_url( 'marzpay-webhook/' . $type );
}

/**
 * Check if MarzPay is configured
 */
function marzpay_is_configured() {
    $api_user = get_option( 'marzpay_api_user' );
    $api_key = get_option( 'marzpay_api_key' );
    
    return ! empty( $api_user ) && ! empty( $api_key );
}

/**
 * Get MarzPay environment
 */
function marzpay_get_environment() {
    return get_option( 'marzpay_environment', 'test' );
}

/**
 * Check if we're in test mode
 */
function marzpay_is_test_mode() {
    return marzpay_get_environment() === 'test';
}

/**
 * Get default currency
 */
function marzpay_get_default_currency() {
    return get_option( 'marzpay_default_currency', 'UGX' );
}

/**
 * Get default country
 */
function marzpay_get_default_country() {
    return get_option( 'marzpay_default_country', 'UG' );
}

/**
 * Generate unique reference
 */
function marzpay_generate_reference( $prefix = 'order' ) {
    return $prefix . '_' . uniqid() . '_' . time();
}

/**
 * Sanitize phone number for display
 */
function marzpay_sanitize_phone_display( $phone ) {
    $phone = preg_replace( '/[^0-9]/', '', $phone );
    
    if ( strlen( $phone ) === 12 && substr( $phone, 0, 3 ) === '256' ) {
        return substr( $phone, 3, 3 ) . ' ' . substr( $phone, 6, 3 ) . ' ' . substr( $phone, 9, 3 );
    } elseif ( strlen( $phone ) === 9 ) {
        return substr( $phone, 0, 3 ) . ' ' . substr( $phone, 3, 3 ) . ' ' . substr( $phone, 6, 3 );
    }
    
    return $phone;
}

/**
 * Get transaction by reference
 */
function marzpay_get_transaction_by_reference( $reference ) {
    $database = marzpay_get_database();
    return $database->get_transaction( $reference );
}

/**
 * Update transaction status
 */
function marzpay_update_transaction_status( $uuid, $status, $metadata = array() ) {
    $database = marzpay_get_database();
    
    $update_data = array(
        'status' => $status,
        'updated_at' => current_time( 'mysql' )
    );
    
    if ( ! empty( $metadata ) ) {
        $update_data['metadata'] = $metadata;
    }
    
    return $database->update_transaction( $uuid, $update_data );
}

/**
 * Get transaction statistics
 */
function marzpay_get_transaction_stats( $args = array() ) {
    $database = marzpay_get_database();
    
    $defaults = array(
        'start_date' => date( 'Y-m-01' ), // First day of current month
        'end_date' => date( 'Y-m-t' )     // Last day of current month
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    $total = $database->get_transaction_count( $args );
    $successful = $database->get_transaction_count( array_merge( $args, array( 'status' => 'successful' ) ) );
    $failed = $database->get_transaction_count( array_merge( $args, array( 'status' => 'failed' ) ) );
    $pending = $database->get_transaction_count( array_merge( $args, array( 'status' => 'pending' ) ) );
    
    return array(
        'total' => $total,
        'successful' => $successful,
        'failed' => $failed,
        'pending' => $pending,
        'success_rate' => $total > 0 ? round( ( $successful / $total ) * 100, 2 ) : 0
    );
}

/**
 * Get total amount for transactions
 */
function marzpay_get_total_amount( $args = array() ) {
    global $wpdb;
    
    $table = $wpdb->prefix . 'marzpay_transactions';
    
    $where_conditions = array();
    $where_values = array();
    
    if ( ! empty( $args['status'] ) ) {
        $where_conditions[] = 'status = %s';
        $where_values[] = $args['status'];
    }
    
    if ( ! empty( $args['type'] ) ) {
        $where_conditions[] = 'type = %s';
        $where_values[] = $args['type'];
    }
    
    if ( ! empty( $args['start_date'] ) ) {
        $where_conditions[] = 'created_at >= %s';
        $where_values[] = $args['start_date'];
    }
    
    if ( ! empty( $args['end_date'] ) ) {
        $where_conditions[] = 'created_at <= %s';
        $where_values[] = $args['end_date'];
    }
    
    $where_clause = '';
    if ( ! empty( $where_conditions ) ) {
        $where_clause = 'WHERE ' . implode( ' AND ', $where_conditions );
    }
    
    $sql = "SELECT SUM(amount) FROM $table $where_clause";
    
    if ( ! empty( $where_values ) ) {
        $sql = $wpdb->prepare( $sql, $where_values );
    }
    
    return $wpdb->get_var( $sql ) ?: 0;
}

/**
 * Send notification email
 */
function marzpay_send_notification_email( $to, $subject, $message, $headers = array() ) {
    $default_headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>'
    );
    
    $headers = array_merge( $default_headers, $headers );
    
    return wp_mail( $to, $subject, $message, $headers );
}

/**
 * Get success page URL
 */
function marzpay_get_success_url( $transaction_id = null ) {
    $success_page = get_option( 'marzpay_success_page' );
    
    if ( $success_page ) {
        $url = get_permalink( $success_page );
    } else {
        $url = home_url( '/marzpay-success/' );
    }
    
    if ( $transaction_id ) {
        $url = add_query_arg( 'transaction_id', $transaction_id, $url );
    }
    
    return $url;
}

/**
 * Get failure page URL
 */
function marzpay_get_failure_url( $transaction_id = null, $error_message = null ) {
    $failure_page = get_option( 'marzpay_failure_page' );
    
    if ( $failure_page ) {
        $url = get_permalink( $failure_page );
    } else {
        $url = home_url( '/marzpay-failure/' );
    }
    
    if ( $transaction_id ) {
        $url = add_query_arg( 'transaction_id', $transaction_id, $url );
    }
    
    if ( $error_message ) {
        $url = add_query_arg( 'error', urlencode( $error_message ), $url );
    }
    
    return $url;
}

/**
 * Check if current user can manage MarzPay
 */
function marzpay_current_user_can_manage() {
    return current_user_can( 'manage_options' );
}

/**
 * Get MarzPay plugin version
 */
function marzpay_get_version() {
    return MARZPAY_VERSION;
}

/**
 * Get MarzPay plugin directory URL
 */
function marzpay_get_plugin_url() {
    return MARZPAY_PLUGIN_URL;
}

/**
 * Get MarzPay plugin directory path
 */
function marzpay_get_plugin_dir() {
    return MARZPAY_PLUGIN_DIR;
}

// WooCommerce AJAX handlers
add_action( 'wp_ajax_marzpay_check_woocommerce_status', 'marzpay_ajax_check_woocommerce_status' );
add_action( 'wp_ajax_marzpay_refresh_all_statuses', 'marzpay_ajax_refresh_all_statuses' );
add_action( 'wp_ajax_marzpay_get_order_status', 'marzpay_ajax_get_order_status' );

/**
 * AJAX handler for checking WooCommerce payment status
 */
function marzpay_ajax_check_woocommerce_status() {
    check_ajax_referer( 'marzpay_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }
    
    $uuid = sanitize_text_field( $_POST['uuid'] );
    $order_id = isset( $_POST['order_id'] ) ? intval( $_POST['order_id'] ) : 0;
    
    if ( ! $uuid ) {
        wp_send_json_error( array( 'message' => 'Invalid transaction UUID' ) );
    }
    
    $api_client = MarzPay_API_Client::get_instance();
    $result = $api_client->get_transaction( $uuid );
    
    if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
        $transaction = $result['data']['transaction'] ?? array();
        $status = $transaction['status'] ?? '';
        
        if ( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( $order ) {
                $order->update_meta_data( '_marzpay_status', $status );
                $order->save();
                
                // Update order status based on payment status
                switch ( $status ) {
                    case 'successful':
                    case 'completed':
                        $order->payment_complete( $uuid );
                        break;
                    case 'failed':
                    case 'cancelled':
                        $order->update_status( 'failed' );
                        break;
                }
            }
        }
        
        wp_send_json_success( array( 'status' => $status ) );
    } else {
        wp_send_json_error( array( 'message' => 'Failed to check payment status' ) );
    }
}

/**
 * AJAX handler for refreshing all payment statuses
 */
function marzpay_ajax_refresh_all_statuses() {
    check_ajax_referer( 'marzpay_nonce', 'nonce' );
    
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
    }
    
    $orders = wc_get_orders( array(
        'payment_method' => 'marzpay',
        'limit' => 100,
        'status' => array( 'pending', 'processing' )
    ) );
    
    $checked = 0;
    $api_client = MarzPay_API_Client::get_instance();
    
    foreach ( $orders as $order ) {
        $transaction_uuid = $order->get_meta( '_marzpay_transaction_uuid' );
        
        if ( $transaction_uuid ) {
            $result = $api_client->get_transaction( $transaction_uuid );
            
            if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
                $transaction = $result['data']['transaction'] ?? array();
                $status = $transaction['status'] ?? '';
                
                $order->update_meta_data( '_marzpay_status', $status );
                $order->save();
                
                // Update order status based on payment status
                switch ( $status ) {
                    case 'successful':
                    case 'completed':
                        $order->payment_complete( $transaction_uuid );
                        break;
                    case 'failed':
                    case 'cancelled':
                        $order->update_status( 'failed' );
                        break;
                }
                
                $checked++;
            }
        }
    }
    
    wp_send_json_success( array( 'checked' => $checked ) );
}

/**
 * AJAX handler for getting order status (for order received page)
 */
function marzpay_ajax_get_order_status() {
    check_ajax_referer( 'marzpay_nonce', 'nonce' );
    
    $order_id = intval( $_POST['order_id'] );
    
    if ( ! $order_id ) {
        wp_send_json_error( array( 'message' => 'Invalid order ID' ) );
    }
    
    $order = wc_get_order( $order_id );
    
    if ( ! $order || $order->get_payment_method() !== 'marzpay' ) {
        wp_send_json_error( array( 'message' => 'Order not found or not paid with MarzPay' ) );
    }
    
    $status = $order->get_meta( '_marzpay_status' );
    
    wp_send_json_success( array( 'status' => $status ) );
}
