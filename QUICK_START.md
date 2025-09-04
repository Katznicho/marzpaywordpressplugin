# 🚀 MarzPay Collections Plugin - Quick Start Guide

Get your MarzPay payment system up and running in 5 minutes!

## ⚡ Quick Setup (5 Minutes)

### 1. Install & Activate Plugin
- Upload plugin to WordPress
- Activate from Plugins menu
- That's it! ✅

### 2. Configure API Credentials
- Go to **Settings → MarzPay**
- Enter your MarzPay API User & Key
- Save settings

### 3. Test Connection
- Click **"Test API Connection"**
- Enter your phone number
- Verify success message

### 4. Use Payment Button
```
[marzpay_button amount="1000" phone="256759983853"]
```

## 🎯 Essential Shortcodes

### Basic Payment Button
```
[marzpay_button amount="1000" phone="256759983853"]
```

### Multiple Amounts
```
[marzpay_button amount="500" phone="256759983853"]
[marzpay_button amount="1000" phone="256759983853"]
[marzpay_button amount="5000" phone="256759983853"]
```

## 📱 Phone Number Formats

| Input | Converts To | Status |
|-------|-------------|---------|
| `256759983853` | `+256759983853` | ✅ Recommended |
| `0759983853` | `+256759983853` | ✅ Supported |
| `+256759983853` | `+256759983853` | ✅ Direct |

## 💰 Amount Limits

- **Minimum**: 500 UGX
- **Maximum**: 10,000,000 UGX
- **Format**: Whole numbers only

## 🔧 Common Settings

### API Credentials
- **API User**: Your MarzPay username
- **API Key**: Your MarzPay API key
- **Callback URL**: (Optional) Custom webhook URL

### Default Callback
If no custom callback is set, the plugin uses:
```
https://yoursite.com/marzpay-callback
```

## 🐛 Quick Troubleshooting

| Error | Quick Fix |
|-------|-----------|
| "Missing API credentials" | Enter credentials in Settings → MarzPay |
| "Invalid amount" | Use amounts between 500-10,000,000 UGX |
| "Invalid phone format" | Use: 256759983853, 0759983853, or +256759983853 |
| "API connection failed" | Test connection from admin panel |

## 🎨 Customization

### CSS Styling
```css
/* Custom button styles */
.marzpay-button {
    background: linear-gradient(45deg, #0073aa, #005177);
    border-radius: 25px;
    box-shadow: 0 4px 15px rgba(0,115,170,0.3);
}
```

### Debug Information
```
[marzpay_debug]
```
*Admin only - shows API status and configuration*

## 📞 Need Help?

1. **Check this guide** - covers 90% of issues
2. **Test API connection** - from admin panel
3. **Enable debug mode** - add to wp-config.php:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
4. **Contact support** - if all else fails

## 🎉 You're Ready!

Your MarzPay payment system is now active. Users can make payments using the shortcode buttons you create!

---

**Need more details?** Check the full [README.md](README.md) for comprehensive documentation.
