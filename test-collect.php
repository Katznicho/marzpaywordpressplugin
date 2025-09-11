<?php
/**
 * Test page for MarzPay Collect Money functionality
 * This is a WordPress-compatible test page
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is logged in and has proper permissions
if (!is_user_logged_in() || !current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to access this test page.');
}

// Get the nonce for AJAX requests
$nonce = wp_create_nonce('marzpay_nonce');
$ajax_url = admin_url('admin-ajax.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MarzPay Collect Money Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background: #f1f1f1;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #0073aa;
        }
        .description {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        button {
            width: 100%;
            padding: 15px;
            background: #0073aa;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #005a87;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            border-radius: 6px;
            display: none;
        }
        .result.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .result.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .result.loading {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            margin-top: 10px;
            font-family: monospace;
            font-size: 12px;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 MarzPay Collect Money Test</h1>
        
        <form id="collectForm">
            <div class="form-group">
                <label for="amount">Amount (UGX) *</label>
                <input type="number" id="amount" name="amount" value="1000" min="100" required>
                <div class="description">Minimum: 100 UGX</div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" value="0701234567" placeholder="0701234567" required>
                <div class="description">Enter Uganda phone number (e.g., 0701234567)</div>
            </div>
            
            <div class="form-group">
                <label for="reference">Reference</label>
                <input type="text" id="reference" name="reference" value="" placeholder="Optional reference">
                <div class="description">Optional reference - if empty, MarzPay will generate one automatically</div>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3" placeholder="Optional description">Test payment collection</textarea>
                <div class="description">Optional description for this transaction</div>
            </div>
            
            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country">
                    <option value="UG">Uganda</option>
                    <option value="KE">Kenya</option>
                    <option value="TZ">Tanzania</option>
                </select>
                <div class="description">Select the country for the phone number</div>
            </div>
            
            <button type="submit" id="submitBtn">Send Payment Request</button>
        </form>
        
        <div id="result" class="result"></div>
    </div>

    <script>
        document.getElementById('collectForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const resultDiv = document.getElementById('result');
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            resultDiv.className = 'result loading';
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = 'Sending payment request...';
            
            // Get form data
            const formData = new FormData(this);
            formData.append('action', 'marzpay_collect_money');
            formData.append('nonce', '<?php echo $nonce; ?>');
            
            // Send AJAX request
            fetch('<?php echo $ajax_url; ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        console.error('Non-JSON response:', text);
                        throw new Error('Server returned non-JSON response: ' + text.substring(0, 200));
                    });
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                
                if (data.success) {
                    resultDiv.className = 'result success';
                    resultDiv.innerHTML = `
                        <strong>✅ Success!</strong><br>
                        Payment request sent successfully!<br><br>
                        <strong>Response:</strong><br>
                        <div class="debug-info">${JSON.stringify(data, null, 2)}</div>
                    `;
                } else {
                    resultDiv.className = 'result error';
                    resultDiv.innerHTML = `
                        <strong>❌ Error:</strong><br>
                        ${data.data?.message || 'Unknown error occurred'}<br><br>
                        <strong>Full Response:</strong><br>
                        <div class="debug-info">${JSON.stringify(data, null, 2)}</div>
                    `;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                resultDiv.className = 'result error';
                resultDiv.innerHTML = `
                    <strong>❌ Network Error:</strong><br>
                    ${error.message}<br><br>
                    <strong>Debug Info:</strong><br>
                    <div class="debug-info">Check browser console for more details</div>
                `;
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Send Payment Request';
            });
        });
    </script>
</body>
</html>
