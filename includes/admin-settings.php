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
        add_action( 'wp_ajax_marzpay_get_transaction_details', array( $this, 'ajax_get_transaction_details' ) );
        add_action( 'wp_ajax_marzpay_test_api', array( $this, 'ajax_test_api_connection' ) );
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
        
        
        // Settings submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Settings', 'marzpay' ),
            __( 'Settings', 'marzpay' ),
            'manage_options',
            'marzpay-settings',
            array( $this, 'settings_page' )
        );
        
        // Documentation submenu
        add_submenu_page(
            'marzpay-dashboard',
            __( 'Documentation', 'marzpay' ),
            __( 'Documentation', 'marzpay' ),
            'manage_options',
            'marzpay-documentation',
            array( $this, 'documentation_page' )
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // API Settings
        register_setting( 'marzpay_settings_group', 'marzpay_api_user' );
        register_setting( 'marzpay_settings_group', 'marzpay_api_key' );
        
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
        
        // Get transactions from API instead of database
        $api_transactions = $api_client->get_transactions( array( 'limit' => 50 ) );
        
        // Process API response
        $transactions = array();
        $total_transactions = 0;
        $successful_transactions = 0;
        $pending_transactions = 0;
        $failed_transactions = 0;
        
        if ( isset( $api_transactions['status'] ) && $api_transactions['status'] === 'success' && isset( $api_transactions['data']['transactions'] ) ) {
            $transactions = $api_transactions['data']['transactions'];
            $total_transactions = count( $transactions );
            
            // Count by status
            foreach ( $transactions as $transaction ) {
                $status = $transaction['status'] ?? 'unknown';
                switch ( $status ) {
                    case 'successful':
                    case 'completed':
                        $successful_transactions++;
                        break;
                    case 'pending':
                    case 'processing':
                        $pending_transactions++;
                        break;
                    case 'failed':
                    case 'cancelled':
                        $failed_transactions++;
                        break;
                }
            }
        }

        // Get recent transactions (first 10)
        $recent_transactions = array_slice( $transactions, 0, 10 );
        
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-dashboard.php';
    }
    
    /**
     * Transactions page
     */
    public function transactions_page() {
        $api_client = MarzPay_API_Client::get_instance();
        
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
        
        // Get transactions from API
        $api_response = $api_client->get_transactions( $filters );
        
        $transactions = array();
        $total_transactions = 0;
        
        if ( isset( $api_response['status'] ) && $api_response['status'] === 'success' && isset( $api_response['data']['transactions'] ) ) {
            $transactions = $api_response['data']['transactions'];
            $total_transactions = count( $transactions );
        }
        
        $total_pages = ceil( $total_transactions / $per_page );
        
        include MARZPAY_PLUGIN_DIR . 'templates/admin-transactions.php';
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
        echo '<p>' . __( 'Configure your MarzPay API credentials.', 'marzpay' ) . '</p>';
    }
    
    
    /**
     * General section callback
     */
    public function general_section_callback() {
        echo '<p>' . __( 'Configure general plugin settings.', 'marzpay' ) . '</p>';
    }
    
    /**
     * AJAX handler for getting transaction details
     */
    public function ajax_get_transaction_details() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }

        $transaction_uuid = sanitize_text_field( $_POST['transaction_id'] );
        if ( ! $transaction_uuid ) {
            wp_send_json_error( array( 'message' => 'Invalid transaction UUID' ) );
        }

        $api_client = MarzPay_API_Client::get_instance();
        $result = $api_client->get_transaction( $transaction_uuid );

        if ( ! isset( $result['status'] ) || $result['status'] !== 'success' || ! isset( $result['data']['transaction'] ) ) {
            wp_send_json_error( array( 'message' => 'Transaction not found' ) );
        }

        $transaction = $result['data']['transaction'];

        $html = '<div class="transaction-details">';
        $html .= '<table class="widefat">';
        $html .= '<tbody>';
        $html .= '<tr><td><strong>' . __( 'Reference:', 'marzpay' ) . '</strong></td><td>' . esc_html( $transaction['reference'] ?? 'N/A' ) . '</td></tr>';
        $html .= '<tr><td><strong>' . __( 'UUID:', 'marzpay' ) . '</strong></td><td>' . esc_html( $transaction['uuid'] ?? 'N/A' ) . '</td></tr>';
        $html .= '<tr><td><strong>' . __( 'Type:', 'marzpay' ) . '</strong></td><td>' . ( function_exists( 'marzpay_get_transaction_type_label' ) ? marzpay_get_transaction_type_label( $transaction['type'] ?? 'collection' ) : ucfirst( $transaction['type'] ?? 'collection' ) ) . '</td></tr>';
        
        // Handle amount formatting
        $amount_display = 'N/A';
        if ( isset( $transaction['amount'] ) ) {
            if ( is_array( $transaction['amount'] ) && isset( $transaction['amount']['formatted'] ) ) {
                $amount_display = $transaction['amount']['formatted'] . ' ' . ( $transaction['amount']['currency'] ?? 'UGX' );
            } elseif ( function_exists( 'marzpay_format_amount' ) ) {
                $amount_display = marzpay_format_amount( $transaction['amount'], $transaction['currency'] ?? 'UGX' );
            } else {
                $amount_display = number_format( is_array( $transaction['amount'] ) ? ( $transaction['amount']['raw'] ?? 0 ) : $transaction['amount'] ) . ' ' . ( $transaction['currency'] ?? 'UGX' );
            }
        }
        $html .= '<tr><td><strong>' . __( 'Amount:', 'marzpay' ) . '</strong></td><td>' . $amount_display . '</td></tr>';
        
        $html .= '<tr><td><strong>' . __( 'Status:', 'marzpay' ) . '</strong></td><td>' . ( function_exists( 'marzpay_get_transaction_status_badge' ) ? marzpay_get_transaction_status_badge( $transaction['status'] ?? 'unknown' ) : ucfirst( $transaction['status'] ?? 'unknown' ) ) . '</td></tr>';
        $html .= '<tr><td><strong>' . __( 'Phone Number:', 'marzpay' ) . '</strong></td><td>' . esc_html( $transaction['phone_number'] ?? 'N/A' ) . '</td></tr>';
        $html .= '<tr><td><strong>' . __( 'Provider:', 'marzpay' ) . '</strong></td><td>' . ( function_exists( 'marzpay_get_provider_label' ) ? marzpay_get_provider_label( $transaction['provider'] ?? 'unknown' ) : ucfirst( $transaction['provider'] ?? 'unknown' ) ) . '</td></tr>';
        $html .= '<tr><td><strong>' . __( 'Description:', 'marzpay' ) . '</strong></td><td>' . esc_html( $transaction['description'] ?? __( 'No description', 'marzpay' ) ) . '</td></tr>';
        
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        wp_send_json_success( array( 'html' => $html ) );
    }
    
    /**
     * AJAX handler for testing API connection
     */
    public function ajax_test_api_connection() {
        check_ajax_referer( 'marzpay_nonce', 'nonce' );
        
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Insufficient permissions' ) );
        }
        
        $api_client = MarzPay_API_Client::get_instance();
        
        if ( ! $api_client->is_configured() ) {
            wp_send_json_error( array( 'message' => 'API credentials not configured. Please enter your API Key and API Secret.' ) );
        }
        
        // Test API connection by getting account information
        $result = $api_client->get_account();
        
        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
            $account_data = $result['data']['account'] ?? array();
            
            // Try different possible field names for the account name
            $account_name = '';
            if ( isset( $account_data['business_name'] ) ) {
                $account_name = $account_data['business_name'];
            } elseif ( isset( $account_data['name'] ) ) {
                $account_name = $account_data['name'];
            } elseif ( isset( $account_data['username'] ) ) {
                $account_name = $account_data['username'];
            } elseif ( isset( $account_data['email'] ) ) {
                $account_name = $account_data['email'];
            } elseif ( isset( $account_data['account_name'] ) ) {
                $account_name = $account_data['account_name'];
            } else {
                $account_name = 'Account ID: ' . ( $account_data['uuid'] ?? 'N/A' );
            }
            
            $message = sprintf( 
                __( 'API connection successful! Connected as: %s', 'marzpay' ), 
                $account_name
            );
            
            // Debug information removed - field mapping fixed
            
            wp_send_json_success( array( 'message' => $message ) );
        } else {
            $error_message = $result['message'] ?? __( 'Unknown error occurred', 'marzpay' );
            wp_send_json_error( array( 'message' => sprintf( __( 'API connection failed: %s', 'marzpay' ), $error_message ) ) );
        }
    }
    
    /**
     * Documentation page
     */
    public function documentation_page() {
        include MARZPAY_PLUGIN_DIR . 'templates/admin-documentation.php';
    }
}

// Initialize admin settings
MarzPay_Admin_Settings::get_instance();
