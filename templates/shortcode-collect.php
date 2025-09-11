<?php
/**
 * Collect Money Shortcode Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="marzpay-collect-form">
    <?php if ( $atts['show_form'] === 'true' ) : ?>
        <form class="marzpay-form marzpay-collect-form" data-action="marzpay_collect_money">
            <div class="form-group">
                <label for="marzpay_amount"><?php _e( 'Amount (UGX)', 'marzpay' ); ?> *</label>
                <input type="number" id="marzpay_amount" name="amount" 
                       value="<?php echo esc_attr( $atts['amount'] ); ?>" 
                       min="500" max="10000000" required />
                <small class="description"><?php _e( 'Minimum: 500 UGX, Maximum: 10,000,000 UGX', 'marzpay' ); ?></small>
            </div>
            
            <div class="form-group">
                <label for="marzpay_phone"><?php _e( 'Phone Number', 'marzpay' ); ?> *</label>
                <input type="tel" id="marzpay_phone" name="phone" 
                       value="<?php echo esc_attr( $atts['phone'] ); ?>" 
                       placeholder="+256759983853" required />
                <small class="description"><?php _e( 'Enter Uganda phone number in international format (e.g., +256759983853)', 'marzpay' ); ?></small>
            </div>
            
            <div class="form-group">
                <label for="marzpay_reference"><?php _e( 'Reference', 'marzpay' ); ?></label>
                <input type="text" id="marzpay_reference" name="reference" 
                       value="<?php echo esc_attr( $atts['reference'] ); ?>" 
                       placeholder="Optional reference" />
                <small class="description"><?php _e( 'Optional reference for this transaction', 'marzpay' ); ?></small>
            </div>
            
            <div class="form-group">
                <label for="marzpay_description"><?php _e( 'Description', 'marzpay' ); ?></label>
                <textarea id="marzpay_description" name="description" rows="3" 
                          placeholder="<?php _e( 'Payment description (optional)', 'marzpay' ); ?>"><?php echo esc_textarea( $atts['description'] ); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="marzpay_callback_url"><?php _e( 'Callback URL', 'marzpay' ); ?></label>
                <input type="url" id="marzpay_callback_url" name="callback_url" 
                       value="<?php echo esc_attr( $atts['callback_url'] ); ?>" 
                       placeholder="https://yoursite.com/callback" />
                <small class="description"><?php _e( 'Optional callback URL for payment notifications', 'marzpay' ); ?></small>
            </div>
            
            <input type="hidden" name="country" value="<?php echo esc_attr( $atts['country'] ); ?>" />
            
            <button type="submit" class="marzpay-button">
                <?php echo esc_html( $atts['button_text'] ); ?>
            </button>
        </form>
    <?php else : ?>
        <div class="marzpay-info">
            <p><?php _e( 'Payment form is disabled. Please contact the administrator.', 'marzpay' ); ?></p>
        </div>
    <?php endif; ?>
</div>

<style>
.marzpay-collect-form {
    max-width: 500px;
    margin: 20px 0;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: #f9f9f9;
}

.marzpay-collect-form .form-group {
    margin-bottom: 20px;
}

.marzpay-collect-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.marzpay-collect-form input,
.marzpay-collect-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
}

.marzpay-collect-form input:focus,
.marzpay-collect-form textarea:focus {
    outline: none;
    border-color: #0073aa;
    box-shadow: 0 0 0 2px rgba(0,115,170,0.2);
}

.marzpay-collect-form .description {
    display: block;
    margin-top: 5px;
    font-size: 12px;
    color: #666;
    font-style: italic;
}

.marzpay-collect-form .marzpay-button {
    width: 100%;
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
    transition: background-color 0.3s ease;
}

.marzpay-collect-form .marzpay-button:hover {
    background: #005177;
}

.marzpay-collect-form .marzpay-button:disabled {
    background: #ccc;
    cursor: not-allowed;
}
</style>
