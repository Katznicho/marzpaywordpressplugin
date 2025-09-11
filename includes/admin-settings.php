<?php
/**
 * MarzPay Admin Settings
 * 
 * Handles admin settings page and configuration
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_Admin_Settings {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        // Main MarzPay menu
        add_menu_page(
            __( 'MarzPay', 'marzpay' ),
            __( 'MarzPay', 'marzpay' ),
            'manage_options',
            'marzpay-dashboard',
            array( $this, 'dashboard_page' ),
            'dashicons-money-alt',
            30
        );
        
        // Dashboard submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Dashboard', 'marzpay' ),
            __( 'Dashboard', 'marzpay' ),
            'manage_options',
            'marzpay-dashboard',
            array( $this, 'dashboard_page' )
        );
        
        // Transactions submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Transactions', 'marzpay' ),
            __( 'Transactions', 'marzpay' ),
            'manage_options',
            'marzpay-transactions',
            array( $this, 'transactions_page' )
        );
        
        // Webhooks submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Webhooks', 'marzpay' ),
            __( 'Webhooks', 'marzpay' ),
            'manage_options',
            'marzpay-webhooks',
            array( $this, 'webhooks_page' )
        );
        
        // Settings submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Settings', 'marzpay' ),
            __( 'Settings', 'marzpay' ),
        'manage_options',
        'marzpay-settings',
            array( $this, 'settings_page' )
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // API Settings
        register_setting( 'marzpay_settings_group', 'marzpay_api_user' );
        register_setting( 'marzpay_settings_group', 'marzpay_api_key' );
        register_setting( 'marzpay_settings_group', 'marzpay_environment' );
        
        // Webhook Settings
        register_setting( 'marzpay_settings_group', 'marzpay_webhook_secret' );
        
        // General Settings
        register_setting( 'marzpay_settings_group', 'marzpay_default_currency' );
        register_setting( 'marzpay_settings_group', 'marzpay_default_country' );
        register_setting( 'marzpay_settings_group', 'marzpay_success_page' );
        register_setting( 'marzpay_settings_group', 'marzpay_failure_page' );
        
        // Add settings sections
        add_settings_section(
            'marzpay_api_section',
            __( 'API Configuration', 'marzpay' ),
            array( $this, 'api_section_callback' ),
            'marzpay-settings'
        );
        
        add_settings_section(
            'marzpay_webhook_section',
            __( 'Webhook Configuration', 'marzpay' ),
            array( $this, 'webhook_section_callback' ),
            'marzpay-settings'
        );
        
        add_settings_section(
            'marzpay_general_section',
            __( 'General Settings', 'marzpay' ),
            array( $this, 'general_section_callback' ),
            'marzpay-settings'
        );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( strpos( $hook, 'marzpay' ) !== false ) {
            wp_enqueue_script( 'marzpay-admin', MARZPAY_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), MARZPAY_VERSION, true );
            wp_enqueue_style( 'marzpay-admin', MARZPAY_PLUGIN_URL . 'assets/css/admin.css', array(), MARZPAY_VERSION );
        }
    }
    
    /**
     * Dashboard page
     */
    public function dashboard_page() {
        $api_client = MarzPay_API_Client::get_instance();
        $database = MarzPay_Database::get_instance();
        
        // Get account info
        $account = $api_client->get_account();
        $balance = $api_client->get_balance();
        
        // Get transaction stats
        $total_transactions = $database->get_transaction_count();
        $successful_transactions = $database->get_transaction_count( array( 'status' => 'successful' ) );
        $pending_transactions = $database->get_transaction_count( array( 'status' => 'pending' ) );
        $failed_transactions = $database->get_transaction_count( array( 'status' => 'failed' ) );
        
        // Get recent transactions
        $recent_transactions = $database->get_transactions( array( 'limit' => 10 ) );
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    /**
     * Transactions page
     */
    public function transactions_page() {
        $database = MarzPay_Database::get_instance();
        
        // Handle filters
        $filters = array();
        if ( isset( $_GET['status'] ) && ! empty( $_GET['status'] ) ) {
            $filters['status'] = sanitize_text_field( $_GET['status'] );
        }
        if ( isset( $_GET['type'] ) && ! empty( $_GET['type'] ) ) {
            $filters['type'] = sanitize_text_field( $_GET['type'] );
        }
        if ( isset( $_GET['provider'] ) && ! empty( $_GET['provider'] ) ) {
            $filters['provider'] = sanitize_text_field( $_GET['provider'] );
        }
        
        // Pagination
        $page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
        $per_page = 20;
        $offset = ( $page - 1 ) * $per_page;
        
        $filters['limit'] = $per_page;
        $filters['offset'] = $offset;
        
        $transactions = $database->get_transactions( $filters );
        $total_transactions = $database->get_transaction_count( $filters );
        $total_pages = ceil( $total_transactions / $per_page );
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-transactions.php';
    }
    
    /**
     * Webhooks page
     */
    public function webhooks_page() {
        $database = MarzPay_Database::get_instance();
        $webhooks = $database->get_webhooks();
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-webhooks.php';
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        if ( isset( $_POST['submit'] ) ) {
            $this->save_settings();
        }
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-settings.php';
    }
    
    /**
     * Save settings
     */
    private function save_settings() {
        if ( ! wp_verify_nonce( $_POST['marzpay_settings_nonce'], 'marzpay_save_settings' ) ) {
            wp_die( 'Security check failed' );
        }
        
        // Save API settings
        update_option( 'marzpay_api_user', sanitize_text_field( $_POST['marzpay_api_user'] ) );
        update_option( 'marzpay_api_key', sanitize_text_field( $_POST['marzpay_api_key'] ) );
        update_option( 'marzpay_environment', sanitize_text_field( $_POST['marzpay_environment'] ) );
        
        // Save webhook settings
        if ( ! empty( $_POST['marzpay_webhook_secret'] ) ) {
            update_option( 'marzpay_webhook_secret', sanitize_text_field( $_POST['marzpay_webhook_secret'] ) );
        }
        
        // Save general settings
        update_option( 'marzpay_default_currency', sanitize_text_field( $_POST['marzpay_default_currency'] ) );
        update_option( 'marzpay_default_country', sanitize_text_field( $_POST['marzpay_default_country'] ) );
        update_option( 'marzpay_success_page', intval( $_POST['marzpay_success_page'] ) );
        update_option( 'marzpay_failure_page', intval( $_POST['marzpay_failure_page'] ) );
        
        add_action( 'admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Settings saved successfully!', 'marzpay' ) . '</p></div>';
        });
    }
    
    /**
     * API section callback
     */
    public function api_section_callback() {
        echo '<p>' . __( 'Configure your MarzPay API credentials and environment settings.', 'marzpay' ) . '</p>';
    }
    
    /**
     * Webhook section callback
     */
    public function webhook_section_callback() {
        echo '<p>' . __( 'Configure webhook settings for receiving payment notifications.', 'marzpay' ) . '</p>';
    }
    
    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . __( 'Configure general plugin settings.', 'marzpay' ) . '</p>';
    }
}

// Initialize admin settings
MarzPay_Admin_Settings::get_instance();
