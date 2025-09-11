<?php
/**
 * Admin Transactions Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
    <h1><?php _e( 'MarzPay Transactions', 'marzpay' ); ?></h1>
    
    <!-- Filters -->
    <div class="marzpay-transactions-filters">
        <form method="get" action="">
            <input type="hidden" name="page" value="marzpay-transactions" />
            
            <div class="filter-row">
                <label for="status"><?php _e( 'Status:', 'marzpay' ); ?></label>
                <select name="status" id="status">
                    <option value=""><?php _e( 'All Statuses', 'marzpay' ); ?></option>
                    <option value="pending" <?php selected( isset( $filters['status'] ) ? $filters['status'] : '', 'pending' ); ?>><?php _e( 'Pending', 'marzpay' ); ?></option>
                    <option value="successful" <?php selected( isset( $filters['status'] ) ? $filters['status'] : '', 'successful' ); ?>><?php _e( 'Successful', 'marzpay' ); ?></option>
                    <option value="failed" <?php selected( isset( $filters['status'] ) ? $filters['status'] : '', 'failed' ); ?>><?php _e( 'Failed', 'marzpay' ); ?></option>
                </select>
                
                <label for="type"><?php _e( 'Type:', 'marzpay' ); ?></label>
                <select name="type" id="type">
                    <option value=""><?php _e( 'All Types', 'marzpay' ); ?></option>
                    <option value="collection" <?php selected( isset( $filters['type'] ) ? $filters['type'] : '', 'collection' ); ?>><?php _e( 'Collection', 'marzpay' ); ?></option>
                    <option value="withdrawal" <?php selected( isset( $filters['type'] ) ? $filters['type'] : '', 'withdrawal' ); ?>><?php _e( 'Withdrawal', 'marzpay' ); ?></option>
                </select>
                
                <label for="provider"><?php _e( 'Provider:', 'marzpay' ); ?></label>
                <select name="provider" id="provider">
                    <option value=""><?php _e( 'All Providers', 'marzpay' ); ?></option>
                    <option value="mtn" <?php selected( isset( $filters['provider'] ) ? $filters['provider'] : '', 'mtn' ); ?>><?php _e( 'MTN', 'marzpay' ); ?></option>
                    <option value="airtel" <?php selected( isset( $filters['provider'] ) ? $filters['provider'] : '', 'airtel' ); ?>><?php _e( 'Airtel', 'marzpay' ); ?></option>
                </select>
                
                <input type="submit" class="button" value="<?php _e( 'Filter', 'marzpay' ); ?>" />
                <a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button"><?php _e( 'Clear Filters', 'marzpay' ); ?></a>
            </div>
        </form>
    </div>
    
    <!-- Transactions Table -->
    <div class="marzpay-transactions-table">
        <?php if ( ! empty( $transactions ) ) : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Date', 'marzpay' ); ?></th>
                        <th><?php _e( 'Reference', 'marzpay' ); ?></th>
                        <th><?php _e( 'Type', 'marzpay' ); ?></th>
                        <th><?php _e( 'Amount', 'marzpay' ); ?></th>
                        <th><?php _e( 'Status', 'marzpay' ); ?></th>
                        <th><?php _e( 'Phone Number', 'marzpay' ); ?></th>
                        <th><?php _e( 'Provider', 'marzpay' ); ?></th>
                        <th><?php _e( 'Description', 'marzpay' ); ?></th>
                        <th><?php _e( 'Actions', 'marzpay' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $transactions as $transaction ) : ?>
                        <tr>
                            <td>
                                <?php 
                                $date = isset( $transaction['timeline']['created_at'] ) ? $transaction['timeline']['created_at'] : ( isset( $transaction['created_at'] ) ? $transaction['created_at'] : date( 'Y-m-d H:i:s' ) );
                                echo date( 'M j, Y', strtotime( $date ) ); 
                                ?>
                                <br>
                                <small class="text-muted"><?php echo date( 'H:i:s', strtotime( $date ) ); ?></small>
                            </td>
                            <td>
                                <strong><?php echo esc_html( isset( $transaction['reference'] ) ? $transaction['reference'] : 'N/A' ); ?></strong>
                            </td>
                            <td>
                                <?php 
                                $type = isset( $transaction['type'] ) ? $transaction['type'] : 'collection';
                                $type_class = '';
                                switch ( strtolower( $type ) ) {
                                    case 'credit':
                                        $type_class = 'type-credit';
                                        break;
                                    case 'debit':
                                        $type_class = 'type-debit';
                                        break;
                                    case 'collection':
                                        $type_class = 'type-collection';
                                        break;
                                    default:
                                        $type_class = 'type-default';
                                }
                                
                                if ( function_exists( 'marzpay_get_transaction_type_label' ) ) {
                                    echo '<span class="transaction-type ' . $type_class . '">' . marzpay_get_transaction_type_label( $type ) . '</span>';
                                } else {
                                    echo '<span class="transaction-type ' . $type_class . '">' . ucfirst( $type ) . '</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <strong><?php 
                                $amount = isset( $transaction['amount'] ) ? $transaction['amount'] : 0;
                                $currency = isset( $transaction['currency'] ) ? $transaction['currency'] : 'UGX';
                                
                                // Handle API amount format
                                if ( is_array( $amount ) && isset( $amount['formatted'] ) ) {
                                    echo $amount['formatted'] . ' ' . ( isset( $amount['currency'] ) ? $amount['currency'] : $currency );
                                } elseif ( function_exists( 'marzpay_format_amount' ) ) {
                                    echo marzpay_format_amount( $amount, $currency );
                                } else {
                                    echo number_format( is_array( $amount ) ? ( isset( $amount['raw'] ) ? $amount['raw'] : 0 ) : $amount ) . ' ' . $currency;
                                }
                                ?></strong>
                            </td>
                            <td>
                                <?php 
                                $status = isset( $transaction['status'] ) ? $transaction['status'] : 'unknown';
                                $status_class = '';
                                switch ( strtolower( $status ) ) {
                                    case 'completed':
                                    case 'successful':
                                        $status_class = 'status-completed';
                                        break;
                                    case 'pending':
                                    case 'processing':
                                        $status_class = 'status-pending';
                                        break;
                                    case 'failed':
                                    case 'cancelled':
                                        $status_class = 'status-failed';
                                        break;
                                    default:
                                        $status_class = 'status-unknown';
                                }
                                
                                if ( function_exists( 'marzpay_get_transaction_status_badge' ) ) {
                                    echo '<span class="transaction-status ' . $status_class . '">' . marzpay_get_transaction_status_badge( $status ) . '</span>';
                                } else {
                                    echo '<span class="transaction-status ' . $status_class . '">' . ucfirst( $status ) . '</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo esc_html( isset( $transaction['phone_number'] ) ? $transaction['phone_number'] : 'N/A' ); ?></td>
                            <td>
                                <?php 
                                $provider = isset( $transaction['provider'] ) ? $transaction['provider'] : 'unknown';
                                if ( function_exists( 'marzpay_get_provider_label' ) ) {
                                    echo marzpay_get_provider_label( $provider );
                                } else {
                                    echo ucfirst( $provider );
                                }
                                ?>
                            </td>
                            <td>
                                <?php if ( ! empty( $transaction['description'] ) ) : ?>
                                    <?php echo esc_html( $transaction['description'] ); ?>
                                <?php else : ?>
                                    <span class="text-muted"><?php _e( 'No description', 'marzpay' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="button button-small view-transaction-details" data-transaction-id="<?php echo esc_attr( isset( $transaction['uuid'] ) ? $transaction['uuid'] : 'N/A' ); ?>">
                                    <?php _e( 'View Details', 'marzpay' ); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ( $total_pages > 1 ) : ?>
                <div class="marzpay-pagination">
                    <?php
                    $current_url = remove_query_arg( 'paged' );
                    $current_url = add_query_arg( 'page', 'marzpay-transactions', $current_url );
                    
                    // Add back filters
                    if ( isset( $filters['status'] ) ) {
                        $current_url = add_query_arg( 'status', $filters['status'], $current_url );
                    }
                    if ( isset( $filters['type'] ) ) {
                        $current_url = add_query_arg( 'type', $filters['type'], $current_url );
                    }
                    if ( isset( $filters['provider'] ) ) {
                        $current_url = add_query_arg( 'provider', $filters['provider'], $current_url );
                    }
                    
                    echo paginate_links( array(
                        'base' => add_query_arg( 'paged', '%#%', $current_url ),
                        'format' => '',
                        'prev_text' => __( '&laquo; Previous', 'marzpay' ),
                        'next_text' => __( 'Next &raquo;', 'marzpay' ),
                        'total' => $total_pages,
                        'current' => $page
                    ) );
                    ?>
                </div>
            <?php endif; ?>
            
        <?php else : ?>
            <div class="marzpay-no-transactions">
                <p><?php _e( 'No transactions found.', 'marzpay' ); ?></p>
                <?php if ( ! empty( $filters ) ) : ?>
                    <p><a href="<?php echo admin_url( 'admin.php?page=marzpay-transactions' ); ?>" class="button"><?php _e( 'Clear Filters', 'marzpay' ); ?></a></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transaction-details-modal" class="marzpay-modal" style="display: none;">
    <div class="marzpay-modal-content">
        <div class="marzpay-modal-header">
            <h2><?php _e( 'Transaction Details', 'marzpay' ); ?></h2>
            <span class="marzpay-modal-close">&times;</span>
        </div>
        <div class="marzpay-modal-body">
            <div id="transaction-details-content">
                <p><?php _e( 'Loading...', 'marzpay' ); ?></p>
            </div>
        </div>
    </div>
</div>

<style>
.marzpay-transactions-filters {
    background: #fff;
    padding: 20px;
    margin-bottom: 20px;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.filter-row {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-row label {
    font-weight: 600;
    margin-right: 5px;
}

.filter-row select {
    min-width: 120px;
}

.marzpay-transactions-table {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
}

.marzpay-transactions-table table {
    margin: 0;
}

.marzpay-transactions-table th,
.marzpay-transactions-table td {
    padding: 12px;
    text-align: left;
    vertical-align: top;
}

.marzpay-transactions-table th {
    background: #f6f7f7;
    font-weight: 600;
    border-bottom: 1px solid #ccd0d4;
}

.text-muted {
    color: #646970;
    font-style: italic;
}

.marzpay-pagination {
    padding: 20px;
    text-align: center;
    background: #f9f9f9;
    border-top: 1px solid #ccd0d4;
}

.marzpay-no-transactions {
    padding: 40px;
    text-align: center;
    color: #646970;
}

/* Modal Styles */
.marzpay-modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.marzpay-modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 4px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
}

.marzpay-modal-header {
    padding: 20px;
    border-bottom: 1px solid #ccd0d4;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.marzpay-modal-header h2 {
    margin: 0;
}

.marzpay-modal-close {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #646970;
}

.marzpay-modal-close:hover {
    color: #d63638;
}

.marzpay-modal-body {
    padding: 20px;
}

/* Transaction Type Colors */
.transaction-type {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}

.type-credit {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.type-debit {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.type-collection {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.type-default {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}

/* Transaction Status Colors */
.transaction-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
}

.status-completed {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-failed {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.status-unknown {
    background: #e2e3e5;
    color: #383d41;
    border: 1px solid #d6d8db;
}
</style>

<script>
jQuery(document).ready(function($) {
    // View transaction details
    $('.view-transaction-details').on('click', function() {
        var transactionId = $(this).data('transaction-id');
        var modal = $('#transaction-details-modal');
        var content = $('#transaction-details-content');
        
        content.html('<p><?php _e( 'Loading...', 'marzpay' ); ?></p>');
        modal.show();
        
        // Load transaction details via AJAX
        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: {
                action: 'marzpay_get_transaction_details',
                transaction_id: transactionId,
                nonce: '<?php echo wp_create_nonce( 'marzpay_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    content.html(response.data.html);
                } else {
                    content.html('<p class="error">' + response.data.message + '</p>');
                }
            },
            error: function() {
                content.html('<p class="error"><?php _e( 'Error loading transaction details.', 'marzpay' ); ?></p>');
            }
        });
    });
    
    // Close modal
    $('.marzpay-modal-close, .marzpay-modal').on('click', function(e) {
        if (e.target === this) {
            $('#transaction-details-modal').hide();
        }
    });
});
</script>
