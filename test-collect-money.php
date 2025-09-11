<?php
/**
 * Test page for MarzPay Collect Money functionality
 * 
 * This is a temporary test file to verify the collect money feature works.
 * You can access this at: http://localhost/wordpress/wp-content/plugins/marzpay-collections/test-collect-money.php
 */

// Load WordPress
require_once( '../../../wp-load.php' );

// Check if user is logged in and has admin privileges
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'You must be logged in as an administrator to access this test page.' );
}

// Get the plugin instance
$marzpay_plugin = MarzPay_Plugin::get_instance();
?>

<!DOCTYPE html>
<html>
<head>
    <title>MarzPay Collect Money Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        .info { background: #d1ecf1; border-color: #bee5eb; }
        .marzpay-collect-form { max-width: 500px; }
        .form-group { margin: 15px 0; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group textarea, .form-group select { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .form-group small { color: #666; font-size: 12px; }
        .button { background: #0073aa; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .button:hover { background: #005a87; }
        .button:disabled { background: #ccc; cursor: not-allowed; }
        .result { margin: 10px 0; padding: 10px; border-radius: 4px; }
        .result.success { background: #d4edda; color: #155724; }
        .result.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>MarzPay Collect Money Test</h1>
    
    <div class="test-section info">
        <h2>Test Information</h2>
        <p>This page tests the MarzPay collect money functionality. Make sure you have:</p>
        <ul>
            <li>✅ API credentials configured in MarzPay Settings</li>
            <li>✅ Valid test phone numbers for Uganda (MTN/Airtel)</li>
            <li>✅ Test amounts within limits (500 - 10,000,000 UGX)</li>
        </ul>
    </div>

    <?php
    // Test API configuration
    $api_client = MarzPay_API_Client::get_instance();
    if ( $api_client->is_configured() ) {
        echo '<div class="test-section success">';
        echo '<h2>✅ API Configuration</h2>';
        echo '<p>API credentials are configured and ready for testing.</p>';
        echo '</div>';
    } else {
        echo '<div class="test-section error">';
        echo '<h2>❌ API Configuration</h2>';
        echo '<p>API credentials are not configured. Please go to <a href="' . admin_url( 'admin.php?page=marzpay-settings' ) . '">MarzPay Settings</a> to configure your API credentials.</p>';
        echo '</div>';
    }
    ?>

    <div class="test-section">
        <h2>Collect Money Form Test</h2>
        <p>Test the collect money shortcode functionality:</p>
        
        <?php
        // Test the shortcode
        echo do_shortcode( '[marzpay_collect]' );
        ?>
    </div>

    <div class="test-section">
        <h2>Manual Test Data</h2>
        <p>You can use these test values:</p>
        <ul>
            <li><strong>Amount:</strong> 1000 (UGX)</li>
            <li><strong>Phone:</strong> 0701234567 (MTN) or 0751234567 (Airtel)</li>
            <li><strong>Reference:</strong> test_order_001</li>
            <li><strong>Description:</strong> Test payment collection</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>Test Results</h2>
        <div id="test-results">
            <p>Submit the form above to see test results here.</p>
        </div>
    </div>

    <script>
    // Add some JavaScript to handle form submission and show results
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.marzpay-collect-form');
        const resultsDiv = document.getElementById('test-results');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.textContent;
                
                // Show loading state
                submitButton.disabled = true;
                submitButton.textContent = 'Processing...';
                resultsDiv.innerHTML = '<p>Processing payment request...</p>';
                
                // Get form data
                const formData = new FormData(form);
                formData.append('action', 'marzpay_collect_money');
                formData.append('nonce', '<?php echo wp_create_nonce( 'marzpay_nonce' ); ?>');
                
                // Submit via AJAX
                fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resultsDiv.innerHTML = '<div class="result success"><h3>✅ Success!</h3><p>' + data.data.message + '</p><pre>' + JSON.stringify(data.data, null, 2) + '</pre></div>';
                    } else {
                        resultsDiv.innerHTML = '<div class="result error"><h3>❌ Error</h3><p>' + data.data.message + '</p></div>';
                    }
                })
                .catch(error => {
                    resultsDiv.innerHTML = '<div class="result error"><h3>❌ Network Error</h3><p>Failed to submit request: ' + error.message + '</p></div>';
                })
                .finally(() => {
                    // Reset button
                    submitButton.disabled = false;
                    submitButton.textContent = originalText;
                });
            });
        }
    });
    </script>
</body>
</html>
