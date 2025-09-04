# 🚀 MarzPay Collections Plugin - Quick Start Guide

**Get up and running with MarzPay Collections in under 5 minutes!**

## ⚡ Quick Setup (5 Minutes)

### **1. Download & Install**
```bash
# Option 1: Download ZIP from GitHub
# Visit: https://github.com/Katznicho/marzpaywordpressplugin
# Click "Code" → "Download ZIP"

# Option 2: Clone with Git
git clone https://github.com/Katznicho/marzpaywordpressplugin.git

# Option 3: WordPress Admin
# Plugins → Add New → Upload Plugin → Choose ZIP
```

### **2. Activate Plugin**
- Go to **WordPress Admin → Plugins**
- Find "MarzPay Collections"
- Click **"Activate"**

### **3. Configure API Credentials**
- Go to **Settings → MarzPay**
- Enter your **API User** and **API Key**
- (Optional) Set custom **Callback URL**
- Click **"Save Settings"**

### **4. Test Connection**
- Click **"Test API Connection"**
- Enter your phone number: `256759983853`
- Verify success message

### **5. Use Shortcode**
```php
[marzpay_button amount="1000" phone="256759983853"]
```

## 🎯 Essential Shortcodes

### **Basic Payment Button**
```php
[marzpay_button amount="1000" phone="256759983853"]
```

### **Multiple Payment Options**
```php
<h3>Choose Amount:</h3>
<p>Small: [marzpay_button amount="1000" phone="256759983853"]</p>
<p>Medium: [marzpay_button amount="5000" phone="256759983853"]</p>
<p>Large: [marzpay_button amount="10000" phone="256759983853"]</p>
```

## 📱 Phone Number Formats

| Input | Converts To | Notes |
|-------|-------------|-------|
| `256759983853` | `+256759983853` | **Recommended** |
| `0759983853` | `+256759983853` | Auto-prefixed |
| `+256759983853` | `+256759983853` | Used as-is |

## 💰 Amount Requirements

- **Minimum**: 500 UGX
- **Maximum**: 10,000,000 UGX
- **Format**: Whole numbers only
- **Examples**: 1000, 5000, 10000, 50000

## ⚙️ Common Settings

### **API Credentials**
- **API User**: Your MarzPay username
- **API Key**: Your MarzPay API key
- **Callback URL**: Webhook notifications (optional)

### **Default Callback**
If no custom URL set, uses:
```
https://yoursite.com/marzpay-callback
```

## 🔧 Quick Troubleshooting

### **"Invalid API response"**
- Check API credentials
- Test API connection
- Verify internet connectivity

### **"URI too large"**
- Use "View Detailed Error Information"
- Check error logs
- Enable WordPress debugging

### **"422 Validation Error"**
- Verify amount (500-10,000,000 UGX)
- Check phone number format
- Ensure reference is valid UUID

### **"Minimum amount 500 UGX"**
- Increase amount to 500+ UGX
- Use whole numbers only

## 📚 Next Steps

1. **Read Full Documentation**: [README.md](README.md)
2. **Troubleshooting Guide**: [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
3. **Report Issues**: [GitHub Issues](https://github.com/Katznicho/marzpaywordpressplugin/issues)
4. **Get Support**: Contact MarzPay support

## 🚀 Advanced Features

- **Debug Shortcode**: `[marzpay_debug]`
- **Custom Callback URLs**
- **Phone Number Validation**
- **Amount Limits**
- **Secure UUID Generation**

---

**Need help? Check the full documentation or contact support!**
