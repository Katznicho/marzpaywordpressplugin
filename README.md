# 🚀 MarzPay Collections WordPress Plugin

**Accept mobile money payments in WordPress using the MarzPay Collections API. Perfect for businesses in Uganda accepting mobile payments.**

> **📖 For WordPress Developers:** Complete shortcode documentation is available in the WordPress admin under **MarzPay → Documentation** after plugin activation.

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [API Reference](#api-reference)
- [Security](#security)
- [Troubleshooting](#troubleshooting)
- [Advanced Configuration](#advanced-configuration)
- [Changelog](#changelog)
- [License](#license)
- [Contributing](#contributing)

## 🌟 Overview

The **MarzPay Collections WordPress Plugin** provides seamless integration between WordPress websites and the MarzPay Collections API. This plugin enables businesses in Uganda to accept mobile money payments directly through their WordPress site using simple shortcodes.

### **What is MarzPay?**

MarzPay is a mobile money payment platform that allows businesses to collect payments from customers via mobile money in Uganda. The platform supports multiple mobile money providers and offers a secure, reliable payment collection service.

### **Plugin Benefits**

- **Easy Integration**: Simple shortcode system for payment buttons
- **Secure Payments**: Professional-grade security and validation
- **Flexible Configuration**: Customizable settings and callback URLs
- **Mobile Optimized**: Works perfectly on all devices
- **Professional Support**: Built-in testing and debugging tools

## 🚀 Features

### **Core Features**
- ✅ **Payment Button Shortcodes** - Easy to use `[marzpay_button]`
- ✅ **Admin Settings Panel** - Professional configuration interface
- ✅ **API Integration** - Full MarzPay Collections API support
- ✅ **Phone Number Support** - Multiple format validation
- ✅ **Amount Validation** - Built-in limits (500 - 10,000,000 UGX)
- ✅ **UUID Generation** - Secure reference creation
- ✅ **Callback Support** - Configurable webhook URLs

### **Security Features**
- ✅ **Input Validation** - All inputs sanitized and validated
- ✅ **Nonce Verification** - CSRF protection for forms
- ✅ **Capability Checks** - Admin-only access
- ✅ **Error Handling** - Secure error messages
- ✅ **API Authentication** - Basic auth with credentials

### **User Experience**
- ✅ **Professional Interface** - Clean, organized admin panel
- ✅ **API Testing Tools** - Built-in connection testing
- ✅ **Debug Shortcode** - Troubleshooting for admins
- ✅ **Error Notifications** - Clear success/failure messages
- ✅ **Helpful Descriptions** - Guidance for all settings

## 📋 Requirements

### **WordPress Requirements**
- **WordPress Version**: 5.0 or higher
- **PHP Version**: 7.4 or higher
- **MySQL Version**: 5.6 or higher

### **Server Requirements**
- **HTTPS Support**: Required for secure API communication
- **cURL Extension**: For API requests
- **JSON Extension**: For data processing
- **Memory Limit**: Minimum 64MB recommended

### **MarzPay Requirements**
- **API Account**: Active MarzPay Collections API account
- **API Credentials**: Valid API User and API Key
- **Business Verification**: Verified business account with MarzPay

## 🛠️ Installation

### **Method 1: WordPress Admin (Recommended)**

1. **Download Plugin**
   - Go to [GitHub Repository](https://github.com/Katznicho/marzpaywordpressplugin)
   - Click "Code" → "Download ZIP"
   - Or use direct link: [Download Latest Release](https://github.com/Katznicho/marzpaywordpressplugin/releases)

2. **Install in WordPress**
   - Go to **WordPress Admin → Plugins → Add New**
   - Click **"Upload Plugin"**
   - Choose the ZIP file and click **"Install Now"**
   - Click **"Activate Plugin"**

### **Method 2: Manual Installation**

1. **Extract Files**
   - Extract the downloaded ZIP file
   - Upload the `marzpay-collections` folder to `/wp-content/plugins/`

2. **Activate Plugin**
   - Go to **WordPress Admin → Plugins**
   - Find "MarzPay Collections" and click **"Activate"**

### **Method 3: Git Clone**

```bash
cd /wp-content/plugins/
git clone https://github.com/Katznicho/marzpaywordpressplugin.git marzpay-collections
```

## ⚙️ Configuration

### **Step 1: Access Settings**
1. Go to **WordPress Admin → Settings → MarzPay**
2. You'll see the configuration panel

### **Step 2: Enter API Credentials**
- **API User**: Your MarzPay API username
- **API Key**: Your MarzPay API key
- **Callback URL**: (Optional) Custom webhook URL

### **Step 3: Test Connection**
1. Click **"Test API Connection"**
2. Enter a test phone number
3. Verify the connection works

### **Step 4: Save Settings**
- Click **"Save Settings"** to store your configuration

## 📱 Usage

### **Basic Payment Button**

```php
[marzpay_button amount="1000" phone="256759983853"]
```

### **Shortcode Parameters**

| Parameter | Required | Description | Example |
|-----------|----------|-------------|---------|
| `amount` | Yes | Payment amount in UGX (500-10,000,000) | `1000` |
| `phone` | Yes | Customer phone number | `256759983853` |

### **Phone Number Formats**

The plugin automatically converts phone numbers to the required format:

| Input Format | Converts To | Description |
|--------------|-------------|-------------|
| `256759983853` | `+256759983853` | **Recommended** - Full country code |
| `0759983853` | `+256759983853` | Local format - automatically prefixed |
| `+256759983853` | `+256759983853` | International format - used as-is |

### **Amount Requirements**

- **Minimum**: 500 UGX
- **Maximum**: 10,000,000 UGX
- **Format**: Whole numbers only
- **Currency**: Ugandan Shillings (UGX)

### **Usage Examples**

#### **Simple Payment Button**
```php
[marzpay_button amount="5000" phone="256759983853"]
```

#### **Multiple Payment Options**
```php
<h3>Choose Payment Amount:</h3>
<p>Small Donation: [marzpay_button amount="1000" phone="256759983853"]</p>
<p>Medium Donation: [marzpay_button amount="5000" phone="256759983853"]</p>
<p>Large Donation: [marzpay_button amount="10000" phone="256759983853"]</p>
```

#### **Dynamic Amounts (PHP)**
```php
<?php
$amount = 2500; // Get from form or calculation
$phone = "256759983853"; // Get from user input
echo do_shortcode("[marzpay_button amount='{$amount}' phone='{$phone}']");
?>
```

## 🔌 API Reference

### **MarzPay Collections API Endpoint**

```
POST https://wallet.wearemarz.com/api/v1/collect-money
```

### **Authentication**

**Basic Authentication** using your API credentials:
```
Authorization: Basic {base64_encode(API_USER:API_KEY)}
```

### **Request Body Format**

```json
{
  "amount": 500,
  "phone_number": "+256759983853",
  "reference": "uuid-v4-format",
  "description": "Payment description",
  "callback_url": "https://yoursite.com/marzpay-callback",
  "country": "UG"
}
```

### **Request Parameters**

| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| `amount` | Integer | Yes | Amount in UGX (500-10,000,000) |
| `phone_number` | String | Yes | Phone in +256XXXXXXXXX format |
| `reference` | String | Yes | Unique UUID v4 reference |
| `description` | String | Yes | Payment description |
| `callback_url` | String | Yes | Webhook notification URL |
| `country` | String | Yes | Country code (UG for Uganda) |

### **Response Format**

#### **Success Response**
```json
{
  "status": "success",
  "message": "Payment request sent successfully",
  "data": {
    "reference": "uuid-here",
    "amount": 500,
    "phone_number": "+256759983853"
  }
}
```

#### **Error Response**
```json
{
  "status": "error",
  "message": "Error description",
  "error_code": "ERROR_CODE"
}
```

### **Error Codes**

| Code | Description | Solution |
|------|-------------|----------|
| `VALIDATION_ERROR` | Input validation failed | Check amount, phone, and reference format |
| `INVALID_CREDENTIALS` | API credentials invalid | Verify API User and Key |
| `MINIMUM_AMOUNT` | Amount below 500 UGX | Increase amount to minimum |
| `MAXIMUM_AMOUNT` | Amount above 10,000,000 UGX | Decrease amount to maximum |
| `INVALID_PHONE` | Phone number format invalid | Use supported phone formats |

## 🔒 Security

### **Input Validation**

- **Amount**: Numeric validation with min/max limits
- **Phone**: Regex validation for proper format
- **Reference**: UUID v4 format validation
- **Sanitization**: All inputs cleaned and sanitized

### **Form Security**

- **Nonce Verification**: CSRF protection for all forms
- **Capability Checks**: Admin-only access to settings
- **Input Sanitization**: XSS protection
- **Error Handling**: Secure error messages

### **API Security**

- **Basic Authentication**: Secure credential transmission
- **HTTPS Required**: All API calls use secure connections
- **Input Validation**: Server-side validation before API calls
- **Error Logging**: Secure logging without sensitive data exposure

## 🐛 Troubleshooting

### **Common Issues**

#### **1. "Invalid API response" Error**
- **Cause**: API connection or authentication issue
- **Solution**: Check API credentials and test connection

#### **2. "URI too large" Error**
- **Cause**: Error message too long for URL
- **Solution**: Use the "View Detailed Error Information" section

#### **3. "422 Validation Error"**
- **Cause**: Invalid request format or data
- **Solution**: Check amount limits and phone number format

#### **4. "Reference must be a valid UUID"**
- **Cause**: Reference format issue
- **Solution**: Plugin automatically generates valid UUIDs

#### **5. "Minimum amount for collection is 500 UGX"**
- **Cause**: Amount below MarzPay minimum
- **Solution**: Use amounts 500 UGX or higher

### **Debug Tools**

#### **Debug Shortcode**
```php
[marzpay_debug]
```
Shows API credentials status and WP_DEBUG information.

#### **API Test Button**
- Go to **Settings → MarzPay**
- Click **"Test API Connection"**
- Enter test phone number
- View detailed results

#### **Error Logs**
Enable WordPress debugging:
```php
// In wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

### **Getting Help**

1. **Check Error Messages**: Use the detailed error information
2. **Test API Connection**: Verify credentials work
3. **Review Settings**: Ensure all fields are filled
4. **Check Logs**: Enable WordPress debugging
5. **Contact Support**: Reach out to MarzPay support

## ⚙️ Advanced Configuration

### **Custom Callback URLs**

Set custom webhook URLs for payment notifications:

```php
// In Settings → MarzPay → Callback URL
https://yoursite.com/custom-webhook-endpoint
```

### **Default Callback URL**

If no custom URL is set, the plugin uses:
```
https://yoursite.com/marzpay-callback
```

### **Phone Number Formatting**

The plugin automatically handles phone number conversion:

```php
// Input: 0759983853
// Output: +256759983853

// Input: 256759983853  
// Output: +256759983853

// Input: +256759983853
// Output: +256759983853 (unchanged)
```

### **Amount Validation**

Built-in validation ensures compliance with MarzPay requirements:

```php
// Valid amounts: 500, 1000, 5000, 10000, 50000, 100000, 1000000
// Invalid amounts: 100, 250, 15000000, 0, -100
```

## 📝 Changelog

### **Version 1.0.0** - *Current Release*
- 🎉 **Initial Release**
- ✅ MarzPay Collections API integration
- ✅ Payment button shortcode system
- ✅ Admin settings panel with API testing
- ✅ Phone number format support and validation
- ✅ Amount validation (500-10,000,000 UGX)
- ✅ Automatic UUID generation for references
- ✅ Configurable callback URL support
- ✅ Built-in API testing and debugging tools
- ✅ Comprehensive error handling and validation
- ✅ Professional documentation and user guides
- ✅ Security features (nonce verification, input validation)
- ✅ WordPress.org ready with professional readme.txt

### **Upcoming Features**
- 📱 Mobile app integration
- 🔄 Payment history and logs
- 📊 Analytics and reporting
- 🚀 Bulk payment operations
- 🔐 Enhanced security features

## 📄 License

This plugin is licensed under the **GPL v2 or later**.

```
Copyright (C) 2025 MarzPay

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
```

## 🤝 Contributing

We welcome contributions to improve the MarzPay Collections WordPress Plugin!

### **How to Contribute**

1. **Fork the Repository**
   - Visit [GitHub Repository](https://github.com/Katznicho/marzpaywordpressplugin)
   - Click "Fork" to create your copy

2. **Make Changes**
   - Create a feature branch
   - Make your improvements
   - Test thoroughly

3. **Submit Pull Request**
   - Push your changes
   - Create a pull request
   - Describe your improvements

### **Development Setup**

```bash
# Clone the repository
git clone https://github.com/Katznicho/marzpaywordpressplugin.git

# Navigate to plugin directory
cd marzpaywordpressplugin

# Make your changes
# Test thoroughly
# Submit pull request
```

### **Code Standards**

- Follow WordPress coding standards
- Use proper PHP documentation
- Include security best practices
- Test all functionality thoroughly

## 📞 Support

### **Plugin Support**
- **GitHub Issues**: [Report Issues](https://github.com/Katznicho/marzpaywordpressplugin/issues)
- **Documentation**: [Full Documentation](https://github.com/Katznicho/marzpaywordpressplugin/blob/main/README.md)
- **Quick Start**: [Quick Start Guide](https://github.com/Katznicho/marzpaywordpressplugin/blob/main/QUICK_START.md)

### **MarzPay Support**
- **API Documentation**: [MarzPay API Docs](https://wallet.wearemarz.com/api/docs)
- **Business Support**: Contact MarzPay business support
- **Technical Support**: API technical assistance

### **WordPress Support**
- **WordPress.org**: [Plugin Directory](https://wordpress.org/plugins/)
- **WordPress Codex**: [Developer Documentation](https://codex.wordpress.org/)
- **WordPress Support**: [Community Support](https://wordpress.org/support/)

## 🌟 Thank You

Thank you for choosing the **MarzPay Collections WordPress Plugin**! We're committed to providing the best mobile money payment experience for WordPress users in Uganda.

---

**Made with ❤️ by MarzPay Team**

*Empowering businesses with seamless mobile payments*
