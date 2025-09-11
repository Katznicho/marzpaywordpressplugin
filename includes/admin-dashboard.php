<?php
/**
 * MarzPay Admin Dashboard
 * 
 * Handles admin dashboard functionality
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_Admin_Dashboard {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets' ) );
        add_action( 'admin_init', array( $this, 'handle_admin_actions' ) );
    }
    
    /**
     * Add dashboard widgets
     */
    public function add_dashboard_widgets() {
        if ( marzpay_current_user_can_manage() ) {
            wp_add_dashboard_widget(
                'marzpay_dashboard_widget',
                __( 'MarzPay Overview', 'marzpay' ),
                array( $this, 'dashboard_widget_content' )
            );
        }
    }
    
    /**
     * Dashboard widget content
     */
    public function dashboard_widget_content() {
        $api_client = marzpay_get_api_client();
        $database = marzpay_get_database();
        
        // Get basic stats
        $total_transactions = $database->get_transaction_count();
        $successful_transactions = $database->get_transaction_count( array( 'status' => 'successful' ) );
        $pending_transactions = $database->get_transaction_count( array( 'status' => 'pending' ) );
        
        // Get recent transactions
        $recent_transactions = $database->get_transactions( array( 'limit' => 5 ) );
        
        // Check API configuration
        $is_configured = $api_client->is_configured();
        
        ?>
        <div class="marzpay-dashboard-widget">
            <?php if ( ! $is_configured ) : ?>
                <div class="marzpay-warning">
                    <p><strong><?php _e( 'API Not Configured', 'marzpay' ); ?></strong></p>
                    <p><?php _e( 'Please configure your MarzPay API credentials in the settings.', 'marzpay' ); ?></p>
                    <a href="<?php echo admin_url( 'admin.php?page=marzpay-settings' ); ?>" class="button button-primary">
                        <?php _e( 'Configure Now', 'marzpay' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div class="marzpay-stats">
                    <div class="marzpay-stat-item">
                        <span class="marzpay-stat-number"><?php echo esc_html( $total_transactions ); ?></span>
                        <span class="marzpay-stat-label"><?php _e( 'Total Transactions', 'marzpay' ); ?></span>
                    </div>
                    <div class="marzpay-stat-item">
                        <span class="marzpay-stat-number"><?php echo esc_html( $successful_transactions ); ?></span>
                        <span class="marzpay-stat-label"><?php _e( 'Successful', 'marzpay' ); ?></span>
                    </div>
                    <div class="marzpay-stat-item">
                        <span class="marzpay-stat-number"><?php echo esc_html( $pending_transactions ); ?></span>
                        <span class="marzpay-stat-label"><?php _e( 'Pending', 'marzpay' ); ?></span>
                    </div>
                </div>
                
                <?php if ( ! empty( $recent_transactions ) ) : ?>
                    <div class="marzpay-recent-transactions">
                        <h4><?php _e( 'Recent Transactions', 'marzpay' ); ?></h4>
                        <table class="marzpay-transactions-table">
                            <thead>
                                <tr>
                                    <th><?php _e( 'Reference', 'marzpay' ); ?></th>
                                    <th><?php _e( 'Amount', 'marzpay' ); ?></th>
                                    <th><?php _e( 'Status', 'marzpay' ); ?></th>
                                    <th><?php _e( 'Date', 'marzpay' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $recent_transactions as $transaction ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $transaction['reference'] ); ?></td>
                                        <td><?php echo marzpay_format_amount( $transaction['amount'] ); ?></td>
                                        <td><?php echo marzpay_get_status_badge( $transaction['status'] ); ?></td>
                                        <td><?php echo esc_html( date( 'M j, Y', strtotime( $transaction['created_at'] ) ) ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                
                <div class="marzpay-dashboard-actions">
                    <a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button">
                        <?php _e( 'View All Transactions', 'marzpay' ); ?>
                    </a>
                    <a href="<?php echo admin_url( 'admin.php?page=marzpay-settings' ); ?>" class="button">
                        <?php _e( 'Settings', 'marzpay' ); ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <style>
        .marzpay-dashboard-widget .marzpay-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .marzpay-dashboard-widget .marzpay-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        .marzpay-dashboard-widget .marzpay-stat-item {
            text-align: center;
            flex: 1;
        }
        
        .marzpay-dashboard-widget .marzpay-stat-number {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #0073aa;
        }
        
        .marzpay-dashboard-widget .marzpay-stat-label {
            display: block;
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .marzpay-dashboard-widget .marzpay-transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .marzpay-dashboard-widget .marzpay-transactions-table th,
        .marzpay-dashboard-widget .marzpay-transactions-table td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .marzpay-dashboard-widget .marzpay-transactions-table th {
            background: #f9f9f9;
            font-weight: bold;
        }
        
        .marzpay-dashboard-widget .marzpay-dashboard-actions {
            text-align: center;
        }
        
        .marzpay-dashboard-widget .marzpay-dashboard-actions .button {
            margin: 0 5px;
        }
        </style>
        <?php
    }
    
    /**
     * Handle admin actions
     */
    public function handle_admin_actions() {
        if ( ! isset( $_GET['page'] ) || strpos( $_GET['page'], 'marzpay' ) === false ) {
            return;
        }
        
        // Handle transaction actions
        if ( isset( $_GET['action'] ) && isset( $_GET['transaction_id'] ) ) {
            $this->handle_transaction_action();
        }
        
        // Handle webhook actions
        if ( isset( $_GET['action'] ) && isset( $_GET['webhook_id'] ) ) {
            $this->handle_webhook_action();
        }
    }
    
    /**
     * Handle transaction actions
     */
    private function handle_transaction_action() {
        if ( ! marzpay_current_user_can_manage() ) {
            wp_die( __( 'You do not have permission to perform this action.', 'marzpay' ) );
        }
        
        $action = sanitize_text_field( $_GET['action'] );
        $transaction_id = sanitize_text_field( $_GET['transaction_id'] );
        $nonce = sanitize_text_field( $_GET['_wpnonce'] );
        
        if ( ! wp_verify_nonce( $nonce, 'marzpay_transaction_action' ) ) {
            wp_die( __( 'Security check failed.', 'marzpay' ) );
        }
        
        $database = marzpay_get_database();
        $transaction = $database->get_transaction( $transaction_id );
        
        if ( ! $transaction ) {
            wp_die( __( 'Transaction not found.', 'marzpay' ) );
        }
        
        switch ( $action ) {
            case 'view':
                $this->show_transaction_details( $transaction );
                break;
            case 'refresh':
                $this->refresh_transaction_status( $transaction );
                break;
            default:
                wp_die( __( 'Invalid action.', 'marzpay' ) );
        }
    }
    
    /**
     * Handle webhook actions
     */
    private function handle_webhook_action() {
        if ( ! marzpay_current_user_can_manage() ) {
            wp_die( __( 'You do not have permission to perform this action.', 'marzpay' ) );
        }
        
        $action = sanitize_text_field( $_GET['action'] );
        $webhook_id = intval( $_GET['webhook_id'] );
        $nonce = sanitize_text_field( $_GET['_wpnonce'] );
        
        if ( ! wp_verify_nonce( $nonce, 'marzpay_webhook_action' ) ) {
            wp_die( __( 'Security check failed.', 'marzpay' ) );
        }
        
        switch ( $action ) {
            case 'test':
                $this->test_webhook( $webhook_id );
                break;
            case 'toggle':
                $this->toggle_webhook( $webhook_id );
                break;
            default:
                wp_die( __( 'Invalid action.', 'marzpay' ) );
        }
    }
    
    /**
     * Show transaction details
     */
    private function show_transaction_details( $transaction ) {
        ?>
        <div class="wrap">
            <h1><?php _e( 'Transaction Details', 'marzpay' ); ?></h1>
            
            <div class="marzpay-transaction-details">
                <table class="form-table">
                    <tr>
                        <th><?php _e( 'UUID', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( $transaction['uuid'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Reference', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( $transaction['reference'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Type', 'marzpay' ); ?></th>
                        <td><?php echo marzpay_get_type_badge( $transaction['type'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Status', 'marzpay' ); ?></th>
                        <td><?php echo marzpay_get_status_badge( $transaction['status'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Amount', 'marzpay' ); ?></th>
                        <td><?php echo marzpay_format_amount( $transaction['amount'], $transaction['currency'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Phone Number', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( marzpay_sanitize_phone_display( $transaction['phone_number'] ) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Provider', 'marzpay' ); ?></th>
                        <td><?php echo marzpay_get_provider_badge( $transaction['provider'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Description', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( $transaction['description'] ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Created At', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( date( 'F j, Y g:i A', strtotime( $transaction['created_at'] ) ) ); ?></td>
                    </tr>
                    <tr>
                        <th><?php _e( 'Updated At', 'marzpay' ); ?></th>
                        <td><?php echo esc_html( date( 'F j, Y g:i A', strtotime( $transaction['updated_at'] ) ) ); ?></td>
                    </tr>
                </table>
                
                <?php if ( ! empty( $transaction['metadata'] ) ) : ?>
                    <h3><?php _e( 'Metadata', 'marzpay' ); ?></h3>
                    <pre><?php echo esc_html( json_encode( $transaction['metadata'], JSON_PRETTY_PRINT ) ); ?></pre>
                <?php endif; ?>
            </div>
            
            <p>
                <a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button">
                    <?php _e( 'Back to Transactions', 'marzpay' ); ?>
                </a>
            </p>
        </div>
        <?php
    }
    
    /**
     * Refresh transaction status
     */
    private function refresh_transaction_status( $transaction ) {
        $api_client = marzpay_get_api_client();
        
        if ( $transaction['type'] === 'collection' ) {
            $result = $api_client->get_collection( $transaction['uuid'] );
        } else {
            $result = $api_client->get_withdrawal( $transaction['uuid'] );
        }
        
        if ( isset( $result['status'] ) && $result['status'] === 'success' ) {
            $new_status = $result['data']['transaction']['status'];
            marzpay_update_transaction_status( $transaction['uuid'], $new_status, $result );
            
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Transaction status updated successfully.', 'marzpay' ) . '</p></div>';
            });
        } else {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Failed to update transaction status.', 'marzpay' ) . '</p></div>';
            });
        }
        
        wp_redirect( admin_url( 'admin.php?page=marzpay-transactions' ) );
        exit;
    }
    
    /**
     * Test webhook
     */
    private function test_webhook( $webhook_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_webhooks';
        $webhook = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $webhook_id
        ), ARRAY_A );
        
        if ( ! $webhook ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Webhook not found.', 'marzpay' ) . '</p></div>';
            });
            return;
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
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode( $test_data ),
            'timeout' => 30
        ) );
        
        $response_code = wp_remote_retrieve_response_code( $response );
        $response_body = wp_remote_retrieve_body( $response );
        
        if ( $response_code >= 200 && $response_code < 300 ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . __( 'Test webhook sent successfully.', 'marzpay' ) . '</p></div>';
            });
        } else {
            add_action( 'admin_notices', function() use ( $response_code, $response_body ) {
                echo '<div class="notice notice-error is-dismissible"><p>' . sprintf( __( 'Test webhook failed. Response code: %s', 'marzpay' ), $response_code ) . '</p></div>';
            });
        }
        
        wp_redirect( admin_url( 'admin.php?page=marzpay-webhooks' ) );
        exit;
    }
    
    /**
     * Toggle webhook status
     */
    private function toggle_webhook( $webhook_id ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_webhooks';
        $webhook = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE id = %d",
            $webhook_id
        ), ARRAY_A );
        
        if ( ! $webhook ) {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Webhook not found.', 'marzpay' ) . '</p></div>';
            });
            return;
        }
        
        $new_status = $webhook['is_active'] ? 0 : 1;
        
        $result = $wpdb->update(
            $table,
            array( 'is_active' => $new_status ),
            array( 'id' => $webhook_id )
        );
        
        if ( $result !== false ) {
            $status_text = $new_status ? __( 'activated', 'marzpay' ) : __( 'deactivated', 'marzpay' );
            add_action( 'admin_notices', function() use ( $status_text ) {
                echo '<div class="notice notice-success is-dismissible"><p>' . sprintf( __( 'Webhook %s successfully.', 'marzpay' ), $status_text ) . '</p></div>';
            });
        } else {
            add_action( 'admin_notices', function() {
                echo '<div class="notice notice-error is-dismissible"><p>' . __( 'Failed to update webhook status.', 'marzpay' ) . '</p></div>';
            });
        }
        
        wp_redirect( admin_url( 'admin.php?page=marzpay-webhooks' ) );
        exit;
    }
}

// Initialize admin dashboard
MarzPay_Admin_Dashboard::get_instance();
