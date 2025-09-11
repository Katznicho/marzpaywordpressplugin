<?php
/**
 * MarzPay Webhooks Class
 * 
 * Handles webhook endpoints and processing
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_Webhooks {
    
    private static $instance = null;
    private $database;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->database = MarzPay_Database::get_instance();
        $this->init_hooks();
    }
    
    private function init_hooks() {
        // Add rewrite rules for webhook endpoints
        add_action( 'init', array( $this, 'add_rewrite_rules' ) );
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
        add_action( 'template_redirect', array( $this, 'handle_webhook_requests' ) );
        
        // AJAX handlers for webhook management
        add_action( 'wp_ajax_marzpay_create_webhook', array( $this, 'ajax_create_webhook' ) );
        add_action( 'wp_ajax_marzpay_update_webhook', array( $this, 'ajax_update_webhook' ) );
        add_action( 'wp_ajax_marzpay_delete_webhook', array( $this, 'ajax_delete_webhook' ) );
        add_action( 'wp_ajax_marzpay_test_webhook', array( $this, 'ajax_test_webhook' ) );
    }
    
    /**
     * Add rewrite rules for webhook endpoints
     */
    public function add_rewrite_rules() {
        add_rewrite_rule(
            '^marzpay-webhook/([^/]+)/?$',
            'index.php?marzpay_webhook=1&webhook_type=$matches[1]',
            'top'
        );
    }
    
    /**
     * Add query vars for webhook handling
     */
    public function add_query_vars( $vars ) {
        $vars[] = 'marzpay_webhook';
        $vars[] = 'webhook_type';
        return $vars;
    }
    
    /**
     * Handle webhook requests
     */
    public function handle_webhook_requests() {
        if ( get_query_var( 'marzpay_webhook' ) ) {
            $webhook_type = get_query_var( 'webhook_type' );
            $this->process_webhook( $webhook_type );
            exit;
        }
    }
    
    /**
     * Process incoming webhook
     */
    private function process_webhook( $webhook_type ) {
        // Verify webhook signature if configured
        if ( ! $this->verify_webhook_signature() ) {
            http_response_code( 401 );
            echo json_encode( array( 'error' => 'Unauthorized' ) );
            return;
        }
        
        // Get request data
        $input = file_get_contents( 'php://input' );
        $data = json_decode( $input, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            http_response_code( 400 );
            echo json_encode( array( 'error' => 'Invalid JSON' ) );
            return;
        }
        
        // Log the webhook
        $this->log_webhook_received( $webhook_type, $data );
        
        // Process based on webhook type
        switch ( $webhook_type ) {
            case 'airtel':
                $this->handle_airtel_webhook( $data );
                break;
            case 'mtn':
                $this->handle_mtn_webhook( $data );
                break;
            case 'mtn-disbursement':
                $this->handle_mtn_disbursement_webhook( $data );
                break;
            default:
                http_response_code( 404 );
                echo json_encode( array( 'error' => 'Webhook type not found' ) );
                return;
        }
        
        // Send success response
        http_response_code( 200 );
        echo json_encode( array( 'status' => 'success', 'message' => 'Webhook processed' ) );
    }
    
    /**
     * Handle Airtel webhook
     */
    private function handle_airtel_webhook( $data ) {
        // Extract transaction information
        $transaction_id = isset( $data['id'] ) ? $data['id'] : null;
        $status_code = isset( $data['status_code'] ) ? $data['status_code'] : null;
        $message = isset( $data['message'] ) ? $data['message'] : null;
        
        if ( $transaction_id ) {
            // Update transaction status in database
            $this->update_transaction_from_webhook( $transaction_id, $status_code, $message, 'airtel' );
            
            // Trigger webhook notifications
            $this->trigger_webhook_notifications( 'collection.completed', array(
                'transaction_id' => $transaction_id,
                'provider' => 'airtel',
                'status' => $status_code,
                'message' => $message
            ));
        }
    }
    
    /**
     * Handle MTN webhook
     */
    private function handle_mtn_webhook( $data ) {
        // Extract transaction information
        $transaction_id = isset( $data['transaction_id'] ) ? $data['transaction_id'] : null;
        $status = isset( $data['status'] ) ? $data['status'] : null;
        $message = isset( $data['message'] ) ? $data['message'] : null;
        
        if ( $transaction_id ) {
            // Update transaction status in database
            $this->update_transaction_from_webhook( $transaction_id, $status, $message, 'mtn' );
            
            // Trigger webhook notifications
            $this->trigger_webhook_notifications( 'collection.completed', array(
                'transaction_id' => $transaction_id,
                'provider' => 'mtn',
                'status' => $status,
                'message' => $message
            ));
        }
    }
    
    /**
     * Handle MTN disbursement webhook
     */
    private function handle_mtn_disbursement_webhook( $data ) {
        // Extract transaction information
        $transaction_id = isset( $data['transaction_id'] ) ? $data['transaction_id'] : null;
        $status = isset( $data['status'] ) ? $data['status'] : null;
        $message = isset( $data['message'] ) ? $data['message'] : null;
        
        if ( $transaction_id ) {
            // Update transaction status in database
            $this->update_transaction_from_webhook( $transaction_id, $status, $message, 'mtn', 'withdrawal' );
            
            // Trigger webhook notifications
            $this->trigger_webhook_notifications( 'withdrawal.completed', array(
                'transaction_id' => $transaction_id,
                'provider' => 'mtn',
                'status' => $status,
                'message' => $message
            ));
        }
    }
    
    /**
     * Update transaction from webhook data
     */
    private function update_transaction_from_webhook( $transaction_id, $status, $message, $provider, $type = 'collection' ) {
        // Find transaction by reference or provider reference
        $transaction = $this->find_transaction_by_reference( $transaction_id );
        
        if ( $transaction ) {
            $update_data = array(
                'status' => $this->map_webhook_status( $status ),
                'provider_reference' => $transaction_id,
                'metadata' => array(
                    'webhook_status' => $status,
                    'webhook_message' => $message,
                    'webhook_provider' => $provider,
                    'webhook_received_at' => current_time( 'mysql' )
                )
            );
            
            $this->database->update_transaction( $transaction['uuid'], $update_data );
        }
    }
    
    /**
     * Find transaction by reference
     */
    private function find_transaction_by_reference( $reference ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_transactions';
        
        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE reference = %s OR provider_reference = %s",
            $reference,
            $reference
        ), ARRAY_A );
    }
    
    /**
     * Map webhook status to internal status
     */
    private function map_webhook_status( $webhook_status ) {
        $status_map = array(
            'success' => 'successful',
            'failed' => 'failed',
            'pending' => 'processing',
            'cancelled' => 'cancelled'
        );
        
        return isset( $status_map[ $webhook_status ] ) ? $status_map[ $webhook_status ] : 'processing';
    }
    
    /**
     * Trigger webhook notifications
     */
    private function trigger_webhook_notifications( $event_type, $data ) {
        $webhooks = $this->database->get_webhooks( array(
            'event_type' => $event_type,
            'is_active' => 1
        ) );
        
        foreach ( $webhooks as $webhook ) {
            $this->send_webhook_notification( $webhook, $data );
        }
    }
    
    /**
     * Send webhook notification
     */
    private function send_webhook_notification( $webhook, $data ) {
        $payload = array(
            'event_type' => $webhook['event_type'],
            'timestamp' => current_time( 'c' ),
            'data' => $data
        );
        
        $response = wp_remote_post( $webhook['url'], array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-MarzPay-Signature' => $this->generate_webhook_signature( $payload )
            ),
            'body' => json_encode( $payload ),
            'timeout' => 30
        ) );
        
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        
        // Log webhook attempt
        $this->database->log_webhook_attempt(
            $webhook['id'],
            isset( $data['transaction_id'] ) ? $data['transaction_id'] : null,
            $webhook['event_type'],
            $payload,
            $response_code,
            $response_body,
            $response_code >= 200 && $response_code < 300 ? 'success' : 'failed'
        );
        
        // Update webhook last triggered info
        if ( $response_code >= 200 && $response_code < 300 ) {
            global $wpdb;
            $table = $wpdb->prefix . 'marzpay_webhooks';
            $wpdb->update(
                $table,
                array(
                    'last_triggered_at' => current_time( 'mysql' ),
                    'last_response_code' => $response_code,
                    'last_response_body' => $response_body
                ),
                array( 'id' => $webhook['id'] )
            );
        }
    }
    
    /**
     * Generate webhook signature
     */
    private function generate_webhook_signature( $payload ) {
        $secret = get_option( 'marzpay_webhook_secret' );
        return 'sha256=' . hash_hmac( 'sha256', json_encode( $payload ), $secret );
    }
    
    /**
     * Verify webhook signature
     */
    private function verify_webhook_signature() {
        $signature = isset( $_SERVER['HTTP_X_MARZPAY_SIGNATURE'] ) ? $_SERVER['HTTP_X_MARZPAY_SIGNATURE'] : '';
        $secret = get_option( 'marzpay_webhook_secret' );
        
        if ( empty( $secret ) ) {
            return true; // Skip verification if no secret is set
        }
        
        $payload = file_get_contents( 'php://input' );
        $expected_signature = 'sha256=' . hash_hmac( 'sha256', $payload, $secret );
        
        return hash_equals( $expected_signature, $signature );
    }
    
    /**
     * Log webhook received
     */
    private function log_webhook_received( $webhook_type, $data ) {
        // Log to WordPress debug log
        error_log( sprintf(
            'MarzPay Webhook Received: %s - %s',
            $webhook_type,
            json_encode( $data )
        ) );
    }
    
    /**
     * AJAX: Create webhook
     */
    public function ajax_create_webhook() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        
        $name = sanitize_text_field( $_POST['name'] );
        $url = esc_url_raw( $_POST['url'] );
        $event_type = sanitize_text_field( $_POST['event_type'] );
        $environment = sanitize_text_field( $_POST['environment'] );
        $is_active = isset( $_POST['is_active'] ) ? 1 : 0;
        
        $webhook_id = $this->database->insert_webhook( array(
            'name' => $name,
            'url' => $url,
            'event_type' => $event_type,
            'environment' => $environment,
            'is_active' => $is_active
        ) );
        
        if ( $webhook_id ) {
            wp_send_json_success( array( 'message' => 'Webhook created successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to create webhook' ) );
        }
    }
    
    /**
     * AJAX: Update webhook
     */
    public function ajax_update_webhook() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        
        $webhook_id = intval( $_POST['webhook_id'] );
        $name = sanitize_text_field( $_POST['name'] );
        $url = esc_url_raw( $_POST['url'] );
        $event_type = sanitize_text_field( $_POST['event_type'] );
        $environment = sanitize_text_field( $_POST['environment'] );
        $is_active = isset( $_POST['is_active'] ) ? 1 : 0;
        
        global $wpdb;
        $table = $wpdb->prefix . 'marzpay_webhooks';
        
        $result = $wpdb->update(
            $table,
            array(
                'name' => $name,
                'url' => $url,
                'event_type' => $event_type,
                'environment' => $environment,
                'is_active' => $is_active,
                'updated_at' => current_time( 'mysql' )
            ),
            array( 'id' => $webhook_id )
        );
        
        if ( $result !== false ) {
            wp_send_json_success( array( 'message' => 'Webhook updated successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to update webhook' ) );
        }
    }
    
    /**
     * AJAX: Delete webhook
     */
    public function ajax_delete_webhook() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        
        $webhook_id = intval( $_POST['webhook_id'] );
        
        global $wpdb;
        $table = $wpdb->prefix . 'marzpay_webhooks';
        
        $result = $wpdb->delete( $table, array( 'id' => $webhook_id ) );
        
        if ( $result ) {
            wp_send_json_success( array( 'message' => 'Webhook deleted successfully' ) );
        } else {
            wp_send_json_error( array( 'message' => 'Failed to delete webhook' ) );
        }
    }
    
    /**
     * AJAX: Test webhook
     */
    public function ajax_test_webhook() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        
        $webhook_id = intval( $_POST['webhook_id'] );
        
        global $wpdb;
        $table = $wpdb->prefix . 'marzpay_webhooks';
        
        $webhook = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $webhook_id
        ), ARRAY_A );
        
        if ( ! $webhook ) {
            wp_send_json_error( array( 'message' => 'Webhook not found' ) );
        }
        
        $test_data = array(
            'event_type' => $webhook['event_type'],
            'timestamp' => current_time( 'c' ),
            'data' => array(
                'test' => true,
                'message' => 'This is a test webhook from MarzPay WordPress plugin'
            )
        );
        
        $response = wp_remote_post( $webhook['url'], array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'X-MarzPay-Signature' => $this->generate_webhook_signature( $test_data )
            ),
            'body' => json_encode( $test_data ),
            'timeout' => 30
        ) );
        
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        
        if ( $response_code >= 200 && $response_code < 300 ) {
            wp_send_json_success( array(
                'message' => 'Test webhook sent successfully',
                'response_code' => $response_code,
                'response_body' => $response_body
            ) );
        } else {
            wp_send_json_error( array(
                'message' => 'Test webhook failed',
                'response_code' => $response_code,
                'response_body' => $response_body
            ) );
        }
    }
}
