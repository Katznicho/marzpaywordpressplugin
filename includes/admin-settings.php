<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action('admin_menu', function() {
    add_options_page(
        __('MarzPay Settings', 'marzpay-collections'),
        'MarzPay',
        'manage_options',
        'marzpay-settings',
        'marzpay_settings_page'
    );
});

add_action('admin_init', function() {
    register_setting('marzpay_settings_group', 'marzpay_api_user');
    register_setting('marzpay_settings_group', 'marzpay_api_key');
    register_setting('marzpay_settings_group', 'marzpay_callback_url');
});

// Handle test API connection
add_action('admin_post_test_marzpay_api', 'test_marzpay_api_connection');

function test_marzpay_api_connection() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized');
    }

    // Verify nonce for security
    if (!isset($_POST['test_marzpay_api_nonce']) || !wp_verify_nonce($_POST['test_marzpay_api_nonce'], 'test_marzpay_api')) {
        wp_die('Security check failed. Please try again.');
    }

    $api_user = get_option('marzpay_api_user');
    $api_key = get_option('marzpay_api_key');

    if (empty($api_user) || empty($api_key)) {
        wp_redirect(add_query_arg('test_result', 'missing_credentials', admin_url('options-general.php?page=marzpay-settings')));
        exit;
    }

    // Get test phone number from form
    $test_phone = isset($_POST['test_phone']) ? sanitize_text_field($_POST['test_phone']) : '256759983853';
    
    // Test with the minimum required amount (500 UGX) and let the API client generate the UUID
    $result = marzpay_request_payment(500, $test_phone);
    
    if (isset($result['status']) && $result['status'] === 'success') {
        wp_redirect(add_query_arg('test_result', 'success', admin_url('options-general.php?page=marzpay-settings')));
    } else {
        $error_message = isset($result['message']) ? $result['message'] : 'Unknown error';
        
        // Truncate error message to avoid URI too long errors
        if (strlen($error_message) > 200) {
            $error_message = substr($error_message, 0, 200) . '...';
        }
        
        // Store detailed error in transient for display
        set_transient('marzpay_test_error_details', $result, 60);
        
        wp_redirect(add_query_arg(array(
            'test_result' => 'failed',
            'error_message' => urlencode($error_message)
        ), admin_url('options-general.php?page=marzpay-settings')));
    }
    exit;
}

function marzpay_settings_page() {
    $test_result = isset($_GET['test_result']) ? $_GET['test_result'] : '';
    $error_message = isset($_GET['error_message']) ? urldecode($_GET['error_message']) : '';
    $detailed_error = get_transient('marzpay_test_error_details');
    
    // Clear the transient after displaying
    if ($detailed_error) {
        delete_transient('marzpay_test_error_details');
    }
    
    ?>
    <div class="wrap">
        <h1><?php _e('MarzPay Settings', 'marzpay-collections'); ?></h1>
        
        <?php if ($test_result === 'success'): ?>
            <div class="notice notice-success">
                <p>✅ API connection test successful! Your MarzPay API credentials are working correctly.</p>
            </div>
        <?php elseif ($test_result === 'failed'): ?>
            <div class="notice notice-error">
                <p>❌ API connection test failed!</p>
                <p><strong>Error:</strong> <?php echo esc_html($error_message); ?></p>
                
                <?php if ($detailed_error): ?>
                    <details style="margin-top: 10px;">
                        <summary><strong>View Detailed Error Information</strong></summary>
                        <div style="background: #f9f9f9; padding: 10px; margin-top: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <pre><?php echo esc_html(print_r($detailed_error, true)); ?></pre>
                        </div>
                    </details>
                <?php endif; ?>
            </div>
        <?php elseif ($test_result === 'missing_credentials'): ?>
            <div class="notice notice-warning">
                <p>⚠️ Please enter your API credentials before testing the connection.</p>
            </div>
        <?php endif; ?>

        <form method="post" action="options.php">
            <?php settings_fields('marzpay_settings_group'); ?>
            <?php do_settings_sections('marzpay_settings_group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">API User</th>
                    <td>
                        <input type="text" name="marzpay_api_user" value="<?php echo esc_attr(get_option('marzpay_api_user')); ?>" class="regular-text" placeholder="your_api_username" />
                        <p class="description">Enter your MarzPay API username</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">API Key</th>
                    <td>
                        <input type="password" name="marzpay_api_key" value="<?php echo esc_attr(get_option('marzpay_api_key')); ?>" class="regular-text" placeholder="your_api_key_here" />
                        <p class="description">Enter your MarzPay API key</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Callback URL</th>
                    <td>
                        <input type="url" name="marzpay_callback_url" value="<?php echo esc_attr(get_option('marzpay_callback_url')); ?>" class="regular-text" placeholder="https://yoursite.com/marzpay-callback" />
                        <p class="description">URL where MarzPay will send payment notifications. Leave empty to use default: <?php echo home_url('/marzpay-callback'); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Settings', 'marzpay-collections')); ?>
        </form>

        <hr style="margin: 30px 0;">

        <h2>Test API Connection</h2>
        <p>Click the button below to test if your API credentials are working correctly:</p>
        <p><strong>Test Amount:</strong> 500 UGX (minimum required by MarzPay)</p>
        <p><strong>Test Phone:</strong> Enter your phone number below to test with your own number</p>
        <p><strong>Reference:</strong> Will be automatically generated as a valid UUID</p>
        
        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="test_marzpay_api">
            <?php wp_nonce_field('test_marzpay_api', 'test_marzpay_api_nonce'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Test Phone Number</th>
                    <td>
                        <input type="text" name="test_phone" value="256759983853" class="regular-text" placeholder="256759983853" />
                        <p class="description">Enter your phone number to test the API. Formats supported: 256759983853, 0759983853, or +256759983853</p>
                    </td>
                </tr>
            </table>
            
            <button type="submit" class="button button-secondary">Test API Connection</button>
        </form>

        <hr style="margin: 30px 0;">

        <h2>Shortcode Usage</h2>
        <p>Use this shortcode to display a payment button:</p>
        <code>[marzpay_button amount="1000" phone="256759983853"]</code>
        
        <h3>Parameters:</h3>
        <ul>
            <li><strong>amount</strong>: Payment amount in UGX (minimum: 500, maximum: 10,000,000)</li>
            <li><strong>phone</strong>: Phone number (required, will be prefixed with +256 if not present)</li>
        </ul>
        
        <h3>Phone Number Formats Supported:</h3>
        <ul>
            <li><code>256759983853</code> → converts to <code>+256759983853</code></li>
            <li><code>0759983853</code> → converts to <code>+256759983853</code></li>
            <li><code>+256759983853</code> → used as-is</li>
        </ul>
        
        <h3>Amount Requirements:</h3>
        <ul>
            <li><strong>Minimum:</strong> 500 UGX</li>
            <li><strong>Maximum:</strong> 10,000,000 UGX</li>
            <li><strong>Format:</strong> Whole numbers only (e.g., 1000 for UGX 1,000)</li>
        </ul>
    </div>
    <?php
}
