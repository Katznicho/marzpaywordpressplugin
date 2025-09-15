=== MarzPay Collections ===
Contributors: marzpay
Tags: payments, marzpay, collections, mobile money, uganda, mobile payments, api, shortcode, payment gateway
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Accept mobile money payments in WordPress using the MarzPay Collections API. Perfect for businesses in Uganda accepting mobile payments.

== Description ==

**MarzPay Collections** is a powerful WordPress plugin that integrates with the MarzPay Collections API to accept mobile money payments in Uganda. This plugin provides a simple shortcode system for creating payment buttons and comprehensive admin settings for API configuration.

## 🚀 Key Features

* **Easy Integration**: Simple shortcode system for payment buttons
* **Secure API**: Full integration with MarzPay Collections API
* **Flexible Configuration**: Admin panel for API credentials and settings
* **Phone Number Support**: Multiple phone number format support
* **Amount Validation**: Built-in amount limits (500 - 10,000,000 UGX)
* **UUID Generation**: Automatic secure reference generation
* **Callback Support**: Configurable webhook URLs for payment notifications
* **Debug Tools**: Built-in API testing and debugging features

## 📱 Phone Number Formats Supported

* **`256759983853`** → converts to `+256759983853`
* **`0759983853`** → converts to `+256759983853`
* **`+256759983853`** → used as-is

## 💰 Amount Requirements

* **Minimum**: 500 UGX
* **Maximum**: 10,000,000 UGX
* **Format**: Whole numbers only

## 🎯 Quick Start

1. Install and activate the plugin
2. Go to **Settings → MarzPay** to configure your API credentials
3. Test the API connection
4. Use the shortcode: `[marzpay_button amount="1000" phone="256759983853"]`

## 🔒 Security Features

* Input validation and sanitization
* Amount limits to prevent invalid transactions
* Phone number format validation
* Secure UUID generation
* API authentication with your credentials

== Installation ==

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

## Configuration

1. Go to **WordPress Admin → Settings → MarzPay**
2. Enter your **API Key** and **API Secret** from MarzPay
3. (Optional) Set a custom **Callback URL**
4. Click **"Save Settings"**
5. Test the API connection

== Frequently Asked Questions ==

= What is MarzPay? =

MarzPay is a mobile money payment platform that allows businesses to collect payments from customers via mobile money in Uganda.

= What phone number formats are supported? =

The plugin supports multiple formats:
* `256759983853` (recommended)
* `0759983853`
* `+256759983853`

= What are the amount limits? =

* Minimum: 500 UGX
* Maximum: 10,000,000 UGX
* Whole numbers only

= How do I test the API connection? =

Go to **Settings → MarzPay** and click the **"Test API Connection"** button. Enter a test phone number to verify your credentials work.

= Can I customize the callback URL? =

Yes! Go to **Settings → MarzPay** and enter your custom callback URL. If left empty, the plugin uses the default: `https://yoursite.com/marzpay-callback`

= Is this plugin secure? =

Yes! The plugin includes:
* Input validation and sanitization
* Secure UUID generation
* API authentication
* Error handling without exposing sensitive data

== Screenshots ==

1. Admin settings page with API configuration
2. Payment button shortcode example
3. API connection test interface
4. Debug information display

== Changelog ==

= 1.0.0 =
* Initial release with MarzPay Collections API integration
* Shortcode system for payment buttons
* Admin settings panel for API configuration
* Phone number format support and validation
* Amount validation (500 - 10,000,000 UGX)
* Automatic UUID generation for references
* Configurable callback URL support
* Built-in API testing and debugging tools
* Comprehensive error handling and validation
* Professional documentation and user guides

== Upgrade Notice ==

= 1.0.0 =
Initial release of MarzPay Collections plugin. Perfect for businesses in Uganda accepting mobile money payments.
