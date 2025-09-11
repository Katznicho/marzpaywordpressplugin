<?php
/**
 * MarzPay API Client Class
 * 
 * Handles all API communications with the MarzPay API
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_API_Client {
    
    private static $instance = null;
    private $api_user;
    private $api_key;
    private $base_url;
    private $environment;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->api_user = get_option( 'marzpay_api_user' );
        $this->api_key = get_option( 'marzpay_api_key' );
        $this->environment = get_option( 'marzpay_environment', 'test' );
        $this->base_url = MARZPAY_API_BASE_URL;
    }
    
    /**
     * Make HTTP request to MarzPay API
     */
    private function make_request( $endpoint, $method = 'GET', $data = null ) {
        if ( empty( $this->api_user ) || empty( $this->api_key ) ) {
            return array(
                'status' => 'error',
                'message' => 'API credentials not configured'
            );
        }
        
        $url = $this->base_url . $endpoint;
        $headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode( $this->api_user . ':' . $this->api_key )
        );
        
        $args = array(
            'headers' => $headers,
            'timeout' => 45,
            'method' => $method
        );
        
        if ( $data && in_array( $method, array( 'POST', 'PUT', 'PATCH' ) ) ) {
            $args['body'] = json_encode( $data );
            
            // Debug: Log the request data
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'MarzPay API Request - URL: ' . $url );
                error_log( 'MarzPay API Request - Method: ' . $method );
                error_log( 'MarzPay API Request - Data: ' . $args['body'] );
            }
        }
        
        $response = wp_remote_request( $url, $args );
        
        if ( is_wp_error( $response ) ) {
            return array(
                'status' => 'error',
                'message' => $response->get_error_message()
            );
        }
        
        $body = wp_remote_retrieve_body( $response );
        $status_code = wp_remote_retrieve_response_code( $response );
        
        // Debug: Log the response
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'MarzPay API Response - Status Code: ' . $status_code );
            error_log( 'MarzPay API Response - Body: ' . $body );
        }
        
        $decoded_body = json_decode( $body, true );
        
        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return array(
                'status' => 'error',
                'message' => 'Invalid JSON response from API'
            );
        }
        
        return $decoded_body;
    }
    
    /**
     * Account Management
     */
    
    public function get_account() {
        return $this->make_request( '/v1/account' );
    }
    
    public function update_account( $data ) {
        return $this->make_request( '/v1/account', 'PUT', $data );
    }
    
    /**
     * Balance Management
     */
    
    public function get_balance() {
        return $this->make_request( '/v1/balance' );
    }
    
    public function get_balance_history( $params = array() ) {
        $query_string = '';
        if ( ! empty( $params ) ) {
            $query_string = '?' . http_build_query( $params );
        }
        return $this->make_request( '/v1/balance/history' . $query_string );
    }
    
    /**
     * Collection/Payment Requests
     */
    
    public function collect_money( $data ) {
        return $this->make_request( '/v1/collect-money', 'POST', $data );
    }
    
    public function get_collection_services() {
        return $this->make_request( '/v1/collect-money/services' );
    }
    
    public function get_collection( $uuid ) {
        return $this->make_request( '/v1/collect-money/' . $uuid );
    }
    
    /**
     * Withdrawal/Disbursement
     */
    
    public function send_money( $data ) {
        return $this->make_request( '/v1/send-money', 'POST', $data );
    }
    
    public function get_withdrawal_services() {
        return $this->make_request( '/v1/send-money/services' );
    }
    
    public function get_withdrawal( $uuid ) {
        return $this->make_request( '/v1/send-money/' . $uuid );
    }
    
    /**
     * Transaction Management
     */
    
    public function get_transactions( $params = array() ) {
        $query_string = '';
        if ( ! empty( $params ) ) {
            $query_string = '?' . http_build_query( $params );
        }
        return $this->make_request( '/v1/transactions' . $query_string );
    }
    
    public function get_transaction( $uuid ) {
        return $this->make_request( '/v1/transactions/' . $uuid );
    }
    
    /**
     * Services Management
     */
    
    public function get_services( $params = array() ) {
        $query_string = '';
        if ( ! empty( $params ) ) {
            $query_string = '?' . http_build_query( $params );
        }
        return $this->make_request( '/v1/services' . $query_string );
    }
    
    public function get_service( $uuid ) {
        return $this->make_request( '/v1/services/' . $uuid );
    }
    
    /**
     * Webhook Management
     */
    
    public function get_webhooks( $params = array() ) {
        $query_string = '';
        if ( ! empty( $params ) ) {
            $query_string = '?' . http_build_query( $params );
        }
        return $this->make_request( '/v1/webhooks' . $query_string );
    }
    
    public function create_webhook( $data ) {
        return $this->make_request( '/v1/webhooks', 'POST', $data );
    }
    
    public function get_webhook( $uuid ) {
        return $this->make_request( '/v1/webhooks/' . $uuid );
    }
    
    public function update_webhook( $uuid, $data ) {
        return $this->make_request( '/v1/webhooks/' . $uuid, 'PUT', $data );
    }
    
    public function delete_webhook( $uuid ) {
        return $this->make_request( '/v1/webhooks/' . $uuid, 'DELETE' );
    }
    
    /**
     * Callback Handlers (for webhook endpoints)
     */
    
    public function handle_airtel_callback( $transaction_data ) {
        return $this->make_request( '/v1/airtel/airtelcallback', 'GET', $transaction_data );
    }
    
    public function handle_mtn_callback( $transaction_data ) {
        return $this->make_request( '/v1/mtn/mtncallback', 'GET', $transaction_data );
    }
    
    public function handle_mtn_disbursement_callback( $transaction_data ) {
        return $this->make_request( '/v1/mtn/disbursement-callback', 'GET', $transaction_data );
    }
    
    /**
     * Helper Methods
     */
    
    public function is_configured() {
        return ! empty( $this->api_user ) && ! empty( $this->api_key );
    }
    
    public function get_environment() {
        return $this->environment;
    }
    
    public function format_amount( $amount ) {
        return number_format( $amount, 0, '.', ',' );
    }
    
    public function validate_phone_number( $phone, $country = 'UG' ) {
        // Phone validation for Uganda only (UG is the only supported country)
        if ( $country !== 'UG' ) {
            return false; // Only Uganda is supported
        }
        
        // Remove all non-numeric characters except +
        $phone = preg_replace( '/[^0-9+]/', '', $phone );
        
        // Validate Uganda phone numbers
        // API expects format: +256XXXXXXXXX (with + sign)
        if ( strlen( $phone ) === 10 && substr( $phone, 0, 1 ) === '0' ) {
            // Convert 0759983853 to +256759983853
            return '+256' . substr( $phone, 1 );
        } elseif ( strlen( $phone ) === 13 && substr( $phone, 0, 4 ) === '+256' ) {
            // Already in correct format +256759983853
            return $phone;
        } elseif ( strlen( $phone ) === 12 && substr( $phone, 0, 3 ) === '256' ) {
            // Convert 256759983853 to +256759983853
            return '+' . $phone;
        }
        
        return false;
    }
    
    /**
     * Legacy method for backward compatibility
     */
    public function request_payment( $amount, $phone, $reference = null ) {
        $data = array(
            'amount' => (int) $amount,
            'phone_number' => $phone,
            'reference' => $reference ?: uniqid( 'order_' ),
            'country' => 'UG'
        );
        
        return $this->collect_money( $data );
    }
}

// Global function for backward compatibility
function marzpay_request_payment( $amount, $phone, $reference = null ) {
    $api_client = MarzPay_API_Client::get_instance();
    return $api_client->request_payment( $amount, $phone, $reference );
}
