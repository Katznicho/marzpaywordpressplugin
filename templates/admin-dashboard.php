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
            <?php if ( isset( $account['data'] ) ) : ?>
                <div class="marzpay-account-info">
                    <table class="widefat">
                        <tbody>
                            <tr>
                                <td><strong><?php _e( 'Account Name:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['name'] ?? $account['data']['username'] ?? $account['data']['account_name'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Email:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['email'] ?? 'N/A' ); ?></td>
                            </tr>
                            <tr>
                                <td><strong><?php _e( 'Phone:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['phone'] ?? $account['data']['phone_number'] ?? 'N/A' ); ?></td>
                            </tr>
                            <?php if ( isset( $account['data']['id'] ) ) : ?>
                            <tr>
                                <td><strong><?php _e( 'Account ID:', 'marzpay' ); ?></strong></td>
                                <td><?php echo esc_html( $account['data']['id'] ); ?></td>
                            </tr>
                            <?php endif; ?>
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
            <?php if ( isset( $balance['data'] ) ) : ?>
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
        </div>

        <!-- Recent Transactions -->
        <div class="marzpay-dashboard-section">
            <h2><?php _e( 'Recent Transactions', 'marzpay' ); ?></h2>
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
                                    <td><?php echo esc_html( $transaction->reference ); ?></td>
                                    <td><?php echo marzpay_get_transaction_type_label( $transaction->type ); ?></td>
                                    <td><?php echo marzpay_format_amount( $transaction->amount, $transaction->currency ); ?></td>
                                    <td><?php echo marzpay_get_transaction_status_badge( $transaction->status ); ?></td>
                                    <td><?php echo esc_html( $transaction->phone_number ); ?></td>
                                    <td><?php echo date( 'M j, Y H:i', strtotime( $transaction->created_at ) ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <p><a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button"><?php _e( 'View All Transactions', 'marzpay' ); ?></a></p>
            <?php else : ?>
                <p><?php _e( 'No transactions found.', 'marzpay' ); ?></p>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<style>
.marzpay-dashboard-section {
    margin-bottom: 30px;
    padding: 20px;
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.marzpay-dashboard-section h2 {
    margin-top: 0;
    color: #23282d;
}

.marzpay-account-info table {
    margin: 0;
}

.marzpay-account-info td {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f1;
}

.marzpay-balance-info {
    text-align: center;
}

.balance-card {
    display: inline-block;
    padding: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    min-width: 200px;
}

.balance-card h3 {
    font-size: 2em;
    margin: 0 0 10px 0;
    font-weight: bold;
}

.balance-card p {
    margin: 0;
    opacity: 0.9;
}

.marzpay-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 6px;
    text-align: center;
    border-left: 4px solid #0073aa;
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
    font-size: 2em;
    margin: 0 0 10px 0;
    color: #23282d;
}

.stat-card p {
    margin: 0;
    color: #646970;
    font-weight: 500;
}

.marzpay-recent-transactions {
    margin-top: 20px;
}

.marzpay-recent-transactions table {
    margin: 0;
}

.marzpay-recent-transactions th,
.marzpay-recent-transactions td {
    padding: 12px;
    text-align: left;
}

.marzpay-recent-transactions th {
    background: #f6f7f7;
    font-weight: 600;
}
</style>
