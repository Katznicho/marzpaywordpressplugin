# 🧪 MarzPay WooCommerce Testing Guide

**Complete guide to test the MarzPay WooCommerce integration in your WordPress site.**

## 🚀 Quick Test Setup

### **Step 1: Prerequisites Check**
1. **WordPress** - Version 5.0+ ✅
2. **WooCommerce** - Version 3.0+ ✅
3. **MarzPay Plugin** - Activated ✅
4. **MarzPay API Account** - With test credentials ✅

### **Step 2: Install WooCommerce (if not already installed)**
1. Go to **Plugins → Add New**
2. Search for "WooCommerce"
3. Install and activate
4. Complete the setup wizard (you can skip payment setup for now)

### **Step 3: Configure MarzPay Plugin**
1. Go to **MarzPay → Settings**
2. Enter your **API Key** and **API Secret**
3. Click **"Test API Connection"** to verify
4. Save settings

### **Step 4: Enable MarzPay Gateway**
1. Go to **WooCommerce → Settings → Payments**
2. Find **"MarzPay Mobile Money"** in the list
3. Click **"Set up"** or **"Manage"**
4. Configure the gateway:
   - ✅ **Enable/Disable**: Enable
   - **Title**: "Mobile Money (Airtel & MTN)"
   - **Description**: "Pay with your mobile money account"
   - ✅ **Test Mode**: Enable (for testing)
   - **API Key**: Your MarzPay API key
   - **API Secret**: Your MarzPay API secret
   - **Phone Required**: Enable (recommended)
   - **Auto Complete**: Disable (for testing)
5. Click **"Save changes"**

## 🛒 Test Scenarios

### **Test 1: Basic Checkout Flow**

#### **Setup Test Products**
1. Go to **Products → Add New**
2. Create a simple test product:
   - **Name**: "Test Product"
   - **Price**: 1000 UGX
   - **Description**: "Test product for MarzPay integration"
3. Publish the product

#### **Test Checkout Process**
1. Go to your shop page
2. Add the test product to cart
3. Proceed to checkout
4. Fill in billing details:
   - **First Name**: Test
   - **Last Name**: Customer
   - **Email**: test@example.com
   - **Phone**: 256759983853 (or your test number)
5. Select **"Mobile Money (Airtel & MTN)"** as payment method
6. Enter phone number: `256759983853` (test number)
7. Click **"Place order"**

#### **Expected Results**
- ✅ Order created successfully
- ✅ Order status: "Pending payment"
- ✅ MarzPay payment request sent
- ✅ Customer receives mobile money prompt (if using real number)

### **Test 2: Admin Order Management**

#### **Check Order Details**
1. Go to **WooCommerce → Orders**
2. Find your test order
3. Click to edit the order
4. Check the **"MarzPay Payment Details"** meta box
5. Verify transaction details are stored

#### **Test Status Checking**
1. In the order edit page, click **"Check Payment Status"**
2. Verify the status updates correctly
3. Check the order notes for MarzPay updates

### **Test 3: MarzPay Admin Interface**

#### **Access MarzPay Orders**
1. Go to **MarzPay → WooCommerce Orders**
2. Verify your test order appears
3. Check the statistics dashboard
4. Test the **"Check Status"** button

#### **Test Bulk Actions**
1. Go to **WooCommerce → Orders**
2. Select multiple MarzPay orders
3. Use **"Check MarzPay Status"** bulk action
4. Verify status updates

### **Test 4: Webhook Testing**

#### **Setup Webhook**
1. Copy the webhook URL from gateway settings
2. Go to your MarzPay dashboard
3. Add webhook with the copied URL
4. Enable for payment status updates

#### **Test Webhook**
1. Complete a test payment
2. Check if order status updates automatically
3. Verify webhook is receiving data

## 🔧 Test Data

### **Test Phone Numbers**
Use these test numbers for different scenarios:

```
Test Number 1: 256759983853 (Recommended)
Test Number 2: 0759983853 (Local format)
Test Number 3: +256759983853 (International format)
```

### **Test Amounts**
```
Minimum: 500 UGX
Small: 1000 UGX
Medium: 5000 UGX
Large: 10000 UGX
Maximum: 10000000 UGX
```

### **Test Scenarios**
1. **Successful Payment**: Use valid test credentials
2. **Failed Payment**: Use invalid phone number
3. **Pending Payment**: Start payment but don't complete
4. **Cancelled Payment**: Cancel during mobile money prompt

## 🐛 Troubleshooting Tests

### **Test 1: Gateway Not Showing**
**Problem**: MarzPay not appearing in payment methods
**Solutions**:
- Check if WooCommerce is active
- Verify MarzPay plugin is activated
- Check if API credentials are configured
- Ensure gateway is enabled in WooCommerce settings

### **Test 2: Payment Not Processing**
**Problem**: Payment request fails
**Solutions**:
- Test API connection in MarzPay settings
- Verify API credentials are correct
- Check phone number format
- Review WordPress error logs

### **Test 3: Orders Not Updating**
**Problem**: Order status not changing
**Solutions**:
- Check webhook configuration
- Verify webhook URL is accessible
- Test webhook manually
- Check order notes for errors

### **Test 4: Phone Number Issues**
**Problem**: Phone number validation fails
**Solutions**:
- Use correct format: 256759983853
- Check if phone field is required
- Verify phone number validation logic

## 📊 Test Checklist

### **Pre-Testing Setup**
- [ ] WordPress 5.0+ installed
- [ ] WooCommerce 3.0+ installed and activated
- [ ] MarzPay plugin installed and activated
- [ ] API credentials configured
- [ ] Test products created
- [ ] Webhook URL configured

### **Basic Functionality Tests**
- [ ] Gateway appears in payment methods
- [ ] Checkout process works
- [ ] Phone number field displays
- [ ] Payment request sent successfully
- [ ] Order created with correct status
- [ ] Transaction details stored

### **Admin Interface Tests**
- [ ] MarzPay orders page accessible
- [ ] Order meta box displays correctly
- [ ] Status checking works
- [ ] Bulk actions function
- [ ] Statistics display correctly

### **Webhook Tests**
- [ ] Webhook URL accessible
- [ ] Payment status updates automatically
- [ ] Order status changes correctly
- [ ] Order notes updated

### **Error Handling Tests**
- [ ] Invalid phone numbers handled
- [ ] API errors displayed properly
- [ ] Failed payments marked correctly
- [ ] Error messages user-friendly

## 🎯 Success Criteria

### **✅ Integration Working If:**
1. MarzPay appears as payment method in checkout
2. Customers can complete orders using mobile money
3. Orders are created with correct status
4. Payment status updates automatically
5. Admin can manage MarzPay orders
6. Webhooks receive and process updates

### **❌ Issues to Fix If:**
1. Gateway doesn't appear in payment methods
2. Payment requests fail consistently
3. Orders don't update after payment
4. Admin interface shows errors
5. Webhooks don't receive data

## 🚀 Next Steps After Testing

### **If Tests Pass:**
1. **Go Live**: Disable test mode
2. **Configure Production**: Use live API credentials
3. **Set Up Monitoring**: Monitor order statuses
4. **Train Staff**: Show team how to manage orders
5. **Customer Communication**: Inform customers about mobile money option

### **If Tests Fail:**
1. **Check Logs**: Review WordPress and server logs
2. **Debug Mode**: Enable WordPress debug mode
3. **API Testing**: Test MarzPay API directly
4. **Contact Support**: Reach out to MarzPay support
5. **Review Configuration**: Double-check all settings

## 📞 Getting Help

### **Debug Information to Collect:**
- WordPress version
- WooCommerce version
- MarzPay plugin version
- PHP version
- Error messages
- API response logs
- Webhook logs

### **Support Resources:**
- [MarzPay API Documentation](https://wearemarz.com/docs)
- [WooCommerce Documentation](https://woocommerce.com/documentation/)
- [WordPress Debug Guide](https://wordpress.org/support/article/debugging-in-wordpress/)

---

**Ready to test? Follow the steps above and let us know if you encounter any issues!**
