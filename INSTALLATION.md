# MarzPay WordPress Plugin - Installation Guide

## 📦 Installation from Zip File

### Step 1: Download the Plugin
- Download `marzpay-collections.zip` from the repository
- The zip file is ready for WordPress installation

### Step 2: Install in WordPress
1. **Login to WordPress Admin**
   - Go to your website: `http://yoursite.com/wp-admin`
   - Login with your admin credentials

2. **Go to Plugins**
   - Click on **Plugins** in the left sidebar
   - Click **Add New**

3. **Upload Plugin**
   - Click **Upload Plugin** button
   - Choose the `marzpay-collections.zip` file
   - Click **Install Now**

4. **Activate Plugin**
   - After installation, click **Activate Plugin**

### Step 3: Configure API Settings
1. **Go to MarzPay Settings**
   - In WordPress admin, click **MarzPay → Settings**

2. **Enter API Credentials**
   - **API User**: Your MarzPay API username
   - **API Key**: Your MarzPay API key
   - Click **Save Settings**

3. **Test Connection**
   - Click **Test API Connection** to verify setup
   - You should see "API connection successful!"

### Step 4: Start Using Shortcodes
1. **View Documentation**
   - Go to **MarzPay → Documentation**
   - Copy the shortcodes you need

2. **Add to Pages/Posts**
   - Edit any page or post
   - Add shortcode: `[marzpay_collect]`
   - Save and view your page

## 🚀 Quick Start Shortcodes

### Collect Money from Customers
```
[marzpay_collect]
```

### Send Money to Customers
```
[marzpay_send]
```

### Show Account Balance
```
[marzpay_balance]
```

### Show Recent Transactions
```
[marzpay_transactions]
```

## 📱 Supported Features

- ✅ **Mobile Money**: Airtel Uganda, MTN Uganda
- ✅ **Phone Format**: +256XXXXXXXXX (international format)
- ✅ **Amount Range**: 500 - 10,000,000 UGX
- ✅ **Real-time**: Live transactions and balance updates
- ✅ **Admin Dashboard**: Complete transaction management

## 🆘 Need Help?

1. **Check Documentation**: MarzPay → Documentation in WordPress admin
2. **Test API**: MarzPay → Settings → Test API Connection
3. **View Logs**: Check WordPress error logs for detailed messages
4. **Contact Support**: MarzPay support team for API issues

## ✅ Installation Complete!

Your MarzPay WordPress plugin is now ready to accept mobile money payments!

---

**Plugin Version**: 1.0.0  
**WordPress Compatibility**: 5.0+  
**PHP Requirements**: 7.4+  
**Last Updated**: September 2025
