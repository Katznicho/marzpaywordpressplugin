<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register MarzPay payment button shortcode
 */
add_shortcode('marzpay_button', 'marzpay_button_shortcode');

function marzpay_button_shortcode($atts) {
    // Set default attributes
    $atts = shortcode_atts(array(
        'amount' => '1000',
        'phone'  => ''
    ), $atts, 'marzpay_button');

    // Validate phone number
    if (empty($atts['phone'])) {
        return '<p style="color:red; font-weight: bold;">❌ Phone number is required. Use: [marzpay_button phone="256759983853"]</p>';
    }

    // Validate amount
    if (!is_numeric($atts['amount']) || $atts['amount'] <= 0) {
        return '<p style="color:red; font-weight: bold;">❌ Invalid amount. Amount must be a positive number.</p>';
    }
    
    // Check amount limits according to MarzPay requirements
    if ($atts['amount'] < 500) {
        return '<p style="color:red; font-weight: bold;">❌ Amount too low. The minimum amount for collection is 500 UGX.</p>';
    }
    
    if ($atts['amount'] > 10000000) {
        return '<p style="color:red; font-weight: bold;">❌ Amount too high. The maximum amount for collection is 10,000,000 UGX.</p>';
    }

    // Check if API credentials are configured
    $api_user = get_option('marzpay_api_user');
    $api_key = get_option('marzpay_api_key');
    
    if (empty($api_user) || empty($api_key)) {
        return '<p style="color:red; font-weight: bold;">❌ MarzPay API credentials not configured. Please go to Settings > MarzPay to configure your API settings.</p>';
    }

    $output = '';

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['marzpay_phone']) && $_POST['marzpay_phone'] === $atts['phone']) {
        $amount = sanitize_text_field($_POST['marzpay_amount']);
        $phone  = sanitize_text_field($_POST['marzpay_phone']);
        
        // Add debugging info if WP_DEBUG is enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $output .= '<div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-left: 4px solid #0073aa;">
                <strong>Debug Info:</strong><br>
                Amount: ' . esc_html($amount) . ' UGX<br>
                Phone (original): ' . esc_html($phone) . '<br>
                API User: ' . esc_html($api_user) . '<br>
                API Key: ' . esc_html(substr($api_key, 0, 8)) . '...<br>
                Reference: ' . esc_html(uniqid('order_')) . '
            </div>';
        }
        
        $result = marzpay_request_payment($amount, $phone);

        if (isset($result['status']) && $result['status'] === 'success') {
            $output .= '<p style="color:green; font-weight: bold;">✅ Payment request sent successfully to ' . esc_html($phone) . '.</p>';
            
            // Show additional success details if available
            if (isset($result['reference'])) {
                $output .= '<p><strong>Reference:</strong> ' . esc_html($result['reference']) . '</p>';
            }
            if (isset($result['message'])) {
                $output .= '<p><strong>Message:</strong> ' . esc_html($result['message']) . '</p>';
            }
        } else {
            $error = isset($result['message']) ? $result['message'] : 'Payment request failed.';
            $output .= '<p style="color:red; font-weight: bold;">❌ ' . esc_html($error) . '</p>';
            
            // Show additional error details if available
            if (isset($result['code'])) {
                $output .= '<p><strong>Error Code:</strong> ' . esc_html($result['code']) . '</p>';
            }
            if (isset($result['status_code'])) {
                $output .= '<p><strong>HTTP Status:</strong> ' . esc_html($result['status_code']) . '</p>';
            }
            
            // Add troubleshooting tips
            $output .= '<div style="background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffeaa7; border-radius: 4px;">
                <strong>💡 Troubleshooting Tips:</strong><br>
                • Check your API credentials in Settings > MarzPay<br>
                • Verify the phone number format (256759983853, 0759983853, or +256759983853)<br>
                • Ensure the amount is between 500 and 10,000,000 UGX<br>
                • Check your WordPress error logs if WP_DEBUG is enabled<br>
                • Test the API connection from the admin panel
            </div>';
        }
    }

    // Display the payment button
    $output .= '<form method="post" style="margin-top:10px;">
                    <input type="hidden" name="marzpay_phone" value="' . esc_attr($atts['phone']) . '">
                    <input type="hidden" name="marzpay_amount" value="' . esc_attr($atts['amount']) . '">
                    <button type="submit" style="
                        background-color: #0073aa; 
                        color: white; 
                        padding: 12px 24px; 
                        border: none; 
                        border-radius: 6px; 
                        cursor: pointer;
                        font-size: 16px;
                        transition: background-color 0.3s ease;
                    " 
                    onmouseover="this.style.backgroundColor=\'#005177\'" 
                    onmouseout="this.style.backgroundColor=\'#0073aa\'">
                        Pay UGX ' . esc_html($atts['amount']) . '
                    </button>
                </form>';
                
    // Add amount requirements info
    $output .= '<div style="background: #e7f3ff; padding: 8px 12px; margin: 10px 0; border-left: 4px solid #0073aa; border-radius: 4px; font-size: 12px; color: #005177;">
        <strong>ℹ️ Amount Requirements:</strong> Minimum: 500 UGX | Maximum: 10,000,000 UGX
    </div>';

    return $output;
}
