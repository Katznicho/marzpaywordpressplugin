<?php
/**
 * MarzPay Shortcodes
 * 
 * Handles all shortcode functionality for collections and withdrawals
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'init', array( $this, 'register_shortcodes' ) );
        add_action( 'wp_ajax_marzpay_collect_money', array( $this, 'ajax_collect_money' ) );
        add_action( 'wp_ajax_marzpay_send_money', array( $this, 'ajax_send_money' ) );
        add_action( 'wp_ajax_nopriv_marzpay_collect_money', array( $this, 'ajax_collect_money' ) );
        add_action( 'wp_ajax_nopriv_marzpay_send_money', array( $this, 'ajax_send_money' ) );
    }
    
    /**
     * Register shortcodes
     */
    public function register_shortcodes() {
        add_shortcode( 'marzpay_collect', array( $this, 'collect_money_shortcode' ) );
        add_shortcode( 'marzpay_send', array( $this, 'send_money_shortcode' ) );
        add_shortcode( 'marzpay_button', array( $this, 'payment_button_shortcode' ) ); // Legacy
        add_shortcode( 'marzpay_balance', array( $this, 'balance_shortcode' ) );
        add_shortcode( 'marzpay_transactions', array( $this, 'transactions_shortcode' ) );
    }
    
    /**
     * Collect money shortcode
     */
    public function collect_money_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'amount' => '',
            'phone' => '',
            'reference' => '',
            'description' => '',
            'callback_url' => '',
            'country' => 'UG',
            'button_text' => 'Request Payment',
            'show_form' => 'true',
            'form_style' => 'default'
        ), $atts, 'marzpay_collect' );
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            return '<div class="marzpay-error">API credentials not configured. Please contact the administrator.</div>';
        }
        
        ob_start();
        include MARZPAY_PLUGIN_DIR . 'templates/shortcode-collect.php';
        return ob_get_clean();
    }
    
    /**
     * Send money shortcode
     */
    public function send_money_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'amount' => '',
            'phone' => '',
            'reference' => '',
            'description' => '',
            'callback_url' => '',
            'country' => 'UG',
            'button_text' => 'Send Money',
            'show_form' => 'true',
            'form_style' => 'default'
        ), $atts, 'marzpay_send' );
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            return '<div class="marzpay-error">API credentials not configured. Please contact the administrator.</div>';
        }
        
        ob_start();
        include MARZPAY_PLUGIN_DIR . 'templates/shortcode-send.php';
        return ob_get_clean();
    }
    
    /**
     * Payment button shortcode (legacy)
     */
    public function payment_button_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'amount' => '1000',
            'phone' => '',
            'reference' => '',
            'description' => '',
            'button_text' => 'Pay Now'
        ), $atts, 'marzpay_button' );
        
        if ( empty( $atts['phone'] ) ) {
            return '<div class="marzpay-error">Phone number is required.</div>';
        }
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            return '<div class="marzpay-error">API credentials not configured. Please contact the administrator.</div>';
    }

    $output = '';

    // Handle form submission
        if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['marzpay_phone'] ) && $_POST['marzpay_phone'] === $atts['phone'] ) {
            $amount = sanitize_text_field( $_POST['marzpay_amount'] );
            $phone = sanitize_text_field( $_POST['marzpay_phone'] );
            $reference = sanitize_text_field( $_POST['marzpay_reference'] );
            $description = sanitize_text_field( $_POST['marzpay_description'] );
            
            $data = array(
                'amount' => (int) $amount,
                'phone_number' => $phone,
                'reference' => $reference ?: uniqid( 'order_' ),
                'description' => $description,
                'country' => 'UG'
            );
            
            $result = $api_client->collect_money( $data );
            
            if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
                $output .= '<div class="marzpay-success">✅ Payment request sent successfully to ' . esc_html( $phone ) . '.</div>';
                
                // Store transaction in database
                $database = MarzPay_Database::get_instance();
                $database->insert_transaction( array(
                    'uuid' => $result['data']['transaction']['uuid'],
                    'reference' => $result['data']['transaction']['reference'],
                    'type' => 'collection',
                    'status' => $result['data']['transaction']['status'],
                    'amount' => $amount,
                    'phone_number' => $phone,
                    'description' => $description,
                    'provider' => $result['data']['collection']['provider'],
                    'metadata' => $result
                ));
            } else {
                $error = isset( $result['message'] ) ? $result['message'] : 'Payment request failed.';
                $output .= '<div class="marzpay-error">❌ ' . esc_html( $error ) . '</div>';
            }
        }
        
        // Display the payment form
        $output .= '<form method="post" class="marzpay-form">';
        $output .= '<input type="hidden" name="marzpay_phone" value="' . esc_attr( $atts['phone'] ) . '">';
        $output .= '<input type="hidden" name="marzpay_amount" value="' . esc_attr( $atts['amount'] ) . '">';
        $output .= '<input type="hidden" name="marzpay_reference" value="' . esc_attr( $atts['reference'] ) . '">';
        $output .= '<input type="hidden" name="marzpay_description" value="' . esc_attr( $atts['description'] ) . '">';
        $output .= '<button type="submit" class="marzpay-button">' . esc_html( $atts['button_text'] ) . ' UGX ' . esc_html( $atts['amount'] ) . '</button>';
        $output .= '</form>';
        
        return $output;
    }
    
    /**
     * Balance shortcode
     */
    public function balance_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'show_currency' => 'true',
            'format' => 'formatted'
        ), $atts, 'marzpay_balance' );
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            return '<div class="marzpay-error">API credentials not configured.</div>';
        }
        
        $balance = $api_client->get_balance();
        
        if ( isset( $balance['status'] ) && $balance['status'] === 'success' ) {
            $balance_data = $balance['data']['account']['balance'];
            
            if ( $atts['format'] === 'raw' ) {
                $amount = $balance_data['raw'];
            } else {
                $amount = $balance_data['formatted'];
            }
            
            $output = '<div class="marzpay-balance">';
            $output .= '<span class="marzpay-amount">' . esc_html( $amount ) . '</span>';
            
            if ( $atts['show_currency'] === 'true' ) {
                $output .= ' <span class="marzpay-currency">' . esc_html( $balance_data['currency'] ) . '</span>';
            }
            
            $output .= '</div>';
            
            return $output;
        } else {
            return '<div class="marzpay-error">Unable to fetch balance.</div>';
        }
    }
    
    /**
     * Transactions shortcode
     */
    public function transactions_shortcode( $atts ) {
        $atts = shortcode_atts( array(
            'limit' => '10',
            'status' => '',
            'type' => '',
            'show_pagination' => 'false'
        ), $atts, 'marzpay_transactions' );
        
        $database = MarzPay_Database::get_instance();
        
        $args = array(
            'limit' => intval( $atts['limit'] ),
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        if ( ! empty( $atts['status'] ) ) {
            $args['status'] = $atts['status'];
        }
        
        if ( ! empty( $atts['type'] ) ) {
            $args['type'] = $atts['type'];
        }
        
        $transactions = $database->get_transactions( $args );
        
        if ( empty( $transactions ) ) {
            return '<div class="marzpay-no-transactions">No transactions found.</div>';
        }
        
        ob_start();
        include MARZPAY_PLUGIN_DIR . 'templates/shortcode-transactions.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX: Collect money
     */
    public function ajax_collect_money() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        $amount = intval( $_POST['amount'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $reference = sanitize_text_field( $_POST['reference'] );
        $description = sanitize_text_field( $_POST['description'] );
        $callback_url = esc_url_raw( $_POST['callback_url'] );
        $country = sanitize_text_field( $_POST['country'] );
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            wp_send_json_error( array( 'message' => 'API credentials not configured' ) );
        }
        
        // Validate phone number
        $validated_phone = $api_client->validate_phone_number( $phone, $country );
        if ( ! $validated_phone ) {
            wp_send_json_error( array( 'message' => 'Invalid phone number format' ) );
        }
        
        $data = array(
            'amount' => $amount,
            'phone_number' => $validated_phone,
            'reference' => $reference ?: uniqid( 'order_' ),
            'description' => $description,
            'callback_url' => $callback_url,
            'country' => $country
        );
        
        $result = $api_client->collect_money( $data );
        
        // Debug: Log the API response structure
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( 'MarzPay API Response: ' . wp_json_encode( $result, JSON_PRETTY_PRINT ) );
        }
        
        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
            // Only store transaction if API returned a proper UUID
            $api_uuid = $result['data']['transaction']['uuid'] ?? $result['data']['uuid'] ?? null;
            
            if ( $api_uuid ) {
                // Store transaction in database with API-generated UUID
                $database = MarzPay_Database::get_instance();
                
                $transaction_data = array(
                    'uuid' => $api_uuid,
                    'reference' => $result['data']['transaction']['reference'] ?? $result['data']['reference'] ?? $reference,
                    'type' => 'collection',
                    'status' => $result['data']['transaction']['status'] ?? $result['data']['status'] ?? 'pending',
                    'amount' => $amount,
                    'phone_number' => $validated_phone,
                    'description' => $description,
                    'callback_url' => $callback_url,
                    'provider' => $result['data']['collection']['provider'] ?? $result['data']['provider'] ?? 'unknown',
                    'metadata' => $result
                );
                
                $insert_result = $database->insert_transaction( $transaction_data );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'MarzPay Database Insert Result: ' . ( $insert_result ? 'Success' : 'Failed' ) );
                }
            } else {
                // Log warning if API didn't return UUID
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'MarzPay Warning: API response missing UUID, transaction not stored locally' );
                }
            }
            
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
    
    /**
     * AJAX: Send money
     */
    public function ajax_send_money() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        $amount = intval( $_POST['amount'] );
        $phone = sanitize_text_field( $_POST['phone'] );
        $reference = sanitize_text_field( $_POST['reference'] );
        $description = sanitize_text_field( $_POST['description'] );
        $callback_url = esc_url_raw( $_POST['callback_url'] );
        $country = sanitize_text_field( $_POST['country'] );
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            wp_send_json_error( array( 'message' => 'API credentials not configured' ) );
        }
        
        // Validate phone number
        $validated_phone = $api_client->validate_phone_number( $phone, $country );
        if ( ! $validated_phone ) {
            wp_send_json_error( array( 'message' => 'Invalid phone number format' ) );
        }
        
        $data = array(
            'amount' => $amount,
            'phone_number' => $validated_phone,
            'reference' => $reference ?: uniqid( 'withdrawal_' ),
            'description' => $description,
            'callback_url' => $callback_url,
            'country' => $country
        );
        
        $result = $api_client->send_money( $data );
        
        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
            // Only store transaction if API returned a proper UUID
            $api_uuid = $result['data']['transaction']['uuid'] ?? $result['data']['uuid'] ?? null;
            
            if ( $api_uuid ) {
                // Store transaction in database with API-generated UUID
                $database = MarzPay_Database::get_instance();
                $database->insert_transaction( array(
                    'uuid' => $api_uuid,
                    'reference' => $result['data']['transaction']['reference'] ?? $result['data']['reference'] ?? $reference,
                    'type' => 'withdrawal',
                    'status' => $result['data']['transaction']['status'] ?? $result['data']['status'] ?? 'pending',
                    'amount' => $amount,
                    'phone_number' => $validated_phone,
                    'description' => $description,
                    'callback_url' => $callback_url,
                    'provider' => $result['data']['withdrawal']['provider'] ?? $result['data']['provider'] ?? 'unknown',
                    'metadata' => $result
                ));
            } else {
                // Log warning if API didn't return UUID
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'MarzPay Warning: Send money API response missing UUID, transaction not stored locally' );
                }
            }
            
            wp_send_json_success( $result );
        } else {
            wp_send_json_error( $result );
        }
    }
}

// Initialize shortcodes
MarzPay_Shortcodes::get_instance();
