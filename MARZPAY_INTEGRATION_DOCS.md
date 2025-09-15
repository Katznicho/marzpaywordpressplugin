# 🔌 MarzPay Integration Libraries

**Official libraries and plugins for integrating MarzPay Collections API into your applications and platforms.**

## 🎯 Overview

MarzPay provides official integration libraries for popular programming languages and platforms, making it easy to accept mobile money payments in Uganda. Choose the library that best fits your technology stack.

## 🚀 Available Libraries

### 🟦 **WordPress Plugin**
**Accept mobile money payments directly in WordPress websites**

- **Status**: ✅ Production Ready
- **Version**: 1.0.0
- **WordPress**: 5.0+
- **PHP**: 7.4+

**Features:**
- Payment button shortcodes
- Admin settings panel
- API testing tools
- Phone number validation
- Amount validation (500-10,000,000 UGX)
- Automatic UUID generation
- Configurable callback URLs

**Quick Start:**
```php
[marzpay_button amount="1000" phone="256759983853"]
```

**Documentation**: [WordPress Plugin Guide](#wordpress-plugin)

---

### 🟨 **JavaScript/Node.js Library**
**Integrate MarzPay into web applications and Node.js backends**

- **Status**: 🚧 Coming Soon
- **Version**: 0.1.0 (Beta)
- **Node.js**: 16+
- **Browser**: ES6+

**Features:**
- Promise-based API
- TypeScript support
- Browser and Node.js compatibility
- Automatic retry logic
- Request/response validation

**Quick Start:**
```javascript
import { MarzPay } from '@marzpay/collections-js';

const marzpay = new MarzPay({
  apiUser: 'your_username',
  apiKey: 'your_api_key'
});

const payment = await marzpay.collectMoney({
  amount: 1000,
  phoneNumber: '256759983853',
  description: 'Payment for services'
});
```

**Documentation**: [JavaScript Library Guide](#javascript-library)

---

### 🐍 **Python Library**
**Python integration for web applications and automation scripts**

- **Status**: 🚧 Coming Soon
- **Version**: 0.1.0 (Beta)
- **Python**: 3.8+
- **Dependencies**: requests, pydantic

**Features:**
- Async/await support
- Pydantic validation
- Comprehensive error handling
- Django/Flask integration examples
- CLI tool for testing

**Quick Start:**
```python
from marzpay import MarzPay

marzpay = MarzPay(
    api_user="your_username",
    api_key="your_api_key"
)

payment = marzpay.collect_money(
    amount=1000,
    phone_number="256759983853",
    description="Payment for services"
)
```

**Documentation**: [Python Library Guide](#python-library)

---

### 🟥 **PHP Library**
**PHP integration for custom applications and frameworks**

- **Status**: 🚧 Coming Soon
- **Version**: 0.1.0 (Beta)
- **PHP**: 8.0+
- **Composer**: Available

**Features:**
- Composer package
- Laravel integration
- Symfony integration
- PSR standards compliance
- Comprehensive validation

**Quick Start:**
```php
use MarzPay\MarzPay;

$marzpay = new MarzPay([
    'api_user' => 'your_username',
    'api_key' => 'your_api_key'
]);

$payment = $marzpay->collectMoney([
    'amount' => 1000,
    'phone_number' => '256759983853',
    'description' => 'Payment for services'
]);
```

**Documentation**: [PHP Library Guide](#php-library)

---

### 🟢 **Go Library**
**Go integration for high-performance applications**

- **Status**: 🚧 Coming Soon
- **Version**: 0.1.0 (Beta)
- **Go**: 1.19+
- **Modules**: Go modules

**Features:**
- High performance
- Context support
- Middleware support
- Comprehensive testing
- CLI tool

**Quick Start:**
```go
package main

import (
    "github.com/marzpay/collections-go"
)

func main() {
    client := marzpay.NewClient("your_username", "your_api_key")
    
    payment, err := client.CollectMoney(context.Background(), marzpay.CollectMoneyRequest{
        Amount:        1000,
        PhoneNumber:   "256759983853",
        Description:   "Payment for services",
    })
}
```

**Documentation**: [Go Library Guide](#go-library)

---

### 📱 **Mobile SDKs**
**Native mobile app integration**

- **Status**: 🚧 Coming Soon
- **Platforms**: iOS, Android, React Native, Flutter

**Features:**
- Native performance
- Offline support
- Push notifications
- Biometric authentication
- Deep linking

---

## 🎯 **WordPress Plugin Guide**

### **Installation**

#### **Method 1: WordPress Admin (Recommended)**
1. Go to **Plugins → Add New → Upload Plugin**
2. Download from [MarzPay WordPress Plugin](#)
3. Upload and activate

#### **Method 2: Manual Installation**
1. Download plugin files
2. Upload to `/wp-content/plugins/marzpay-collections/`
3. Activate from Plugins menu

### **Configuration**

1. Go to **Settings → MarzPay**
2. Enter your **API Key** and **API Secret**
3. (Optional) Set custom **Callback URL**
4. Test API connection
5. Save settings

### **Usage**

#### **Basic Payment Button**
```php
[marzpay_button amount="1000" phone="256759983853"]
```

#### **Multiple Payment Options**
```php
<h3>Choose Amount:</h3>
<p>Small: [marzpay_button amount="1000" phone="256759983853"]</p>
<p>Medium: [marzpay_button amount="5000" phone="256759983853"]</p>
<p>Large: [marzpay_button amount="10000" phone="256759983853"]</p>
```

### **Features**

- ✅ **Payment Button Shortcodes** - Easy to use
- ✅ **Admin Settings Panel** - Professional interface
- ✅ **API Testing Tools** - Built-in connection testing
- ✅ **Phone Number Validation** - Multiple format support
- ✅ **Amount Validation** - 500-10,000,000 UGX limits
- ✅ **UUID Generation** - Secure reference creation
- ✅ **Callback Support** - Configurable webhooks
- ✅ **Security Features** - Nonce verification, validation

---

## 🟨 **JavaScript Library Guide**

### **Installation**

#### **NPM Package**
```bash
npm install @marzpay/collections-js
```

#### **Yarn Package**
```bash
yarn add @marzpay/collections-js
```

#### **CDN (Browser)**
```html
<script src="https://unpkg.com/@marzpay/collections-js@latest/dist/marzpay.min.js"></script>
```

### **Configuration**

```javascript
import { MarzPay } from '@marzpay/collections-js';

const marzpay = new MarzPay({
    apiUser: 'your_username',
    apiKey: 'your_api_key',
    environment: 'production', // or 'sandbox'
    timeout: 30000, // 30 seconds
    retries: 3
});
```

### **Usage**

#### **Collect Money**
```javascript
try {
    const payment = await marzpay.collectMoney({
        amount: 1000,
        phoneNumber: '256759983853',
        description: 'Payment for services',
        callbackUrl: 'https://yoursite.com/webhook'
    });
    
    console.log('Payment initiated:', payment.reference);
} catch (error) {
    console.error('Payment failed:', error.message);
}
```

#### **Check Payment Status**
```javascript
const status = await marzpay.checkPaymentStatus('payment-reference-uuid');
console.log('Payment status:', status.status);
```

### **Features**

- ✅ **Promise-based API** - Modern async/await
- ✅ **TypeScript Support** - Full type definitions
- ✅ **Browser & Node.js** - Universal compatibility
- ✅ **Automatic Retry** - Built-in retry logic
- ✅ **Validation** - Request/response validation
- ✅ **Error Handling** - Comprehensive error management

---

## 🐍 **Python Library Guide**

### **Installation**

#### **PIP Package**
```bash
pip install marzpay-collections
```

#### **Poetry Package**
```bash
poetry add marzpay-collections
```

#### **Requirements.txt**
```
marzpay-collections==0.1.0
```

### **Configuration**

```python
from marzpay import MarzPay

marzpay = MarzPay(
    api_user="your_username",
    api_key="your_api_key",
    environment="production",  # or "sandbox"
    timeout=30,
    max_retries=3
)
```

### **Usage**

#### **Collect Money**
```python
try:
    payment = marzpay.collect_money(
        amount=1000,
        phone_number="256759983853",
        description="Payment for services",
        callback_url="https://yoursite.com/webhook"
    )
    
    print(f"Payment initiated: {payment.reference}")
except MarzPayError as e:
    print(f"Payment failed: {e.message}")
```

#### **Async Support**
```python
import asyncio
from marzpay import AsyncMarzPay

async def main():
    marzpay = AsyncMarzPay("your_username", "your_api_key")
    
    payment = await marzpay.collect_money(
        amount=1000,
        phone_number="256759983853",
        description="Payment for services"
    )
    
    print(f"Payment: {payment.reference}")

asyncio.run(main())
```

### **Features**

- ✅ **Async/Await Support** - Modern Python async
- ✅ **Pydantic Validation** - Data validation
- ✅ **Django/Flask Integration** - Framework support
- ✅ **CLI Tool** - Command-line testing
- ✅ **Comprehensive Errors** - Detailed error handling
- ✅ **Type Hints** - Full type support

---

## 🟥 **PHP Library Guide**

### **Installation**

#### **Composer Package**
```bash
composer require marzpay/collections-php
```

#### **Manual Installation**
1. Download library files
2. Include autoloader
3. Use in your application

### **Configuration**

```php
use MarzPay\MarzPay;

$marzpay = new MarzPay([
    'api_user' => 'your_username',
    'api_key' => 'your_api_key',
    'environment' => 'production', // or 'sandbox'
    'timeout' => 30,
    'max_retries' => 3
]);
```

### **Usage**

#### **Collect Money**
```php
try {
    $payment = $marzpay->collectMoney([
        'amount' => 1000,
        'phone_number' => '256759983853',
        'description' => 'Payment for services',
        'callback_url' => 'https://yoursite.com/webhook'
    ]);
    
    echo "Payment initiated: " . $payment->reference;
} catch (MarzPayException $e) {
    echo "Payment failed: " . $e->getMessage();
}
```

#### **Laravel Integration**
```php
// In your controller
use MarzPay\Laravel\Facades\MarzPay;

public function collectPayment(Request $request)
{
    $payment = MarzPay::collectMoney([
        'amount' => $request->amount,
        'phone_number' => $request->phone,
        'description' => 'Payment for order'
    ]);
    
    return response()->json($payment);
}
```

### **Features**

- ✅ **Composer Package** - Easy dependency management
- ✅ **Laravel Integration** - Laravel service provider
- ✅ **Symfony Integration** - Symfony bundle
- ✅ **PSR Standards** - PSR compliance
- ✅ **Validation** - Input validation
- ✅ **Error Handling** - Exception management

---

## 🟢 **Go Library Guide**

### **Installation**

#### **Go Modules**
```bash
go get github.com/marzpay/collections-go
```

#### **Go Workspace**
```bash
go work use github.com/marzpay/collections-go
```

### **Configuration**

```go
package main

import (
    "github.com/marzpay/collections-go"
)

func main() {
    client := marzpay.NewClient(
        "your_username",
        "your_api_key",
        marzpay.WithEnvironment("production"),
        marzpay.WithTimeout(30*time.Second),
        marzpay.WithMaxRetries(3),
    )
}
```

### **Usage**

#### **Collect Money**
```go
func collectPayment(client *marzpay.Client) error {
    ctx := context.Background()
    
    payment, err := client.CollectMoney(ctx, marzpay.CollectMoneyRequest{
        Amount:        1000,
        PhoneNumber:   "256759983853",
        Description:   "Payment for services",
        CallbackURL:   "https://yoursite.com/webhook",
    })
    
    if err != nil {
        return fmt.Errorf("payment failed: %w", err)
    }
    
    fmt.Printf("Payment initiated: %s\n", payment.Reference)
    return nil
}
```

#### **With Middleware**
```go
client := marzpay.NewClient("username", "key")
client.Use(middleware.Logging)
client.Use(middleware.Retry)
client.Use(middleware.Caching)
```

### **Features**

- ✅ **High Performance** - Go-native performance
- ✅ **Context Support** - Context cancellation
- ✅ **Middleware Support** - Extensible architecture
- ✅ **Comprehensive Testing** - Full test coverage
- ✅ **CLI Tool** - Command-line interface
- ✅ **Error Handling** - Go-style error management

---

## 📱 **Mobile SDKs Guide**

### **iOS SDK**

#### **Installation**
```bash
# CocoaPods
pod 'MarzPayCollections'

# Swift Package Manager
dependencies: [
    .package(url: "https://github.com/marzpay/collections-ios.git", from: "1.0.0")
]
```

#### **Usage**
```swift
import MarzPayCollections

let marzpay = MarzPay(apiUser: "username", apiKey: "key")

marzpay.collectMoney(
    amount: 1000,
    phoneNumber: "256759983853",
    description: "Payment for services"
) { result in
    switch result {
    case .success(let payment):
        print("Payment: \(payment.reference)")
    case .failure(let error):
        print("Error: \(error.localizedDescription)")
    }
}
```

### **Android SDK**

#### **Installation**
```gradle
dependencies {
    implementation 'com.marzpay:collections-android:1.0.0'
}
```

#### **Usage**
```kotlin
import com.marzpay.collections.MarzPay

val marzpay = MarzPay("username", "key")

marzpay.collectMoney(
    amount = 1000,
    phoneNumber = "256759983853",
    description = "Payment for services"
) { result ->
    result.onSuccess { payment ->
        println("Payment: ${payment.reference}")
    }.onFailure { error ->
        println("Error: ${error.message}")
    }
}
```

### **React Native SDK**

#### **Installation**
```bash
npm install @marzpay/collections-react-native
```

#### **Usage**
```javascript
import { MarzPay } from '@marzpay/collections-react-native';

const marzpay = new MarzPay('username', 'key');

try {
    const payment = await marzpay.collectMoney({
        amount: 1000,
        phoneNumber: '256759983853',
        description: 'Payment for services'
    });
    
    console.log('Payment:', payment.reference);
} catch (error) {
    console.error('Error:', error.message);
}
```

### **Flutter SDK**

#### **Installation**
```yaml
dependencies:
  marzpay_collections: ^1.0.0
```

#### **Usage**
```dart
import 'package:marzpay_collections/marzpay_collections.dart';

final marzpay = MarzPay(apiUser: 'username', apiKey: 'key');

try {
  final payment = await marzpay.collectMoney(
    amount: 1000,
    phoneNumber: '256759983853',
    description: 'Payment for services',
  );
  
  print('Payment: ${payment.reference}');
} catch (error) {
  print('Error: $error');
}
```

---

## 🔧 **Common Configuration**

### **Environment Variables**

All libraries support environment variable configuration:

```bash
# Required
MARZPAY_API_USER=your_username
MARZPAY_API_KEY=your_api_key

# Optional
MARZPAY_ENVIRONMENT=production
MARZPAY_TIMEOUT=30
MARZPAY_MAX_RETRIES=3
MARZPAY_CALLBACK_URL=https://yoursite.com/webhook
```

### **Configuration Options**

| Option | Default | Description |
|--------|---------|-------------|
| `environment` | `production` | API environment (production/sandbox) |
| `timeout` | `30` | Request timeout in seconds |
| `max_retries` | `3` | Maximum retry attempts |
| `callback_url` | `null` | Default callback URL |
| `user_agent` | `marzpay-{library}-{version}` | Custom user agent |

---

## 🧪 **Testing & Development**

### **Sandbox Environment**

All libraries support sandbox mode for testing:

```javascript
// JavaScript
const marzpay = new MarzPay({
    apiUser: 'sandbox_user',
    apiKey: 'sandbox_key',
    environment: 'sandbox'
});
```

```python
# Python
marzpay = MarzPay(
    api_user="sandbox_user",
    api_key="sandbox_key",
    environment="sandbox"
)
```

```php
// PHP
$marzpay = new MarzPay([
    'api_user' => 'sandbox_user',
    'api_key' => 'sandbox_key',
    'environment' => 'sandbox'
]);
```

### **Test Credentials**

Use these credentials for testing:

- **API Key**: `sandbox_user`
- **API Secret**: `sandbox_key`
- **Test Phone**: `256759983853`
- **Test Amount**: `500` (minimum)

---

## 📚 **Examples & Tutorials**

### **WordPress Integration**
- [Complete WordPress Plugin Guide](#wordpress-plugin)
- [Custom Payment Forms](examples/wordpress-custom-forms.md)
- [WooCommerce Integration](examples/woocommerce-integration.md)
- [Multi-site Setup](examples/wordpress-multisite.md)

### **JavaScript Integration**
- [React.js Payment Component](examples/react-payment-component.md)
- [Vue.js Integration](examples/vue-integration.md)
- [Node.js Express Server](examples/node-express-server.md)
- [Browser-based Payments](examples/browser-payments.md)

### **Python Integration**
- [Django Payment App](examples/django-payment-app.md)
- [Flask API Server](examples/flask-api-server.md)
- [FastAPI Integration](examples/fastapi-integration.md)
- [Payment Automation Scripts](examples/python-automation.md)

### **PHP Integration**
- [Laravel Payment Package](examples/laravel-payment-package.md)
- [Symfony Payment Bundle](examples/symfony-payment-bundle.md)
- [Custom PHP Application](examples/custom-php-app.md)
- [Payment Webhook Handler](examples/php-webhook-handler.md)

### **Go Integration**
- [Gin Web Server](examples/gin-web-server.md)
- [Echo Framework](examples/echo-framework.md)
- [Microservices](examples/go-microservices.md)
- [CLI Payment Tool](examples/go-cli-tool.md)

### **Mobile Integration**
- [iOS Payment App](examples/ios-payment-app.md)
- [Android Payment App](examples/android-payment-app.md)
- [React Native App](examples/react-native-app.md)
- [Flutter App](examples/flutter-app.md)

---

## 🚀 **Getting Started**

### **1. Choose Your Library**
Select the library that matches your technology stack from the options above.

### **2. Get API Credentials**
- Sign up at [wearemarz.com](https://wearemarz.com)
- Navigate to API settings
- Copy your API Key and API Secret

### **3. Install Library**
Follow the installation instructions for your chosen library.

### **4. Configure Library**
Set up your API credentials and configuration options.

### **5. Test Integration**
Use the sandbox environment to test your integration.

### **6. Go Live**
Switch to production environment and start accepting payments!

---

## 📞 **Support & Resources**

### **Documentation**
- [API Reference](../api-reference.md)
- [Integration Guides](integration-guides.md)
- [Examples & Tutorials](examples.md)
- [Troubleshooting](troubleshooting.md)

### **Community**
- [Developer Forum](https://community.wearemarz.com)
- [GitHub Discussions](https://github.com/marzpay/collections/discussions)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/marzpay)

### **Support**
- **Technical Support**: [support@wearemarz.com](mailto:support@wearemarz.com)
- **Business Support**: [business@wearemarz.com](mailto:business@wearemarz.com)
- **Emergency**: [emergency@wearemarz.com](mailto:emergency@wearemarz.com)

### **Status**
- **Service Status**: [status.wearemarz.com](https://status.wearemarz.com)
- **API Status**: [api-status.wearemarz.com](https://api-status.wearemarz.com)

---

## 🌟 **Contributing**

We welcome contributions to improve our integration libraries!

### **How to Contribute**
1. Fork the library repository
2. Create a feature branch
3. Make your improvements
4. Add tests and documentation
5. Submit a pull request

### **Development Setup**
Each library includes detailed development setup instructions in its README.

---

**Ready to integrate MarzPay into your application? Choose your library above and get started today!** 🚀
