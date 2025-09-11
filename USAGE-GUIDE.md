# MarzPay WordPress Plugin - Usage Guide

## 🚀 Quick Start

### 1. Installation
1. Upload the plugin to your WordPress site
2. Activate the plugin
3. Go to **MarzPay → Settings** in your WordPress admin
4. Enter your MarzPay API credentials

### 2. Basic Usage

#### Collect Money from Customers
Add this shortcode to any page or post:
```
[marzpay_collect]
```

#### Send Money to Customers  
Add this shortcode to any page or post:
```
[marzpay_send]
```

#### Show Account Balance
Display your current balance:
```
[marzpay_balance]
```

#### Show Recent Transactions
Display recent transactions:
```
[marzpay_transactions]
```

## 📝 Shortcode Options

### Collect Money Shortcode
```
[marzpay_collect amount="1000" phone="+256759983853" description="Payment for services"]
```

**Attributes:**
- `amount` - Default amount (optional)
- `phone` - Default phone number (optional)
- `description` - Default description (optional)
- `button_text` - Custom button text (default: "Request Payment")

### Send Money Shortcode
```
[marzpay_send amount="500" phone="+256700000000" description="Refund payment"]
```

**Attributes:**
- `amount` - Default amount (optional)
- `phone` - Default phone number (optional)
- `description` - Default description (optional)
- `button_text` - Custom button text (default: "Send Money")

### Balance Shortcode
```
[marzpay_balance show_currency="true"]
```

**Attributes:**
- `show_currency` - Show currency symbol (true/false)

### Transactions Shortcode
```
[marzpay_transactions limit="10"]
```

**Attributes:**
- `limit` - Number of transactions to show (default: 10)

## 🎨 Styling

The plugin includes basic CSS styling. You can customize the appearance by adding CSS to your theme:

```css
/* Customize form styling */
.marzpay-collect-form {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
}

.marzpay-button {
    background: #your-color;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 4px;
}
```

## 📱 Mobile Money Support

**Supported Providers:**
- Airtel Uganda
- MTN Uganda

**Phone Number Format:**
- Use international format: `+256XXXXXXXXX`
- Example: `+256759983853`

## 💰 Amount Limits

- **Minimum:** 500 UGX
- **Maximum:** 10,000,000 UGX
- **Currency:** Uganda Shillings (UGX) only

## 🔧 Admin Features

### Dashboard
- View account information
- Check current balance
- See transaction statistics
- View recent transactions

### Settings
- Configure API credentials
- Set default currency and country
- Configure success/failure pages

### Transactions
- View all transactions
- Filter by status, type, date
- View transaction details
- Export transaction data

## 🚨 Troubleshooting

### Common Issues

**1. "API credentials not configured"**
- Go to MarzPay → Settings
- Enter your MarzPay API User and API Key

**2. "Invalid phone number format"**
- Use international format: `+256XXXXXXXXX`
- Example: `+256759983853`

**3. "Amount must be between 500 and 10,000,000"**
- Check your amount is within the limits
- Amount should be in UGX (no decimals)

**4. Forms not showing**
- Make sure the plugin is activated
- Check if you have the correct shortcode
- Verify API credentials are configured

### Getting Help

1. Check the WordPress admin → MarzPay → Dashboard for account status
2. Test API connection in MarzPay → Settings
3. Check WordPress error logs for detailed error messages
4. Contact MarzPay support for API-related issues

## 📞 Support

- **Plugin Support:** Check WordPress admin → MarzPay → Dashboard
- **API Support:** Contact MarzPay support team
- **Documentation:** Visit MarzPay documentation website

## 🔄 Updates

Keep your plugin updated for the latest features and security improvements:
1. Go to WordPress admin → Plugins
2. Check for MarzPay plugin updates
3. Update when available

---

**Need more help?** Check the plugin documentation or contact support.
