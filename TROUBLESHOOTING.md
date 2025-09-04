# MarzPay Collections Plugin - Troubleshooting Guide

## "Invalid API Response" Error - How to Fix

If you're getting the "Invalid API response" error when testing your MarzPay shortcode, follow these steps to identify and fix the issue:

### Step 1: Check API Credentials

1. Go to **WordPress Admin → Settings → MarzPay**
2. Verify that both **API User** and **API Key** are filled in
3. Make sure there are no extra spaces before or after the credentials

### Step 2: Test API Connection

1. In the MarzPay settings page, click the **"Test API Connection"** button
2. This will test your credentials with the MarzPay API using phone number: **256759983853**
3. Check the result message for specific error details

### Step 3: Enable Debug Logging

1. Add this to your `wp-config.php` file:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

2. Test the shortcode again
3. Check the debug log at `wp-content/debug.log` for detailed API information

### Step 4: Use Debug Shortcode

Add this shortcode to any page to see detailed debugging information:
```
[marzpay_debug]
```

This will show:
- Whether API credentials are configured
- If WP_DEBUG is enabled
- A test button to verify API connectivity

### Step 5: Check Common Issues

#### Phone Number Format
- **Recommended format**: `256759983853` (will be converted to `+256759983853`)
- **Alternative formats**:
  - `0759983853` → converts to `+256759983853`
  - `+256759983853` → used as-is
- **Example**: `[marzpay_button phone="256759983853" amount="1000"]`

#### Amount Validation
- Amount must be a positive number
- Use whole numbers (e.g., 1000 for UGX 1,000)

#### API Endpoint ✅ **CORRECTED**
- **Correct endpoint**: `https://wallet.wearemarz.com/api/v1/collect-money`
- **Previous incorrect endpoint**: `https://wallet.wearemarz.com/api/collect-money` (missing `/v1/`)
- The plugin now uses the correct endpoint with version `/v1/`

### Step 6: Check Error Logs

With WP_DEBUG enabled, you'll see detailed logs including:
- API request details
- HTTP status codes
- Raw API responses
- JSON parsing errors

### Step 7: Verify API Response Format

The MarzPay API should return JSON in this format:
```json
{
  "status": "success",
  "reference": "order_1234567890",
  "message": "Payment request sent successfully"
}
```

If you get a different format, the API may have changed or there's an authentication issue.

### Step 8: Test with Different Values

Try testing with:
1. Different phone numbers
2. Different amounts
3. Check if the issue is specific to certain values

### Step 9: Check Network Connectivity

1. Verify your server can reach `https://wallet.wearemarz.com`
2. Check if there are firewall restrictions
3. Test with a simple cURL request if possible

### Step 10: Contact MarzPay Support

If all else fails:
1. Verify your API credentials with MarzPay
2. Check if there are any API changes or maintenance
3. Confirm your account has the correct permissions

## Quick Test

Use this shortcode to test with your specific phone number:
```
[marzpay_button phone="256759983853" amount="100"]
```

## Debug Information

The plugin now provides much more detailed error messages. When you get an error, it will show:
- HTTP status code
- Raw API response (truncated to avoid URI too long errors)
- JSON parsing errors
- Specific validation failures

## URI Too Large Error (HTTP 414) - FIXED

**Problem**: If you were getting "URI too large" errors, this has been fixed by:
- Truncating long error messages
- Using WordPress transients to store detailed error information
- Limiting URL length in redirects

## API Endpoint Issue - FIXED ✅

**Problem**: The plugin was using the wrong API endpoint:
- **Incorrect**: `https://wallet.wearemarz.com/api/collect-money`
- **Correct**: `https://wallet.wearemarz.com/api/v1/collect-money`

**Solution**: Updated the plugin to use the correct endpoint with the `/v1/` version.

## Still Having Issues?

1. Check the WordPress error logs
2. Use the debug shortcode `[marzpay_debug]`
3. Test the API connection from the admin panel
4. Verify your MarzPay account status

## Support

For additional help:
- Check the WordPress error logs
- Use the debug features added to the plugin
- Contact MarzPay support with your API credentials
