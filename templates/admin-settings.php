<?php
/**
 * Admin Settings Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
    <h1><?php _e( 'MarzPay Settings', 'marzpay' ); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field( 'marzpay_save_settings', 'marzpay_settings_nonce' ); ?>
        
        <div class="marzpay-settings-tabs">
            <nav class="nav-tab-wrapper">
                <a href="#api-settings" class="nav-tab nav-tab-active"><?php _e( 'API Settings', 'marzpay' ); ?></a>
                <a href="#webhook-settings" class="nav-tab"><?php _e( 'Webhook Settings', 'marzpay' ); ?></a>
                <a href="#general-settings" class="nav-tab"><?php _e( 'General Settings', 'marzpay' ); ?></a>
            </nav>
            
            <div id="api-settings" class="tab-content active">
                <h2><?php _e( 'API Configuration', 'marzpay' ); ?></h2>
                <p><?php _e( 'Configure your MarzPay API credentials and environment settings.', 'marzpay' ); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="marzpay_api_user"><?php _e( 'API User', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="marzpay_api_user" name="marzpay_api_user" 
                                   value="<?php echo esc_attr( get_option( 'marzpay_api_user' ) ); ?>" 
                                   class="regular-text" required />
                            <p class="description"><?php _e( 'Your MarzPay API username.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="marzpay_api_key"><?php _e( 'API Key', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <input type="password" id="marzpay_api_key" name="marzpay_api_key" 
                                   value="<?php echo esc_attr( get_option( 'marzpay_api_key' ) ); ?>" 
                                   class="regular-text" required />
                            <p class="description"><?php _e( 'Your MarzPay API key.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="marzpay_environment"><?php _e( 'Environment', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <select id="marzpay_environment" name="marzpay_environment">
                                <option value="test" <?php selected( get_option( 'marzpay_environment', 'test' ), 'test' ); ?>>
                                    <?php _e( 'Test', 'marzpay' ); ?>
                                </option>
                                <option value="production" <?php selected( get_option( 'marzpay_environment' ), 'production' ); ?>>
                                    <?php _e( 'Production', 'marzpay' ); ?>
                                </option>
                            </select>
                            <p class="description"><?php _e( 'Select the environment for API calls.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <div class="marzpay-api-test">
                    <button type="button" id="test-api-connection" class="button button-secondary">
                        <?php _e( 'Test API Connection', 'marzpay' ); ?>
                    </button>
                    <div id="api-test-result"></div>
                </div>
            </div>
            
            <div id="webhook-settings" class="tab-content">
                <h2><?php _e( 'Webhook Configuration', 'marzpay' ); ?></h2>
                <p><?php _e( 'Configure webhook settings for receiving payment notifications.', 'marzpay' ); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="marzpay_webhook_secret"><?php _e( 'Webhook Secret', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="marzpay_webhook_secret" name="marzpay_webhook_secret" 
                                   value="<?php echo esc_attr( get_option( 'marzpay_webhook_secret' ) ); ?>" 
                                   class="regular-text" />
                            <button type="button" id="generate-webhook-secret" class="button button-secondary">
                                <?php _e( 'Generate New Secret', 'marzpay' ); ?>
                            </button>
                            <p class="description"><?php _e( 'Secret key for webhook signature verification.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <div class="marzpay-webhook-urls">
                    <h3><?php _e( 'Webhook URLs', 'marzpay' ); ?></h3>
                    <p><?php _e( 'Use these URLs in your MarzPay dashboard to receive webhook notifications:', 'marzpay' ); ?></p>
                    
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e( 'Airtel Callback', 'marzpay' ); ?></th>
                            <td>
                                <code><?php echo marzpay_get_webhook_url( 'airtel' ); ?></code>
                                <button type="button" class="button button-secondary copy-webhook-url" data-url="<?php echo marzpay_get_webhook_url( 'airtel' ); ?>">
                                    <?php _e( 'Copy', 'marzpay' ); ?>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'MTN Callback', 'marzpay' ); ?></th>
                            <td>
                                <code><?php echo marzpay_get_webhook_url( 'mtn' ); ?></code>
                                <button type="button" class="button button-secondary copy-webhook-url" data-url="<?php echo marzpay_get_webhook_url( 'mtn' ); ?>">
                                    <?php _e( 'Copy', 'marzpay' ); ?>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'MTN Disbursement Callback', 'marzpay' ); ?></th>
                            <td>
                                <code><?php echo marzpay_get_webhook_url( 'mtn-disbursement' ); ?></code>
                                <button type="button" class="button button-secondary copy-webhook-url" data-url="<?php echo marzpay_get_webhook_url( 'mtn-disbursement' ); ?>">
                                    <?php _e( 'Copy', 'marzpay' ); ?>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div id="general-settings" class="tab-content">
                <h2><?php _e( 'General Settings', 'marzpay' ); ?></h2>
                <p><?php _e( 'Configure general plugin settings.', 'marzpay' ); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="marzpay_default_currency"><?php _e( 'Default Currency', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <select id="marzpay_default_currency" name="marzpay_default_currency">
                                <option value="UGX" <?php selected( get_option( 'marzpay_default_currency', 'UGX' ), 'UGX' ); ?>>
                                    UGX - Ugandan Shilling
                                </option>
                            </select>
                            <p class="description"><?php _e( 'Default currency for transactions.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="marzpay_default_country"><?php _e( 'Default Country', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <select id="marzpay_default_country" name="marzpay_default_country">
                                <option value="UG" <?php selected( get_option( 'marzpay_default_country', 'UG' ), 'UG' ); ?>>
                                    UG - Uganda
                                </option>
                            </select>
                            <p class="description"><?php _e( 'Default country for transactions.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="marzpay_success_page"><?php _e( 'Success Page', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_pages( array(
                                'name' => 'marzpay_success_page',
                                'selected' => get_option( 'marzpay_success_page' ),
                                'show_option_none' => __( 'Select a page', 'marzpay' ),
                                'option_none_value' => 0
                            ) );
                            ?>
                            <p class="description"><?php _e( 'Page to redirect users after successful payment.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="marzpay_failure_page"><?php _e( 'Failure Page', 'marzpay' ); ?></label>
                        </th>
                        <td>
                            <?php
                            wp_dropdown_pages( array(
                                'name' => 'marzpay_failure_page',
                                'selected' => get_option( 'marzpay_failure_page' ),
                                'show_option_none' => __( 'Select a page', 'marzpay' ),
                                'option_none_value' => 0
                            ) );
                            ?>
                            <p class="description"><?php _e( 'Page to redirect users after failed payment.', 'marzpay' ); ?></p>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <?php submit_button( __( 'Save Settings', 'marzpay' ) ); ?>
    </form>
</div>

<style>
.marzpay-settings-tabs .nav-tab-wrapper {
    margin-bottom: 20px;
}

.marzpay-settings-tabs .tab-content {
    display: none;
}

.marzpay-settings-tabs .tab-content.active {
    display: block;
}

.marzpay-api-test {
    margin-top: 20px;
    padding: 15px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.marzpay-webhook-urls {
    margin-top: 20px;
}

.marzpay-webhook-urls code {
    display: inline-block;
    padding: 5px 10px;
    background: #f1f1f1;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-family: monospace;
    margin-right: 10px;
    max-width: 400px;
    word-break: break-all;
}

.copy-webhook-url {
    margin-left: 10px;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        
        $('.nav-tab').removeClass('nav-tab-active');
        $('.tab-content').removeClass('active');
        
        $(this).addClass('nav-tab-active');
        $($(this).attr('href')).addClass('active');
    });
    
    // Test API connection
    $('#test-api-connection').on('click', function() {
        var button = $(this);
        var result = $('#api-test-result');
        
        button.prop('disabled', true).text('<?php _e( 'Testing...', 'marzpay' ); ?>');
        result.html('');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'marzpay_test_api',
                nonce: '<?php echo wp_create_nonce( 'marzpay_nonce' ); ?>'
            },
            success: function(response) {
                if (response.success) {
                    result.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
                } else {
                    result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                result.html('<div class="notice notice-error inline"><p><?php _e( 'Connection test failed.', 'marzpay' ); ?></p></div>');
            },
            complete: function() {
                button.prop('disabled', false).text('<?php _e( 'Test API Connection', 'marzpay' ); ?>');
            }
        });
    });
    
    // Generate webhook secret
    $('#generate-webhook-secret').on('click', function() {
        var secret = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        $('#marzpay_webhook_secret').val(secret);
    });
    
    // Copy webhook URL
    $('.copy-webhook-url').on('click', function() {
        var url = $(this).data('url');
        navigator.clipboard.writeText(url).then(function() {
            alert('<?php _e( 'URL copied to clipboard!', 'marzpay' ); ?>');
        });
    });
});
</script>
