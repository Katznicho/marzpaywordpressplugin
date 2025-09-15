# 🛒 MarzPay WooCommerce Integration

**Complete WooCommerce payment gateway integration for accepting mobile money payments in Uganda.**

## 🎯 Overview

The MarzPay WooCommerce integration allows you to accept mobile money payments (Airtel Money & MTN Mobile Money) directly through your WooCommerce store checkout process. Customers can pay using their mobile money accounts seamlessly.

## ✨ Features

### **Core Features**
- ✅ **WooCommerce Payment Gateway** - Native integration as payment method
- ✅ **Mobile Money Support** - Airtel Money & MTN Mobile Money
- ✅ **Automatic Order Management** - Order status updates based on payment
- ✅ **Webhook Integration** - Real-time payment status updates
- ✅ **Phone Number Validation** - Multiple format support
- ✅ **Admin Dashboard** - Dedicated WooCommerce orders management
- ✅ **Bulk Actions** - Check payment status for multiple orders
- ✅ **Order Meta Boxes** - Payment details in order edit page

### **User Experience**
- ✅ **Seamless Checkout** - Integrated payment method selection
- ✅ **Payment Instructions** - Clear guidance for customers
- ✅ **Status Updates** - Real-time payment status on order pages
- ✅ **Mobile Optimized** - Works perfectly on all devices
- ✅ **Auto-refresh** - Automatic status checking for pending payments

## 🚀 Installation & Setup

### **Prerequisites**
- WordPress 5.0+
- WooCommerce 3.0+
- PHP 7.4+
- MarzPay API account

### **Step 1: Install WooCommerce**
1. Go to **Plugins → Add New**
2. Search for "WooCommerce"
3. Install and activate WooCommerce
4. Complete WooCommerce setup wizard

### **Step 2: Install MarzPay Plugin**
1. Install the MarzPay Collections plugin
2. Activate the plugin
3. Configure your API credentials in **MarzPay → Settings**

### **Step 3: Enable MarzPay Gateway**
1. Go to **WooCommerce → Settings → Payments**
2. Find "MarzPay Mobile Money" in the payment methods list
3. Click **"Set up"** or **"Manage"**
4. Configure the gateway settings

## ⚙️ Configuration

### **Gateway Settings**

#### **Basic Settings**
- **Enable/Disable**: Turn the gateway on/off
- **Title**: Payment method title (default: "Mobile Money (Airtel & MTN)")
- **Description**: Payment method description for customers
- **Test Mode**: Enable for testing with sandbox credentials

#### **API Credentials**
- **API Key**: Your MarzPay API key
- **API Secret**: Your MarzPay API secret
- **Webhook URL**: Copy this URL to your MarzPay webhook settings

#### **Advanced Settings**
- **Phone Number Required**: Require customers to enter phone number
- **Auto Complete Orders**: Automatically complete orders on successful payment

### **Webhook Configuration**
1. Copy the webhook URL from gateway settings
2. Go to your MarzPay dashboard
3. Navigate to **Webhooks** section
4. Add new webhook with the copied URL
5. Enable webhook for payment status updates

## 📱 Customer Experience

### **Checkout Process**
1. Customer adds products to cart
2. Proceeds to checkout
3. Selects "Mobile Money (Airtel & MTN)" as payment method
4. Enters phone number (if required)
5. Completes order
6. Receives mobile money prompt on phone
7. Enters mobile money PIN
8. Payment is processed automatically

### **Payment Instructions**
The plugin automatically shows payment instructions when MarzPay is selected:
- Complete your order below
- You will receive a mobile money prompt on your phone
- Enter your mobile money PIN to confirm payment
- Your order will be processed automatically

### **Supported Networks**
- **Airtel Money** - Airtel Uganda customers
- **MTN Mobile Money** - MTN Uganda customers

## 🛠️ Admin Management

### **WooCommerce Orders**
Access dedicated MarzPay orders management:
1. Go to **MarzPay → WooCommerce Orders**
2. View statistics and recent orders
3. Check payment statuses
4. Perform bulk actions

### **Order Management**
- **Order List**: Custom column showing MarzPay payment status
- **Order Edit**: Meta box with payment details
- **Bulk Actions**: Check status for multiple orders
- **Auto-refresh**: Automatic status checking for pending orders

### **Payment Status Tracking**
- **Successful**: Payment completed successfully
- **Pending**: Payment request sent, awaiting customer action
- **Processing**: Payment being processed
- **Failed**: Payment failed or was cancelled

## 🔧 Advanced Features

### **Phone Number Formats**
The plugin automatically converts phone numbers:
- `256759983853` → `+256759983853`
- `0759983853` → `+256759983853`
- `+256759983853` → `+256759983853`

### **Order Status Mapping**
- **Successful Payment** → Order marked as "Processing" or "Completed"
- **Failed Payment** → Order marked as "Failed"
- **Pending Payment** → Order remains "Pending"

### **Webhook Processing**
- Real-time payment status updates
- Automatic order status changes
- Transaction UUID tracking
- Payment confirmation handling

## 📊 Monitoring & Analytics

### **Dashboard Statistics**
- Total MarzPay orders
- Successful payments count
- Pending payments count
- Failed payments count
- Total amount processed

### **Order Tracking**
- Transaction UUID for each payment
- Payment reference numbers
- Customer phone numbers
- Payment timestamps
- Status change history

## 🔒 Security Features

### **Data Protection**
- Secure API credential storage
- Phone number validation
- Amount limits enforcement
- Nonce verification for AJAX requests
- User permission checks

### **Payment Security**
- UUID-based transaction tracking
- Webhook signature verification
- Order status validation
- Duplicate payment prevention

## 🐛 Troubleshooting

### **Common Issues**

#### **Gateway Not Showing**
- Ensure WooCommerce is active
- Check if MarzPay plugin is activated
- Verify API credentials are configured

#### **Payment Not Processing**
- Check API credentials
- Verify webhook URL is configured
- Test API connection in MarzPay settings

#### **Orders Not Updating**
- Check webhook configuration
- Verify webhook URL is accessible
- Test webhook manually

#### **Phone Number Issues**
- Ensure phone number is in correct format
- Check if phone number is required
- Verify phone number validation

### **Debug Mode**
Enable WordPress debug mode to see detailed logs:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## 📞 Support

### **Getting Help**
- Check the troubleshooting section above
- Review WooCommerce and MarzPay documentation
- Contact MarzPay support for API issues
- Check WordPress error logs for detailed error messages

### **Useful Resources**
- [WooCommerce Documentation](https://woocommerce.com/documentation/)
- [MarzPay API Documentation](https://wearemarz.com/docs)
- [WordPress Debug Guide](https://wordpress.org/support/article/debugging-in-wordpress/)

## 🎉 Success Tips

### **Best Practices**
1. **Test Thoroughly**: Use test mode before going live
2. **Monitor Orders**: Regularly check payment statuses
3. **Customer Communication**: Inform customers about mobile money process
4. **Backup Data**: Regular backups of order data
5. **Update Regularly**: Keep plugins updated

### **Optimization**
- Enable auto-complete for faster order processing
- Use webhooks for real-time updates
- Monitor failed payments and follow up
- Provide clear payment instructions to customers

---

**Ready to accept mobile money payments in your WooCommerce store? Follow the setup guide above and start selling with MarzPay!**
