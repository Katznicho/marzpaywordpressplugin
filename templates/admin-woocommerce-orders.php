<?php
/**
 * Admin WooCommerce Orders Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
    <h1><?php _e( 'MarzPay WooCommerce Orders', 'marzpay' ); ?></h1>
    
    <div class="marzpay-woocommerce-stats">
        <div class="stats-grid">
            <div class="stat-box">
                <h3><?php _e( 'Total MarzPay Orders', 'marzpay' ); ?></h3>
                <p class="stat-number"><?php echo count( $orders ); ?></p>
            </div>
            
            <?php
            $successful_orders = 0;
            $pending_orders = 0;
            $failed_orders = 0;
            $total_amount = 0;
            
            foreach ( $orders as $order ) {
                $status = $order->get_meta( '_marzpay_status' );
                $amount = $order->get_total();
                $total_amount += $amount;
                
                switch ( $status ) {
                    case 'successful':
                    case 'completed':
                        $successful_orders++;
                        break;
                    case 'pending':
                    case 'processing':
                        $pending_orders++;
                        break;
                    case 'failed':
                    case 'cancelled':
                        $failed_orders++;
                        break;
                }
            }
            ?>
            
            <div class="stat-box">
                <h3><?php _e( 'Successful Payments', 'marzpay' ); ?></h3>
                <p class="stat-number success"><?php echo $successful_orders; ?></p>
            </div>
            
            <div class="stat-box">
                <h3><?php _e( 'Pending Payments', 'marzpay' ); ?></h3>
                <p class="stat-number pending"><?php echo $pending_orders; ?></p>
            </div>
            
            <div class="stat-box">
                <h3><?php _e( 'Failed Payments', 'marzpay' ); ?></h3>
                <p class="stat-number failed"><?php echo $failed_orders; ?></p>
            </div>
            
            <div class="stat-box">
                <h3><?php _e( 'Total Amount', 'marzpay' ); ?></h3>
                <p class="stat-number"><?php echo wc_price( $total_amount ); ?></p>
            </div>
        </div>
    </div>
    
    <div class="marzpay-orders-table">
        <h2><?php _e( 'Recent MarzPay Orders', 'marzpay' ); ?></h2>
        
        <?php if ( empty( $orders ) ) : ?>
            <p><?php _e( 'No MarzPay orders found.', 'marzpay' ); ?></p>
        <?php else : ?>
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e( 'Order', 'marzpay' ); ?></th>
                        <th><?php _e( 'Customer', 'marzpay' ); ?></th>
                        <th><?php _e( 'Amount', 'marzpay' ); ?></th>
                        <th><?php _e( 'Phone Number', 'marzpay' ); ?></th>
                        <th><?php _e( 'MarzPay Status', 'marzpay' ); ?></th>
                        <th><?php _e( 'Order Status', 'marzpay' ); ?></th>
                        <th><?php _e( 'Date', 'marzpay' ); ?></th>
                        <th><?php _e( 'Actions', 'marzpay' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $orders as $order ) : ?>
                        <?php
                        $transaction_uuid = $order->get_meta( '_marzpay_transaction_uuid' );
                        $phone = $order->get_meta( '_marzpay_phone' );
                        $marzpay_status = $order->get_meta( '_marzpay_status' );
                        $order_status = $order->get_status();
                        $customer = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
                        ?>
                        <tr>
                            <td>
                                <a href="<?php echo admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ); ?>">
                                    #<?php echo $order->get_order_number(); ?>
                                </a>
                            </td>
                            <td><?php echo esc_html( $customer ); ?></td>
                            <td><?php echo wc_price( $order->get_total() ); ?></td>
                            <td><?php echo esc_html( $phone ?: 'N/A' ); ?></td>
                            <td>
                                <?php if ( $marzpay_status ) : ?>
                                    <span class="marzpay-status marzpay-status-<?php echo esc_attr( $marzpay_status ); ?>">
                                        <?php echo esc_html( ucfirst( $marzpay_status ) ); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="marzpay-status marzpay-status-unknown"><?php _e( 'Unknown', 'marzpay' ); ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="order-status order-status-<?php echo esc_attr( $order_status ); ?>">
                                    <?php echo esc_html( wc_get_order_status_name( $order_status ) ); ?>
                                </span>
                            </td>
                            <td><?php echo $order->get_date_created()->date_i18n( get_option( 'date_format' ) ); ?></td>
                            <td>
                                <a href="<?php echo admin_url( 'post.php?post=' . $order->get_id() . '&action=edit' ); ?>" class="button button-small">
                                    <?php _e( 'View', 'marzpay' ); ?>
                                </a>
                                <?php if ( $transaction_uuid ) : ?>
                                    <button class="button button-small check-status" data-uuid="<?php echo esc_attr( $transaction_uuid ); ?>" data-order-id="<?php echo $order->get_id(); ?>">
                                        <?php _e( 'Check Status', 'marzpay' ); ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <div class="marzpay-woocommerce-actions">
        <h2><?php _e( 'Bulk Actions', 'marzpay' ); ?></h2>
        <p>
            <a href="<?php echo admin_url( 'edit.php?post_type=shop_order&payment_method=marzpay' ); ?>" class="button button-primary">
                <?php _e( 'View All MarzPay Orders in WooCommerce', 'marzpay' ); ?>
            </a>
            <button class="button button-secondary" id="refresh-all-statuses">
                <?php _e( 'Refresh All Payment Statuses', 'marzpay' ); ?>
            </button>
        </p>
    </div>
</div>

<style>
.marzpay-woocommerce-stats {
    margin: 20px 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-box {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-box h3 {
    margin: 0 0 10px 0;
    font-size: 14px;
    color: #666;
    text-transform: uppercase;
}

.stat-number {
    font-size: 32px;
    font-weight: bold;
    margin: 0;
    color: #333;
}

.stat-number.success {
    color: #46b450;
}

.stat-number.pending {
    color: #ffb900;
}

.stat-number.failed {
    color: #dc3232;
}

.marzpay-orders-table {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.marzpay-status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.marzpay-status-successful,
.marzpay-status-completed {
    background: #d4edda;
    color: #155724;
}

.marzpay-status-pending,
.marzpay-status-processing {
    background: #fff3cd;
    color: #856404;
}

.marzpay-status-failed,
.marzpay-status-cancelled {
    background: #f8d7da;
    color: #721c24;
}

.marzpay-status-unknown {
    background: #e2e3e5;
    color: #383d41;
}

.order-status {
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
    font-weight: bold;
    text-transform: uppercase;
}

.order-status-pending {
    background: #fff3cd;
    color: #856404;
}

.order-status-processing {
    background: #cce5ff;
    color: #004085;
}

.order-status-completed {
    background: #d4edda;
    color: #155724;
}

.order-status-failed {
    background: #f8d7da;
    color: #721c24;
}

.marzpay-woocommerce-actions {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 20px;
    margin: 20px 0;
}

.check-status {
    margin-left: 5px;
}

#refresh-all-statuses {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Check individual payment status
    $('.check-status').on('click', function() {
        var button = $(this);
        var uuid = button.data('uuid');
        var orderId = button.data('order-id');
        
        button.prop('disabled', true).text('<?php _e( 'Checking...', 'marzpay' ); ?>');
        
        $.ajax({
            url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
            type: 'POST',
            data: {
                action: 'marzpay_check_woocommerce_status',
                uuid: uuid,
                order_id: orderId,
                nonce: '<?php echo wp_create_nonce( 'marzpay_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('<?php _e( 'Connection error. Please try again.', 'marzpay' ); ?>');
            },
            complete: function() {
                button.prop('disabled', false).text('<?php _e( 'Check Status', 'marzpay' ); ?>');
            }
        });
    });
    
    // Refresh all statuses
    $('#refresh-all-statuses').on('click', function() {
        if (confirm('<?php _e( 'This will check the payment status for all MarzPay orders. Continue?', 'marzpay' ); ?>')) {
            var button = $(this);
            button.prop('disabled', true).text('<?php _e( 'Refreshing...', 'marzpay' ); ?>');
            
            $.ajax({
                url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                type: 'POST',
                data: {
                    action: 'marzpay_refresh_all_statuses',
                    nonce: '<?php echo wp_create_nonce( 'marzpay_nonce' ); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                },
                error: function() {
                    alert('<?php _e( 'Connection error. Please try again.', 'marzpay' ); ?>');
                },
                complete: function() {
                    button.prop('disabled', false).text('<?php _e( 'Refresh All Payment Statuses', 'marzpay' ); ?>');
                }
            });
        }
    });
});
</script>
