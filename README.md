# MarzPay Collections WordPress Plugin

A powerful WordPress plugin that integrates with the MarzPay Collections API to accept mobile money payments in Uganda. This plugin provides a simple shortcode system for creating payment buttons and comprehensive admin settings for API configuration.

## 🚀 Features

- **Easy Integration**: Simple shortcode system for payment buttons
- **Secure API**: Full integration with MarzPay Collections API
- **Flexible Configuration**: Admin panel for API credentials and settings
- **Phone Number Support**: Multiple phone number format support
- **Amount Validation**: Built-in amount limits (500 - 10,000,000 UGX)
- **UUID Generation**: Automatic secure reference generation
- **Callback Support**: Configurable webhook URLs for payment notifications
- **Debug Tools**: Built-in API testing and debugging features

## 📋 Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MarzPay API credentials
- SSL certificate (recommended for production)

## 🔧 Installation

### Method 1: WordPress Admin (Recommended)

1. Download the plugin ZIP file
2. Go to **WordPress Admin → Plugins → Add New**
3. Click **"Upload Plugin"**
4. Choose the ZIP file and click **"Install Now"**
5. Click **"Activate Plugin"**

### Method 2: Manual Installation

1. Extract the plugin files
2. Upload the `marzpay-collections` folder to `/wp-content/plugins/`
3. Go to **WordPress Admin → Plugins**
4. Find "MarzPay Collections" and click **"Activate"**

## ⚙️ Configuration

### Step 1: Get MarzPay API Credentials

1. Sign up for a MarzPay account at [wearemarz.com](https://wearemarz.com)
2. Navigate to your API settings
3. Copy your **API User** and **API Key**

### Step 2: Configure Plugin Settings

1. Go to **WordPress Admin → Settings → MarzPay**
2. Enter your **API User** and **API Key**
3. (Optional) Set a custom **Callback URL**
4. Click **"Save Settings"**

### Step 3: Test API Connection

1. In the MarzPay settings page, enter a test phone number
2. Click **"Test API Connection"**
3. Verify the connection is successful

## 📱 Usage

### Basic Payment Button

```
[marzpay_button amount="1000" phone="256759983853"]
```

### Parameters

| Parameter | Required | Description | Example |
|-----------|----------|-------------|---------|
| `amount` | Yes | Payment amount in UGX (500-10,000,000) | `1000` |
| `phone` | Yes | Phone number (multiple formats supported) | `256759983853` |

### Phone Number Formats

The plugin automatically converts these formats to the required `+256XXXXXXXXX` format:

- **`256759983853`** → converts to `+256759983853`
- **`0759983853`** → converts to `+256759983853`
- **`+256759983853`** → used as-is

### Amount Requirements

- **Minimum**: 500 UGX
- **Maximum**: 10,000,000 UGX
- **Format**: Whole numbers only

## 🎯 Examples

### Simple Payment Button
```
[marzpay_button amount="5000" phone="256759983853"]
```
Creates a payment button for 5,000 UGX to the specified phone number.

### Multiple Payment Buttons
```
[marzpay_button amount="1000" phone="256700000000"]
[marzpay_button amount="2000" phone="256700000001"]
[marzpay_button amount="5000" phone="256700000002"]
```

### With Custom Styling
You can style the payment buttons using CSS:
```css
.marzpay-button {
    background: linear-gradient(45deg, #0073aa, #005177);
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0,115,170,0.3);
}
```

## 🔒 Security Features

- **Input Validation**: All inputs are sanitized and validated
- **Amount Limits**: Prevents invalid transaction amounts
- **Phone Validation**: Ensures proper international format
- **UUID Generation**: Secure, unique reference identifiers
- **API Authentication**: Basic auth with your credentials
- **Error Handling**: Secure error messages without sensitive data

## 🐛 Troubleshooting

### Common Issues

#### 1. "Missing API credentials" Error
**Solution**: Go to **Settings → MarzPay** and enter your API credentials.

#### 2. "Invalid amount" Error
**Solution**: Ensure amount is between 500 and 10,000,000 UGX.

#### 3. "Invalid phone number format" Error
**Solution**: Use formats: `256759983853`, `0759983853`, or `+256759983853`.

#### 4. "API connection failed" Error
**Solution**: 
1. Verify your API credentials
2. Check internet connectivity
3. Test API connection from admin panel
4. Contact MarzPay support

### Debug Mode

Enable WordPress debug mode to see detailed API logs:

1. Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

2. Check logs at `wp-content/debug.log`

### Test API Connection

Use the built-in test feature:
1. Go to **Settings → MarzPay**
2. Enter a test phone number
3. Click **"Test API Connection"**
4. Check the result message

## 📚 API Reference

### MarzPay API Endpoint
```
POST https://wallet.wearemarz.com/api/v1/collect-money
```

### Request Body Format
```json
{
  "amount": 500,
  "phone_number": "+256759983853",
  "reference": "a1b2c3d4-e5f6-4abc-8def-123456789abc",
  "description": "Payment for services",
  "callback_url": "https://yoursite.com/marzpay-callback",
  "country": "UG"
}
```

### Response Format
```json
{
  "status": "success",
  "reference": "a1b2c3d4-e5f6-4abc-8def-123456789abc",
  "message": "Payment request sent successfully"
}
```

## 🔧 Advanced Configuration

### Custom Callback URL

Set a custom webhook URL for payment notifications:
1. Go to **Settings → MarzPay**
2. Enter your callback URL in the "Callback URL" field
3. Save settings

### Debug Shortcode

Use the debug shortcode to troubleshoot issues:
```
[marzpay_debug]
```
*Note: Only visible to administrators*

## 📞 Support

### Documentation
- [MarzPay API Documentation](https://wearemarz.com/docs)
- [WordPress Plugin Development](https://developer.wordpress.org/plugins/)

### Contact
- **MarzPay Support**: [support@wearemarz.com](mailto:support@wearemarz.com)
- **Plugin Issues**: Check the troubleshooting section above

### Community
- [WordPress.org Forums](https://wordpress.org/support/)
- [MarzPay Community](https://wearemarz.com/community)

## 🔄 Changelog

### Version 1.0.0
- Initial release
- MarzPay Collections API integration
- Shortcode system for payment buttons
- Admin settings panel
- Phone number format support
- Amount validation
- UUID generation
- Callback URL configuration
- API testing tools
- Comprehensive error handling

## 📄 License

This plugin is licensed under the GPL v2 or later.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## ⚠️ Disclaimer

This plugin is provided "as is" without warranty of any kind. Always test thoroughly in a development environment before using in production.

---

**Made with ❤️ for the MarzPay community**
