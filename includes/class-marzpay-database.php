<?php
/**
 * MarzPay Database Class
 * 
 * Handles database operations for transactions, webhooks, and logs
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class MarzPay_Database {
    
    private static $instance = null;
    
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Constructor
    }
    
    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        // Transactions table
        $transactions_table = $wpdb->prefix . 'marzpay_transactions';
        $transactions_sql = "CREATE TABLE $transactions_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            uuid varchar(36) NOT NULL,
            reference varchar(50) NOT NULL,
            provider_reference varchar(100) DEFAULT NULL,
            type enum('collection','withdrawal','charge','refund') NOT NULL,
            status varchar(20) NOT NULL,
            amount decimal(15,2) NOT NULL,
            currency varchar(3) DEFAULT 'UGX',
            phone_number varchar(20) NOT NULL,
            description text,
            provider varchar(20) DEFAULT NULL,
            callback_url varchar(500) DEFAULT NULL,
            metadata longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uuid (uuid),
            KEY reference (reference),
            KEY status (status),
            KEY type (type),
            KEY phone_number (phone_number),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // Webhooks table
        $webhooks_table = $wpdb->prefix . 'marzpay_webhooks';
        $webhooks_sql = "CREATE TABLE $webhooks_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            uuid varchar(36) NOT NULL,
            name varchar(255) NOT NULL,
            url varchar(500) NOT NULL,
            event_type varchar(50) NOT NULL,
            environment enum('test','production') NOT NULL,
            is_active tinyint(1) DEFAULT 1,
            retry_count int(11) DEFAULT 0,
            last_triggered_at datetime DEFAULT NULL,
            last_response_code varchar(10) DEFAULT NULL,
            last_response_body text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uuid (uuid),
            KEY event_type (event_type),
            KEY is_active (is_active)
        ) $charset_collate;";
        
        // Webhook logs table
        $webhook_logs_table = $wpdb->prefix . 'marzpay_webhook_logs';
        $webhook_logs_sql = "CREATE TABLE $webhook_logs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            webhook_id bigint(20) NOT NULL,
            transaction_id bigint(20) DEFAULT NULL,
            event_type varchar(50) NOT NULL,
            payload longtext NOT NULL,
            response_code varchar(10) DEFAULT NULL,
            response_body text,
            attempts int(11) DEFAULT 1,
            status enum('pending','success','failed') DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY webhook_id (webhook_id),
            KEY transaction_id (transaction_id),
            KEY status (status),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        // API logs table
        $api_logs_table = $wpdb->prefix . 'marzpay_api_logs';
        $api_logs_sql = "CREATE TABLE $api_logs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            endpoint varchar(255) NOT NULL,
            method varchar(10) NOT NULL,
            request_data longtext,
            response_data longtext,
            status_code varchar(10) DEFAULT NULL,
            error_message text,
            duration_ms int(11) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY endpoint (endpoint),
            KEY method (method),
            KEY created_at (created_at)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        dbDelta( $transactions_sql );
        dbDelta( $webhooks_sql );
        dbDelta( $webhook_logs_sql );
        dbDelta( $api_logs_sql );
    }
    
    /**
     * Drop database tables
     */
    public static function drop_tables() {
        global $wpdb;
        
        $tables = array(
            $wpdb->prefix . 'marzpay_transactions',
            $wpdb->prefix . 'marzpay_webhooks',
            $wpdb->prefix . 'marzpay_webhook_logs',
            $wpdb->prefix . 'marzpay_api_logs'
        );
        
        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS $table" );
        }
    }
    
    /**
     * Insert transaction
     */
    public function insert_transaction( $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_transactions';
        
        $defaults = array(
            'uuid' => wp_generate_uuid4(),
            'type' => 'collection',
            'status' => 'pending',
            'currency' => 'UGX',
            'created_at' => current_time( 'mysql' ),
            'updated_at' => current_time( 'mysql' )
        );
        
        $data = wp_parse_args( $data, $defaults );
        
        if ( isset( $data['metadata'] ) && is_array( $data['metadata'] ) ) {
            $data['metadata'] = json_encode( $data['metadata'] );
        }
        
        $result = $wpdb->insert( $table, $data );
        
        if ( $result === false ) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Update transaction
     */
    public function update_transaction( $uuid, $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_transactions';
        
        $data['updated_at'] = current_time( 'mysql' );
        
        if ( isset( $data['metadata'] ) && is_array( $data['metadata'] ) ) {
            $data['metadata'] = json_encode( $data['metadata'] );
        }
        
        return $wpdb->update( $table, $data, array( 'uuid' => $uuid ) );
    }
    
    /**
     * Get transaction by UUID
     */
    public function get_transaction( $uuid ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_transactions';
        
        $transaction = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM $table WHERE uuid = %s",
            $uuid
        ), ARRAY_A );
        
        if ( $transaction && isset( $transaction['metadata'] ) ) {
            $transaction['metadata'] = json_decode( $transaction['metadata'], true );
        }
        
        return $transaction;
    }
    
    /**
     * Get transactions with pagination
     */
    public function get_transactions( $args = array() ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_transactions';
        
        $defaults = array(
            'limit' => 20,
            'offset' => 0,
            'status' => '',
            'type' => '',
            'provider' => '',
            'orderby' => 'created_at',
            'order' => 'DESC'
        );
        
        $args = wp_parse_args( $args, $defaults );
        
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
        
        if ( ! empty( $args['provider'] ) ) {
            $where_conditions[] = 'provider = %s';
            $where_values[] = $args['provider'];
        }
        
        $where_clause = '';
        if ( ! empty( $where_conditions ) ) {
            $where_clause = 'WHERE ' . implode( ' AND ', $where_conditions );
        }
        
        $order_clause = sprintf( 'ORDER BY %s %s', $args['orderby'], $args['order'] );
        $limit_clause = sprintf( 'LIMIT %d OFFSET %d', $args['limit'], $args['offset'] );
        
        $sql = "SELECT * FROM $table $where_clause $order_clause $limit_clause";
        
        if ( ! empty( $where_values ) ) {
            $sql = $wpdb->prepare( $sql, $where_values );
        }
        
        $transactions = $wpdb->get_results( $sql, ARRAY_A );
        
        // Decode metadata for each transaction
        foreach ( $transactions as &$transaction ) {
            if ( isset( $transaction['metadata'] ) ) {
                $transaction['metadata'] = json_decode( $transaction['metadata'], true );
            }
        }
        
        return $transactions;
    }
    
    /**
     * Get transaction count
     */
    public function get_transaction_count( $args = array() ) {
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
        
        if ( ! empty( $args['provider'] ) ) {
            $where_conditions[] = 'provider = %s';
            $where_values[] = $args['provider'];
        }
        
        $where_clause = '';
        if ( ! empty( $where_conditions ) ) {
            $where_clause = 'WHERE ' . implode( ' AND ', $where_conditions );
        }
        
        $sql = "SELECT COUNT(*) FROM $table $where_clause";
        
        if ( ! empty( $where_values ) ) {
            $sql = $wpdb->prepare( $sql, $where_values );
        }
        
        return $wpdb->get_var( $sql );
    }
    
    /**
     * Insert webhook
     */
    public function insert_webhook( $data ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_webhooks';
        
        $defaults = array(
            'uuid' => wp_generate_uuid4(),
            'is_active' => 1,
            'retry_count' => 0,
            'created_at' => current_time( 'mysql' ),
            'updated_at' => current_time( 'mysql' )
        );
        
        $data = wp_parse_args( $data, $defaults );
        
        $result = $wpdb->insert( $table, $data );
        
        if ( $result === false ) {
            return false;
        }
        
        return $wpdb->insert_id;
    }
    
    /**
     * Get webhooks
     */
    public function get_webhooks( $args = array() ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_webhooks';
        
        $defaults = array(
            'is_active' => '',
            'event_type' => ''
        );
        
        $args = wp_parse_args( $args, $defaults );
        
        $where_conditions = array();
        $where_values = array();
        
        if ( $args['is_active'] !== '' ) {
            $where_conditions[] = 'is_active = %d';
            $where_values[] = $args['is_active'];
        }
        
        if ( ! empty( $args['event_type'] ) ) {
            $where_conditions[] = 'event_type = %s';
            $where_values[] = $args['event_type'];
        }
        
        $where_clause = '';
        if ( ! empty( $where_conditions ) ) {
            $where_clause = 'WHERE ' . implode( ' AND ', $where_conditions );
        }
        
        $sql = "SELECT * FROM $table $where_clause ORDER BY created_at DESC";
        
        if ( ! empty( $where_values ) ) {
            $sql = $wpdb->prepare( $sql, $where_values );
        }
        
        return $wpdb->get_results( $sql, ARRAY_A );
    }
    
    /**
     * Log API request
     */
    public function log_api_request( $endpoint, $method, $request_data, $response_data, $status_code = null, $error_message = null, $duration_ms = null ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_api_logs';
        
        $data = array(
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => is_array( $request_data ) ? json_encode( $request_data ) : $request_data,
            'response_data' => is_array( $response_data ) ? json_encode( $response_data ) : $response_data,
            'status_code' => $status_code,
            'error_message' => $error_message,
            'duration_ms' => $duration_ms,
            'created_at' => current_time( 'mysql' )
        );
        
        return $wpdb->insert( $table, $data );
    }
    
    /**
     * Log webhook attempt
     */
    public function log_webhook_attempt( $webhook_id, $transaction_id, $event_type, $payload, $response_code = null, $response_body = null, $status = 'pending' ) {
        global $wpdb;
        
        $table = $wpdb->prefix . 'marzpay_webhook_logs';
        
        $data = array(
            'webhook_id' => $webhook_id,
            'transaction_id' => $transaction_id,
            'event_type' => $event_type,
            'payload' => is_array( $payload ) ? json_encode( $payload ) : $payload,
            'response_code' => $response_code,
            'response_body' => $response_body,
            'status' => $status,
            'created_at' => current_time( 'mysql' )
        );
        
        return $wpdb->insert( $table, $data );
    }
}
