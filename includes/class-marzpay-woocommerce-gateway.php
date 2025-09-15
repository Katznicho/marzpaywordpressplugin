<?php
/**
 * MarzPay WooCommerce Payment Gateway
 * 
 * Extends WooCommerce to accept mobile money payments via MarzPay API
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_WooCommerce_Gateway extends WC_Payment_Gateway {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Make sure WooCommerce is available
        if ( ! class_exists( 'WooCommerce' ) ) {
            return;
        }
        $this->id = 'marzpay';
        $this->icon = MARZPAY_PLUGIN_URL . 'assets/images/marzpay-logo.png';
        $this->has_fields = true;
        $this->method_title = 'MarzPay Mobile Money';
        $this->method_description = 'Accept mobile money payments via MarzPay API (Airtel & MTN Uganda)';
        
        // Load the settings
        $this->init_form_fields();
        $this->init_settings();
        
        // Define user set variables
        $this->title = $this->get_option( 'title' );
        $this->description = $this->get_option( 'description' );
        $this->enabled = $this->get_option( 'enabled' );
        $this->testmode = $this->get_option( 'testmode' );
        $this->api_key = $this->get_option( 'api_key' );
        $this->api_secret = $this->get_option( 'api_secret' );
        $this->phone_required = $this->get_option( 'phone_required' );
        $this->auto_complete = $this->get_option( 'auto_complete' );
        
        // Save settings
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        
        // Add custom fields to checkout
        add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_phone_field' ) );
        
        // Validate custom fields
        add_action( 'woocommerce_checkout_process', array( $this, 'validate_phone_field' ) );
        
        // Handle webhook
        add_action( 'woocommerce_api_marzpay_webhook', array( $this, 'handle_webhook' ) );
        
        // Add admin notice if API credentials are missing
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }
    
    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __( 'Enable/Disable', 'marzpay' ),
                'type' => 'checkbox',
                'label' => __( 'Enable MarzPay Mobile Money', 'marzpay' ),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __( 'Title', 'marzpay' ),
                'type' => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'marzpay' ),
                'default' => __( 'Mobile Money (Airtel & MTN)', 'marzpay' ),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __( 'Description', 'marzpay' ),
                'type' => 'textarea',
                'description' => __( 'This controls the description which the user sees during checkout.', 'marzpay' ),
                'default' => __( 'Pay securely with your mobile money account (Airtel Money or MTN Mobile Money).', 'marzpay' ),
                'desc_tip' => true,
            ),
            'testmode' => array(
                'title' => __( 'Test Mode', 'marzpay' ),
                'type' => 'checkbox',
                'label' => __( 'Enable test mode', 'marzpay' ),
                'default' => 'yes',
                'description' => __( 'Place the payment gateway in test mode using test API credentials.', 'marzpay' ),
            ),
            'api_key' => array(
                'title' => __( 'API Key', 'marzpay' ),
                'type' => 'text',
                'description' => __( 'Your MarzPay API key.', 'marzpay' ),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => 'your_api_key_here'
            ),
            'api_secret' => array(
                'title' => __( 'API Secret', 'marzpay' ),
                'type' => 'password',
                'description' => __( 'Your MarzPay API secret.', 'marzpay' ),
                'default' => '',
                'desc_tip' => true,
                'placeholder' => 'your_api_secret_here'
            ),
            'phone_required' => array(
                'title' => __( 'Phone Number Required', 'marzpay' ),
                'type' => 'checkbox',
                'label' => __( 'Require customer to enter phone number', 'marzpay' ),
                'default' => 'yes',
                'description' => __( 'If disabled, will use billing phone number from checkout form.', 'marzpay' ),
            ),
            'auto_complete' => array(
                'title' => __( 'Auto Complete Orders', 'marzpay' ),
                'type' => 'checkbox',
                'label' => __( 'Automatically complete orders on successful payment', 'marzpay' ),
                'default' => 'no',
                'description' => __( 'Automatically mark orders as completed when payment is successful.', 'marzpay' ),
            ),
            'webhook_url' => array(
                'title' => __( 'Webhook URL', 'marzpay' ),
                'type' => 'text',
                'description' => __( 'Copy this URL to your MarzPay webhook settings.', 'marzpay' ),
                'default' => home_url( '/wc-api/marzpay_webhook' ),
                'custom_attributes' => array( 'readonly' => 'readonly' ),
            ),
        );
    }
    
    /**
     * Add phone number field to checkout
     */
    public function add_phone_field( $checkout ) {
        if ( $this->phone_required === 'yes' ) {
            echo '<div id="marzpay_phone_field"><h3>' . __( 'Mobile Money Details', 'marzpay' ) . '</h3>';
            
            woocommerce_form_field( 'marzpay_phone', array(
                'type' => 'tel',
                'class' => array( 'form-row-wide' ),
                'label' => __( 'Mobile Money Phone Number', 'marzpay' ),
                'placeholder' => __( 'e.g., 256759983853 or 0759983853', 'marzpay' ),
                'required' => true,
                'description' => __( 'Enter the phone number linked to your mobile money account.', 'marzpay' ),
            ), $checkout->get_value( 'marzpay_phone' ) );
            
            echo '</div>';
        }
    }
    
    /**
     * Validate phone number field
     */
    public function validate_phone_field() {
        if ( $this->phone_required === 'yes' && empty( $_POST['marzpay_phone'] ) ) {
            wc_add_notice( __( 'Mobile money phone number is required.', 'marzpay' ), 'error' );
        }
    }
    
    /**
     * Process the payment and return the result
     */
    public function process_payment( $order_id ) {
        $order = wc_get_order( $order_id );
        
        if ( ! $order ) {
            return array(
                'result' => 'failure',
                'messages' => __( 'Order not found.', 'marzpay' )
            );
        }
        
        // Get phone number
        $phone = '';
        if ( $this->phone_required === 'yes' && ! empty( $_POST['marzpay_phone'] ) ) {
            $phone = sanitize_text_field( $_POST['marzpay_phone'] );
        } elseif ( ! empty( $order->get_billing_phone() ) ) {
            $phone = $order->get_billing_phone();
        } else {
            return array(
                'result' => 'failure',
                'messages' => __( 'Phone number is required for mobile money payment.', 'marzpay' )
            );
        }
        
        // Validate and format phone number
        $api_client = MarzPay_API_Client::get_instance();
        $formatted_phone = $api_client->validate_phone_number( $phone );
        
        if ( ! $formatted_phone ) {
            return array(
                'result' => 'failure',
                'messages' => __( 'Invalid phone number format. Please use format: 256759983853 or 0759983853', 'marzpay' )
            );
        }
        
        // Prepare payment data
        $amount = (int) $order->get_total();
        $reference = 'WC_' . $order_id . '_' . time();
        $description = sprintf( __( 'Payment for order #%s', 'marzpay' ), $order->get_order_number() );
        
        $payment_data = array(
            'amount' => $amount,
            'phone_number' => $formatted_phone,
            'reference' => $reference,
            'description' => $description,
            'country' => 'UG'
        );
        
        // Make API call
        $result = $api_client->collect_money( $payment_data );
        
        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
            // Payment request successful
            $transaction_data = $result['data']['transaction'] ?? array();
            $transaction_uuid = $transaction_data['uuid'] ?? '';
            
            // Update order with transaction details
            $order->add_meta_data( '_marzpay_transaction_uuid', $transaction_uuid );
            $order->add_meta_data( '_marzpay_reference', $reference );
            $order->add_meta_data( '_marzpay_phone', $formatted_phone );
            $order->add_meta_data( '_marzpay_status', 'pending' );
            $order->save();
            
            // Mark order as pending payment
            $order->update_status( 'pending', __( 'Awaiting mobile money payment confirmation.', 'marzpay' ) );
            
            // Add order note
            $order->add_order_note( sprintf( 
                __( 'MarzPay payment request sent. Transaction UUID: %s, Phone: %s, Amount: %s UGX', 'marzpay' ),
                $transaction_uuid,
                $formatted_phone,
                number_format( $amount )
            ) );
            
            // Return success
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
            
        } else {
            // Payment request failed
            $error_message = $result['message'] ?? __( 'Payment request failed. Please try again.', 'marzpay' );
            
            $order->add_order_note( sprintf( __( 'MarzPay payment request failed: %s', 'marzpay' ), $error_message ) );
            
            return array(
                'result' => 'failure',
                'messages' => $error_message
            );
        }
    }
    
    /**
     * Handle webhook from MarzPay
     */
    public function handle_webhook() {
        $input = file_get_contents( 'php://input' );
        $data = json_decode( $input, true );
        
        if ( ! $data || ! isset( $data['transaction'] ) ) {
            http_response_code( 400 );
            exit( 'Invalid webhook data' );
        }
        
        $transaction = $data['transaction'];
        $reference = $transaction['reference'] ?? '';
        $status = $transaction['status'] ?? '';
        $uuid = $transaction['uuid'] ?? '';
        
        // Find order by reference
        if ( strpos( $reference, 'WC_' ) === 0 ) {
            $order_id = str_replace( 'WC_', '', $reference );
            $order_id = explode( '_', $order_id )[0];
            
            $order = wc_get_order( $order_id );
            
            if ( $order ) {
                // Update order status based on payment status
                switch ( $status ) {
                    case 'successful':
                    case 'completed':
                        $order->payment_complete( $uuid );
                        $order->add_order_note( sprintf( 
                            __( 'MarzPay payment confirmed. Transaction UUID: %s', 'marzpay' ),
                            $uuid
                        ) );
                        
                        if ( $this->auto_complete === 'yes' ) {
                            $order->update_status( 'completed' );
                        }
                        break;
                        
                    case 'failed':
                    case 'cancelled':
                        $order->update_status( 'failed', __( 'MarzPay payment failed or was cancelled.', 'marzpay' ) );
                        $order->add_order_note( sprintf( 
                            __( 'MarzPay payment failed. Transaction UUID: %s', 'marzpay' ),
                            $uuid
                        ) );
                        break;
                        
                    case 'pending':
                    case 'processing':
                        // Keep as pending
                        $order->add_order_note( sprintf( 
                            __( 'MarzPay payment status updated to: %s. Transaction UUID: %s', 'marzpay' ),
                            $status,
                            $uuid
                        ) );
                        break;
                }
                
                // Update transaction status
                $order->update_meta_data( '_marzpay_status', $status );
                $order->save();
            }
        }
        
        http_response_code( 200 );
        exit( 'OK' );
    }
    
    /**
     * Admin notices
     */
    public function admin_notices() {
        if ( $this->enabled === 'yes' && ( empty( $this->api_key ) || empty( $this->api_secret ) ) ) {
            echo '<div class="notice notice-warning"><p>';
            echo sprintf( 
                __( 'MarzPay WooCommerce Gateway is enabled but API credentials are missing. Please <a href="%s">configure your API credentials</a>.', 'marzpay' ),
                admin_url( 'admin.php?page=wc-settings&tab=checkout&section=marzpay' )
            );
            echo '</p></div>';
        }
    }
    
    /**
     * Check if the gateway is available
     */
    public function is_available() {
        // Force enable for testing - always return true if enabled
        if ( $this->enabled === 'no' ) {
            return false;
        }
        
        // Always return true if enabled (for testing)
        return true;
    }
}
