<?php
/**
 * Admin Dashboard Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
    <h1><?php _e( 'MarzPay Dashboard', 'marzpay' ); ?></h1>
    
    <?php if ( ! $api_client->is_configured() ) : ?>
        <div class="notice notice-warning">
            <p><?php _e( 'Please configure your API credentials in the Settings page to use MarzPay features.', 'marzpay' ); ?></p>
            <p><a href="<?php echo admin_url( 'admin.php?page=marzpay-settings' ); ?>" class="button button-primary"><?php _e( 'Configure API Settings', 'marzpay' ); ?></a></p>
        </div>
    <?php else : ?>
        
        <!-- Account Information -->
        <div class="marzpay-dashboard-section">
            <h2><?php _e( 'Account Information', 'marzpay' ); ?></h2>
            <?php if ( isset( $account['data']['account'] ) ) : ?>
                <div class="marzpay-account-info">
                    <table class="widefat">
                        <tbody>
                            <tr>
                                <td><strong><?php _e( 'Business Name:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['account']['business_name'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Email:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['account']['email'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Contact Phone:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['account']['contact_phone'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Business Address:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['account']['business_address'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Account Status:', 'marzpay' ); ?></strong></td>
                                <td>
                                    <?php 
                                    $status = $account['data']['account']['status']['account_status'] ?? 'Unknown';
                                    $status_class = $status === 'active' ? 'success' : 'warning';
                                    echo '<span class="status-' . $status_class . '">' . ucfirst( $status ) . '</span>';
                                    ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p><?php _e( 'Unable to fetch account information. Please check your API credentials.', 'marzpay' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Balance Information -->
        <div class="marzpay-dashboard-section">
            <h2><?php _e( 'Account Balance', 'marzpay' ); ?></h2>
            <?php if ( isset( $account['data']['account']['balance'] ) ) : ?>
                <div class="marzpay-balance-info">
                    <div class="balance-card">
                        <h3><?php echo esc_html( $account['data']['account']['balance']['formatted'] . ' ' . $account['data']['account']['balance']['currency'] ); ?></h3>
                        <p><?php _e( 'Available Balance', 'marzpay' ); ?></p>
                    </div>
                </div>
            <?php elseif ( isset( $balance['data'] ) ) : ?>
                <div class="marzpay-balance-info">
                    <div class="balance-card">
                        <h3><?php echo marzpay_format_amount( $balance['data']['balance'], $balance['data']['currency'] ?? 'UGX' ); ?></h3>
                        <p><?php _e( 'Available Balance', 'marzpay' ); ?></p>
                    </div>
                </div>
            <?php else : ?>
                <p><?php _e( 'Unable to fetch balance information. Please check your API credentials.', 'marzpay' ); ?></p>
            <?php endif; ?>
        </div>

        <!-- Transaction Statistics -->
        <div class="marzpay-dashboard-section">
            <h2><?php _e( 'Transaction Statistics', 'marzpay' ); ?></h2>
            
            <!-- Temporary Debug Info -->
            <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
                <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px; font-family: monospace; font-size: 12px;">
                    <strong>Debug Info:</strong><br>
                    Total: <?php echo $total_transactions; ?><br>
                    Successful: <?php echo $successful_transactions; ?><br>
                    Pending: <?php echo $pending_transactions; ?><br>
                    Failed: <?php echo $failed_transactions; ?><br>
                    Recent count: <?php echo count( $recent_transactions ); ?>
                </div>
            <?php endif; ?>
            
            <?php if ( $total_transactions > 0 ) : ?>
                <div class="marzpay-stats-grid">
                    <div class="stat-card">
                        <h3><?php echo number_format( $total_transactions ); ?></h3>
                        <p><?php _e( 'Total Transactions', 'marzpay' ); ?></p>
                    </div>
                    <div class="stat-card success">
                        <h3><?php echo number_format( $successful_transactions ); ?></h3>
                        <p><?php _e( 'Successful', 'marzpay' ); ?></p>
                    </div>
                    <div class="stat-card warning">
                        <h3><?php echo number_format( $pending_transactions ); ?></h3>
                        <p><?php _e( 'Pending', 'marzpay' ); ?></p>
                    </div>
                    <div class="stat-card error">
                        <h3><?php echo number_format( $failed_transactions ); ?></h3>
                        <p><?php _e( 'Failed', 'marzpay' ); ?></p>
                    </div>
                </div>
            <?php else : ?>
                <div style="text-align: center; padding: 40px; color: #646970;">
                    <div style="font-size: 3em; margin-bottom: 15px; opacity: 0.3;">📊</div>
                    <p style="font-size: 1.2em; margin: 0 0 10px 0; font-weight: 600;"><?php _e( 'No Transactions Yet', 'marzpay' ); ?></p>
                    <p style="margin: 0;"><?php _e( 'Transaction statistics will appear here once you start processing payments.', 'marzpay' ); ?></p>
                </div>
            <?php endif; ?>
        </div>

                <!-- Recent Transactions -->
                <div class="marzpay-dashboard-section">
                    <h2><?php _e( 'Recent Transactions', 'marzpay' ); ?></h2>
                    
                    <!-- Temporary Debug Info -->
                    <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
                        <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 4px; font-family: monospace; font-size: 12px;">
                            <strong>Debug Info:</strong><br>
                            Recent transactions count: <?php echo count( $recent_transactions ); ?><br>
                            <?php if ( ! empty( $recent_transactions ) ) : ?>
                                First transaction structure:<br>
                                <pre><?php echo esc_html( print_r( $recent_transactions[0], true ) ); ?></pre>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ( ! empty( $recent_transactions ) ) : ?>
                <div class="marzpay-recent-transactions">
                    <table class="widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php _e( 'Reference', 'marzpay' ); ?></th>
                                <th><?php _e( 'Type', 'marzpay' ); ?></th>
                                <th><?php _e( 'Amount', 'marzpay' ); ?></th>
                                <th><?php _e( 'Status', 'marzpay' ); ?></th>
                                <th><?php _e( 'Phone', 'marzpay' ); ?></th>
                                <th><?php _e( 'Date', 'marzpay' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $recent_transactions as $transaction ) : ?>
                                <tr>
                                    <td>
                                        <strong><?php echo esc_html( isset( $transaction['reference'] ) ? $transaction['reference'] : 'N/A' ); ?></strong>
                                    </td>
                                    <td>
                                        <span class="transaction-type">
                                            <?php 
                                            $type = isset( $transaction['type'] ) ? $transaction['type'] : 'collection';
                                            if ( function_exists( 'marzpay_get_transaction_type_label' ) ) {
                                                echo marzpay_get_transaction_type_label( $type );
                                            } else {
                                                echo ucfirst( $type );
                                            }
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong><?php 
                                        $amount = isset( $transaction['amount'] ) ? $transaction['amount'] : 0;
                                        $currency = isset( $transaction['currency'] ) ? $transaction['currency'] : 'UGX';
                                        if ( function_exists( 'marzpay_format_amount' ) ) {
                                            echo marzpay_format_amount( $amount, $currency );
                                        } else {
                                            echo number_format( $amount ) . ' ' . $currency;
                                        }
                                        ?></strong>
                                    </td>
                                    <td><?php 
                                    $status = isset( $transaction['status'] ) ? $transaction['status'] : 'unknown';
                                    if ( function_exists( 'marzpay_get_transaction_status_badge' ) ) {
                                        echo marzpay_get_transaction_status_badge( $status );
                                    } else {
                                        echo '<span class="status-' . $status . '">' . ucfirst( $status ) . '</span>';
                                    }
                                    ?></td>
                                    <td><?php echo esc_html( isset( $transaction['phone_number'] ) ? $transaction['phone_number'] : 'N/A' ); ?></td>
                                    <td>
                                        <?php 
                                        $date = isset( $transaction['created_at'] ) ? $transaction['created_at'] : date( 'Y-m-d H:i:s' );
                                        echo date( 'M j, Y', strtotime( $date ) ); 
                                        ?>
                                        <br>
                                        <small style="color: #646970;"><?php echo date( 'H:i', strtotime( $date ) ); ?></small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button button-primary">
                        <?php _e( 'View All Transactions', 'marzpay' ); ?>
                    </a>
                </div>
            <?php else : ?>
                <div style="text-align: center; padding: 40px; color: #646970;">
                    <p style="font-size: 1.1em; margin: 0;"><?php _e( 'No transactions found.', 'marzpay' ); ?></p>
                    <p style="margin: 10px 0 0 0;"><?php _e( 'Transactions will appear here once you start collecting payments.', 'marzpay' ); ?></p>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<style>
.marzpay-dashboard-section {
    margin-bottom: 30px;
    padding: 25px;
    background: #fff;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.marzpay-dashboard-section h2 {
    margin-top: 0;
    margin-bottom: 20px;
    color: #1d2327;
    font-size: 1.5em;
    font-weight: 600;
    border-bottom: 2px solid #f0f0f1;
    padding-bottom: 10px;
}

.marzpay-account-info table {
    margin: 0;
    width: 100%;
}

.marzpay-account-info td {
    padding: 12px 15px;
    border-bottom: 1px solid #f0f0f1;
    vertical-align: top;
}

.marzpay-account-info td:first-child {
    width: 30%;
    font-weight: 600;
    color: #1d2327;
}

.marzpay-balance-info {
    text-align: center;
    margin: 20px 0;
}

.balance-card {
    display: inline-block;
    padding: 40px 50px;
    background: linear-gradient(135deg, #0073aa 0%, #005a87 100%);
    color: white;
    border-radius: 12px;
    min-width: 250px;
    box-shadow: 0 4px 15px rgba(0,115,170,0.3);
}

.balance-card h3 {
    font-size: 2.5em;
    margin: 0 0 10px 0;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.balance-card p {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1em;
    font-weight: 500;
}

.marzpay-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-top: 25px;
}

.stat-card {
    padding: 25px;
    background: #fff;
    border-radius: 8px;
    text-align: center;
    border-left: 4px solid #0073aa;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.stat-card.success {
    border-left-color: #00a32a;
}

.stat-card.warning {
    border-left-color: #dba617;
}

.stat-card.error {
    border-left-color: #d63638;
}

.stat-card h3 {
    font-size: 2.2em;
    margin: 0 0 10px 0;
    color: #1d2327;
    font-weight: 700;
}

.stat-card p {
    margin: 0;
    color: #646970;
    font-weight: 600;
    font-size: 0.95em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.marzpay-recent-transactions {
    margin-top: 25px;
}

.marzpay-recent-transactions table {
    margin: 0;
    width: 100%;
    border-collapse: collapse;
}

.marzpay-recent-transactions th,
.marzpay-recent-transactions td {
    padding: 15px 12px;
    text-align: left;
    border-bottom: 1px solid #f0f0f1;
}

.marzpay-recent-transactions th {
    background: #f8f9fa;
    font-weight: 600;
    color: #1d2327;
    font-size: 0.9em;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.marzpay-recent-transactions tr:hover {
    background: #f8f9fa;
}

.status-success {
    color: #00a32a;
    font-weight: 600;
}

.status-warning {
    color: #dba617;
    font-weight: 600;
}

.status-error {
    color: #d63638;
    font-weight: 600;
}

.notice {
    padding: 15px 20px;
    margin: 20px 0;
    border-radius: 6px;
    border-left: 4px solid;
}

.notice-warning {
    background: #fff8e1;
    border-left-color: #dba617;
    color: #8a6914;
}

.notice-success {
    background: #f0f8ff;
    border-left-color: #0073aa;
    color: #0073aa;
}
</style>
