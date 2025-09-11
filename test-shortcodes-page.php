<?php
/**
 * Test page to demonstrate all MarzPay shortcodes
 * This shows how users would use the plugin in their WordPress sites
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is logged in and has proper permissions
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to access this test page.');
}

get_header();
?>

<div class="container" style="max-width: 1200px; margin: 40px auto; padding: 20px;">
    <h1>🧪 MarzPay Plugin - Shortcode Testing</h1>
    <p>This page demonstrates how users can use MarzPay shortcodes in their WordPress sites.</p>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px;">
        
        <!-- Collect Money Shortcode -->
        <div style="border: 2px solid #0073aa; border-radius: 8px; padding: 20px;">
            <h2>💰 Collect Money</h2>
            <p><strong>Shortcode:</strong> <code>[marzpay_collect]</code></p>
            <p>Allow customers to send you money via mobile money.</p>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <h3>Live Demo:</h3>
                <?php echo do_shortcode('[marzpay_collect]'); ?>
            </div>
        </div>
        
        <!-- Send Money Shortcode -->
        <div style="border: 2px solid #28a745; border-radius: 8px; padding: 20px;">
            <h2>💸 Send Money</h2>
            <p><strong>Shortcode:</strong> <code>[marzpay_send]</code></p>
            <p>Send money to customers via mobile money.</p>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <h3>Live Demo:</h3>
                <?php echo do_shortcode('[marzpay_send]'); ?>
            </div>
        </div>
        
        <!-- Balance Shortcode -->
        <div style="border: 2px solid #ffc107; border-radius: 8px; padding: 20px;">
            <h2>💳 Account Balance</h2>
            <p><strong>Shortcode:</strong> <code>[marzpay_balance]</code></p>
            <p>Display your current account balance.</p>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <h3>Live Demo:</h3>
                <?php echo do_shortcode('[marzpay_balance]'); ?>
            </div>
        </div>
        
        <!-- Transactions Shortcode -->
        <div style="border: 2px solid #6f42c1; border-radius: 8px; padding: 20px;">
            <h2>📊 Recent Transactions</h2>
            <p><strong>Shortcode:</strong> <code>[marzpay_transactions]</code></p>
            <p>Show recent transactions.</p>
            
            <div style="background: #f9f9f9; padding: 15px; border-radius: 4px; margin: 15px 0;">
                <h3>Live Demo:</h3>
                <?php echo do_shortcode('[marzpay_transactions]'); ?>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 40px; padding: 20px; background: #e7f3ff; border-radius: 8px;">
        <h2>📖 How to Use in Your WordPress Site</h2>
        <ol>
            <li><strong>Install the Plugin:</strong> Upload and activate the MarzPay plugin</li>
            <li><strong>Configure API:</strong> Go to MarzPay → Settings and enter your API credentials</li>
            <li><strong>Add Shortcodes:</strong> Use the shortcodes above in any page, post, or widget</li>
            <li><strong>Customize:</strong> Use shortcode attributes to customize the forms</li>
        </ol>
        
        <h3>Shortcode Attributes:</h3>
        <ul>
            <li><code>[marzpay_collect amount="1000" phone="+256759983853"]</code></li>
            <li><code>[marzpay_send amount="500" phone="+256700000000"]</code></li>
            <li><code>[marzpay_balance show_currency="true"]</code></li>
            <li><code>[marzpay_transactions limit="10"]</code></li>
        </ul>
    </div>
</div>

<style>
/* Ensure shortcode forms look good */
.marzpay-collect-form, .marzpay-send-form {
    max-width: 100%;
}

.marzpay-form .form-group {
    margin-bottom: 15px;
}

.marzpay-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.marzpay-form input, .marzpay-form textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
}

.marzpay-button {
    background: #0073aa;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
}

.marzpay-button:hover {
    background: #005a87;
}
</style>

<?php get_footer(); ?>
