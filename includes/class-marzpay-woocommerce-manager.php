<?php
/**
 * MarzPay WooCommerce Integration Manager
 * 
 * Handles WooCommerce integration, gateway registration, and admin features
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_WooCommerce_Manager {
    
    private static $instance = null;
    private $initialized = false;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
        add_action( 'woocommerce_init', array( $this, 'init' ) );
        add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
    }
    
    /**
     * Initialize WooCommerce integration
     */
    public function init() {
        if ( $this->initialized || ! $this->is_woocommerce_active() ) {
            return;
        }
        
        $this->initialized = true;
        
        // Add gateway to WooCommerce
        add_filter( 'woocommerce_payment_gateways', array( $this, 'add_gateway' ) );
        
        // Add admin menu for WooCommerce orders
        add_action( 'admin_menu', array( $this, 'add_woocommerce_menu' ) );
        
        // Add order meta boxes
        add_action( 'add_meta_boxes', array( $this, 'add_order_meta_boxes' ) );
        
        // Add custom columns to orders list
        add_filter( 'manage_shop_order_posts_columns', array( $this, 'add_order_columns' ) );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'display_order_columns' ), 10, 2 );
        
        // Add bulk actions
        add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions' ) );
        add_filter( 'handle_bulk_actions-edit-shop_order', array( $this, 'handle_bulk_actions' ), 10, 3 );
        
        // Add admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }
    
    /**
     * Check if WooCommerce is active
     */
    private function is_woocommerce_active() {
        return class_exists( 'WooCommerce' );
    }
    
    /**
     * Add MarzPay gateway to WooCommerce
     */
    public function add_gateway( $gateways ) {
        if ( class_exists( 'MarzPay_WooCommerce_Gateway' ) ) {
            $gateways[] = 'MarzPay_WooCommerce_Gateway';
        }
        return $gateways;
    }
    
    /**
     * Add WooCommerce admin menu
     */
    public function add_woocommerce_menu() {
        add_submenu_page(
            'marzpay-dashboard',
            __( 'WooCommerce Orders', 'marzpay' ),
            __( 'WooCommerce Orders', 'marzpay' ),
            'manage_options',
            'marzpay-woocommerce-orders',
            array( $this, 'woocommerce_orders_page' )
        );
    }
    
    /**
     * Add meta boxes to order edit page
     */
    public function add_order_meta_boxes() {
        add_meta_box(
            'marzpay-order-details',
            __( 'MarzPay Payment Details', 'marzpay' ),
            array( $this, 'order_meta_box_content' ),
            'shop_order',
            'side',
            'high'
        );
    }
    
    /**
     * Meta box content for order details
     */
    public function order_meta_box_content( $post ) {
        $order = wc_get_order( $post->ID );
        
        if ( ! $order || $order->get_payment_method() !== 'marzpay' ) {
            echo '<p>' . __( 'This order was not paid using MarzPay.', 'marzpay' ) . '</p>';
            return;
        }
        
        $transaction_uuid = $order->get_meta( '_marzpay_transaction_uuid' );
        $reference = $order->get_meta( '_marzpay_reference' );
        $phone = $order->get_meta( '_marzpay_phone' );
        $status = $order->get_meta( '_marzpay_status' );
        
        echo '<table class="widefat">';
        echo '<tr><td><strong>' . __( 'Transaction UUID:', 'marzpay' ) . '</strong></td><td>' . esc_html( $transaction_uuid ?: 'N/A' ) . '</td></tr>';
        echo '<tr><td><strong>' . __( 'Reference:', 'marzpay' ) . '</strong></td><td>' . esc_html( $reference ?: 'N/A' ) . '</td></tr>';
        echo '<tr><td><strong>' . __( 'Phone Number:', 'marzpay' ) . '</strong></td><td>' . esc_html( $phone ?: 'N/A' ) . '</td></tr>';
        echo '<tr><td><strong>' . __( 'Payment Status:', 'marzpay' ) . '</strong></td><td>' . esc_html( $status ?: 'N/A' ) . '</td></tr>';
        echo '</table>';
        
        if ( $transaction_uuid ) {
            echo '<p><a href="#" class="button button-secondary" id="check-payment-status" data-uuid="' . esc_attr( $transaction_uuid ) . '">' . __( 'Check Payment Status', 'marzpay' ) . '</a></p>';
        }
    }
    
    /**
     * Add custom columns to orders list
     */
    public function add_order_columns( $columns ) {
        $new_columns = array();
        
        foreach ( $columns as $key => $column ) {
            $new_columns[ $key ] = $column;
            
            if ( $key === 'order_status' ) {
                $new_columns['marzpay_status'] = __( 'MarzPay Status', 'marzpay' );
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Display custom column content
     */
    public function display_order_columns( $column, $post_id ) {
        if ( $column === 'marzpay_status' ) {
            $order = wc_get_order( $post_id );
            
            if ( $order && $order->get_payment_method() === 'marzpay' ) {
                $status = $order->get_meta( '_marzpay_status' );
                
                if ( $status ) {
                    $status_class = '';
                    switch ( $status ) {
                        case 'successful':
                        case 'completed':
                            $status_class = 'marzpay-status-success';
                            break;
                        case 'failed':
                        case 'cancelled':
                            $status_class = 'marzpay-status-failed';
                            break;
                        case 'pending':
                        case 'processing':
                            $status_class = 'marzpay-status-pending';
                            break;
                    }
                    
                    echo '<span class="marzpay-status ' . esc_attr( $status_class ) . '">' . esc_html( ucfirst( $status ) ) . '</span>';
                } else {
                    echo '<span class="marzpay-status marzpay-status-unknown">' . __( 'Unknown', 'marzpay' ) . '</span>';
                }
            } else {
                echo '—';
            }
        }
    }
    
    /**
     * Add bulk actions
     */
    public function add_bulk_actions( $actions ) {
        $actions['marzpay_check_status'] = __( 'Check MarzPay Status', 'marzpay' );
        return $actions;
    }
    
    /**
     * Handle bulk actions
     */
    public function handle_bulk_actions( $redirect_to, $action, $post_ids ) {
        if ( $action === 'marzpay_check_status' ) {
            $checked = 0;
            $api_client = MarzPay_API_Client::get_instance();
            
            foreach ( $post_ids as $post_id ) {
                $order = wc_get_order( $post_id );
                
                if ( $order && $order->get_payment_method() === 'marzpay' ) {
                    $transaction_uuid = $order->get_meta( '_marzpay_transaction_uuid' );
                    
                    if ( $transaction_uuid ) {
                        $result = $api_client->get_transaction( $transaction_uuid );
                        
                        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
                            $transaction = $result['data']['transaction'] ?? array();
                            $status = $transaction['status'] ?? '';
                            
                            $order->update_meta_data( '_marzpay_status', $status );
                            $order->save();
                            
                            $checked++;
                        }
                    }
                }
            }
            
            $redirect_to = add_query_arg( 'marzpay_checked', $checked, $redirect_to );
        }
        
        return $redirect_to;
    }
    
    /**
     * WooCommerce orders page
     */
    public function woocommerce_orders_page() {
        // Get MarzPay orders
        $orders = wc_get_orders( array(
            'payment_method' => 'marzpay',
            'limit' => 50,
            'orderby' => 'date',
            'order' => 'DESC'
        ) );
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-woocommerce-orders.php';
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'marzpay' ) !== false || $hook === 'post.php' ) {
            wp_enqueue_script( 'marzpay-woocommerce-admin', MARZPAY_PLUGIN_URL . 'assets/js/woocommerce-admin.js', array( 'jquery' ), MARZPAY_VERSION, true );
            wp_enqueue_style( 'marzpay-woocommerce-admin', MARZPAY_PLUGIN_URL . 'assets/css/woocommerce-admin.css', array(), MARZPAY_VERSION );
            
            wp_localize_script( 'marzpay-woocommerce-admin', 'marzpay_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'marzpay_nonce' )
            ) );
        }
    }
    
    /**
     * WooCommerce missing notice
     */
    public function woocommerce_missing_notice() {
        if ( ! $this->is_woocommerce_active() ) {
            echo '<div class="notice notice-warning"><p>';
            echo sprintf( 
                __( 'MarzPay WooCommerce integration requires WooCommerce to be installed and activated. <a href="%s">Install WooCommerce</a>', 'marzpay' ),
                admin_url( 'plugin-install.php?s=woocommerce&tab=search&type=term' )
            );
            echo '</p></div>';
        }
    }
}

// Initialize WooCommerce manager
MarzPay_WooCommerce_Manager::get_instance();
