<?php
/**
 * Admin Documentation Template
 */

if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap">
    <h1><?php _e( 'MarzPay Documentation', 'marzpay' ); ?></h1>
    
    <div class="marzpay-documentation">
        
        <!-- Quick Start -->
        <div class="marzpay-doc-section">
            <h2>🚀 Quick Start</h2>
            <ol>
                <li>Go to <strong>MarzPay → Settings</strong> and enter your API credentials</li>
                <li>Use the shortcodes below in any page, post, or widget</li>
                <li>Customize the shortcodes with attributes as needed</li>
            </ol>
        </div>
        
        <!-- Shortcodes -->
        <div class="marzpay-doc-section">
            <h2>📝 Available Shortcodes</h2>
            
            <div class="shortcode-example">
                <h3>💰 Collect Money</h3>
                <p>Allow customers to send you money via mobile money.</p>
                <div class="code-block">
                    <code>[marzpay_collect]</code>
                </div>
                
                <h4>With Custom Attributes:</h4>
                <div class="code-block">
                    <code>[marzpay_collect amount="1000" phone="+256759983853" description="Payment for services" button_text="Pay Now"]</code>
                </div>
                
                <h4>Available Attributes:</h4>
                <ul>
                    <li><code>amount</code> - Default amount (optional)</li>
                    <li><code>phone</code> - Default phone number (optional)</li>
                    <li><code>description</code> - Default description (optional)</li>
                    <li><code>button_text</code> - Custom button text (default: "Request Payment")</li>
                </ul>
            </div>
            
            <div class="shortcode-example">
                <h3>💸 Send Money</h3>
                <p>Send money to customers via mobile money.</p>
                <div class="code-block">
                    <code>[marzpay_send]</code>
                </div>
                
                <h4>With Custom Attributes:</h4>
                <div class="code-block">
                    <code>[marzpay_send amount="500" phone="+256700000000" description="Refund payment" button_text="Send Refund"]</code>
                </div>
                
                <h4>Available Attributes:</h4>
                <ul>
                    <li><code>amount</code> - Default amount (optional)</li>
                    <li><code>phone</code> - Default phone number (optional)</li>
                    <li><code>description</code> - Default description (optional)</li>
                    <li><code>button_text</code> - Custom button text (default: "Send Money")</li>
                </ul>
            </div>
            
            <div class="shortcode-example">
                <h3>💳 Account Balance</h3>
                <p>Display your current account balance.</p>
                <div class="code-block">
                    <code>[marzpay_balance]</code>
                </div>
                
                <h4>With Custom Attributes:</h4>
                <div class="code-block">
                    <code>[marzpay_balance show_currency="true"]</code>
                </div>
                
                <h4>Available Attributes:</h4>
                <ul>
                    <li><code>show_currency</code> - Show currency symbol (true/false)</li>
                </ul>
            </div>
            
            <div class="shortcode-example">
                <h3>📊 Recent Transactions</h3>
                <p>Show recent transactions.</p>
                <div class="code-block">
                    <code>[marzpay_transactions]</code>
                </div>
                
                <h4>With Custom Attributes:</h4>
                <div class="code-block">
                    <code>[marzpay_transactions limit="10"]</code>
                </div>
                
                <h4>Available Attributes:</h4>
                <ul>
                    <li><code>limit</code> - Number of transactions to show (default: 10)</li>
                </ul>
            </div>
        </div>
        
        <!-- Usage Examples -->
        <div class="marzpay-doc-section">
            <h2>💡 Usage Examples</h2>
            
            <div class="example-box">
                <h3>E-commerce Checkout</h3>
                <p>Add a payment form to your checkout page:</p>
                <div class="code-block">
                    <code>[marzpay_collect amount="5000" description="Payment for Order #12345"]</code>
                </div>
            </div>
            
            <div class="example-box">
                <h3>Service Payment</h3>
                <p>Create a payment page for services:</p>
                <div class="code-block">
                    <code>[marzpay_collect amount="10000" description="Consulting Services" button_text="Pay for Services"]</code>
                </div>
            </div>
            
            <div class="example-box">
                <h3>Refund System</h3>
                <p>Send refunds to customers:</p>
                <div class="code-block">
                    <code>[marzpay_send amount="2500" description="Refund for cancelled order" button_text="Process Refund"]</code>
                </div>
            </div>
            
            <div class="example-box">
                <h3>Balance Display</h3>
                <p>Show your account balance on dashboard:</p>
                <div class="code-block">
                    <code>[marzpay_balance show_currency="true"]</code>
                </div>
            </div>
        </div>
        
        <!-- Phone Number Format -->
        <div class="marzpay-doc-section">
            <h2>📱 Phone Number Format</h2>
            <p><strong>Important:</strong> Always use international format with + sign:</p>
            <div class="code-block">
                <code>+256XXXXXXXXX</code>
            </div>
            <p><strong>Examples:</strong></p>
            <ul>
                <li><code>+256759983853</code> ✅ Correct</li>
                <li><code>0759983853</code> ❌ Will be converted automatically</li>
                <li><code>256759983853</code> ❌ Will be converted automatically</li>
            </ul>
        </div>
        
        <!-- Amount Limits -->
        <div class="marzpay-doc-section">
            <h2>💰 Amount Limits</h2>
            <ul>
                <li><strong>Minimum:</strong> 500 UGX</li>
                <li><strong>Maximum:</strong> 10,000,000 UGX</li>
                <li><strong>Currency:</strong> Uganda Shillings (UGX) only</li>
                <li><strong>Format:</strong> Whole numbers only (no decimals)</li>
            </ul>
        </div>
        
        <!-- Styling -->
        <div class="marzpay-doc-section">
            <h2>🎨 Custom Styling</h2>
            <p>Add this CSS to your theme to customize the appearance:</p>
            <div class="code-block">
<pre>.marzpay-collect-form, .marzpay-send-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.marzpay-button {
    background: #your-brand-color;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.marzpay-button:hover {
    background: #your-brand-color-dark;
}</pre>
            </div>
        </div>
        
        <!-- Troubleshooting -->
        <div class="marzpay-doc-section">
            <h2>🚨 Troubleshooting</h2>
            
            <h3>Common Issues:</h3>
            <ul>
                <li><strong>"API credentials not configured"</strong> - Go to MarzPay → Settings and enter your API credentials</li>
                <li><strong>"Invalid phone number format"</strong> - Use international format: +256XXXXXXXXX</li>
                <li><strong>"Amount must be between 500 and 10,000,000"</strong> - Check your amount is within limits</li>
                <li><strong>Forms not showing</strong> - Make sure plugin is activated and API is configured</li>
            </ul>
            
            <h3>Getting Help:</h3>
            <ul>
                <li>Check <strong>MarzPay → Dashboard</strong> for account status</li>
                <li>Test API connection in <strong>MarzPay → Settings</strong></li>
                <li>Check WordPress error logs for detailed messages</li>
                <li>Contact MarzPay support for API-related issues</li>
            </ul>
        </div>
        
    </div>
</div>

<style>
.marzpay-documentation {
    max-width: 1000px;
}

.marzpay-doc-section {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
    margin-bottom: 20px;
}

.shortcode-example {
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
}

.code-block {
    background: #2c3e50;
    color: #ecf0f1;
    padding: 10px 15px;
    border-radius: 4px;
    font-family: 'Courier New', monospace;
    margin: 10px 0;
    overflow-x: auto;
}

.code-block code {
    background: none;
    color: #ecf0f1;
    padding: 0;
    font-size: 14px;
}

.example-box {
    background: #e8f4fd;
    border: 1px solid #bee5eb;
    border-radius: 4px;
    padding: 15px;
    margin: 15px 0;
}

.marzpay-doc-section h2 {
    color: #0073aa;
    border-bottom: 2px solid #0073aa;
    padding-bottom: 10px;
}

.marzpay-doc-section h3 {
    color: #333;
    margin-top: 20px;
}

.marzpay-doc-section h4 {
    color: #666;
    margin-top: 15px;
}

.marzpay-doc-section ul {
    margin-left: 20px;
}

.marzpay-doc-section li {
    margin-bottom: 5px;
}
</style>
