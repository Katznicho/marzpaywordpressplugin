/**
 * MarzPay WooCommerce Admin JavaScript
 */

jQuery(document).ready(function($) {
    
    // Check payment status from order edit page
    $('#check-payment-status').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var uuid = button.data('uuid');
        
        button.prop('disabled', true).text('Checking...');
        
        $.ajax({
            url: marzpay_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'marzpay_check_woocommerce_status',
                uuid: uuid,
                nonce: marzpay_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Reload the page to show updated status
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Connection error. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false).text('Check Payment Status');
            }
        });
    });
    
    // Auto-refresh payment status every 30 seconds for pending orders
    if ($('.marzpay-status-pending, .marzpay-status-processing').length > 0) {
        setInterval(function() {
            $('.marzpay-status-pending, .marzpay-status-processing').each(function() {
                var statusElement = $(this);
                var row = statusElement.closest('tr');
                var checkButton = row.find('.check-status');
                
                if (checkButton.length > 0 && !checkButton.prop('disabled')) {
                    checkButton.trigger('click');
                }
            });
        }, 30000); // 30 seconds
    }
    
    // Add payment method icon to checkout
    if ($('body').hasClass('woocommerce-checkout')) {
        $('input[value="marzpay"]').closest('li').find('label').prepend(
            '<img src="' + marzpay_ajax.plugin_url + 'assets/images/mobile-money-icon.png" style="width: 20px; height: 20px; margin-right: 8px; vertical-align: middle;" alt="Mobile Money">'
        );
    }
    
    // Phone number validation
    $('#marzpay_phone').on('blur', function() {
        var phone = $(this).val();
        var formatted = formatPhoneNumber(phone);
        
        if (formatted && formatted !== phone) {
            $(this).val(formatted);
        }
    });
    
    // Format phone number helper function
    function formatPhoneNumber(phone) {
        // Remove all non-numeric characters except +
        phone = phone.replace(/[^0-9+]/g, '');
        
        // Convert to international format
        if (phone.length === 10 && phone.charAt(0) === '0') {
            // Convert 0759983853 to +256759983853
            return '+256' + phone.substring(1);
        } else if (phone.length === 12 && phone.substring(0, 3) === '256') {
            // Convert 256759983853 to +256759983853
            return '+' + phone;
        } else if (phone.length === 13 && phone.substring(0, 4) === '+256') {
            // Already in correct format
            return phone;
        }
        
        return phone;
    }
    
    // Show payment instructions when MarzPay is selected
    $('input[name="payment_method"]').on('change', function() {
        if ($(this).val() === 'marzpay') {
            showMarzPayInstructions();
        } else {
            hideMarzPayInstructions();
        }
    });
    
    function showMarzPayInstructions() {
        if ($('#marzpay-instructions').length === 0) {
            var instructions = $('<div id="marzpay-instructions" class="marzpay-payment-instructions">' +
                '<h3>Mobile Money Payment Instructions</h3>' +
                '<ol>' +
                '<li>Complete your order below</li>' +
                '<li>You will receive a mobile money prompt on your phone</li>' +
                '<li>Enter your mobile money PIN to confirm payment</li>' +
                '<li>Your order will be processed automatically</li>' +
                '</ol>' +
                '<p><strong>Supported Networks:</strong> Airtel Money, MTN Mobile Money</p>' +
                '</div>');
            
            $('#order_review').before(instructions);
        }
        $('#marzpay-instructions').show();
    }
    
    function hideMarzPayInstructions() {
        $('#marzpay-instructions').hide();
    }
    
    // Initialize instructions if MarzPay is already selected
    if ($('input[name="payment_method"][value="marzpay"]').is(':checked')) {
        showMarzPayInstructions();
    }
    
    // Add loading state to place order button
    $('form.checkout').on('submit', function() {
        if ($('input[name="payment_method"]:checked').val() === 'marzpay') {
            var placeOrderButton = $('#place_order');
            placeOrderButton.prop('disabled', true).text('Processing Payment...');
            
            // Re-enable button after 10 seconds as fallback
            setTimeout(function() {
                placeOrderButton.prop('disabled', false).text('Place order');
            }, 10000);
        }
    });
    
    // Show payment status in order received page
    if ($('body').hasClass('woocommerce-order-received')) {
        var orderId = $('.woocommerce-order-details').data('order-id');
        
        if (orderId) {
            checkOrderPaymentStatus(orderId);
        }
    }
    
    function checkOrderPaymentStatus(orderId) {
        $.ajax({
            url: marzpay_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'marzpay_get_order_status',
                order_id: orderId,
                nonce: marzpay_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data.status) {
                    updateOrderStatusDisplay(response.data);
                }
            }
        });
    }
    
    function updateOrderStatusDisplay(data) {
        var statusElement = $('.marzpay-payment-status');
        
        if (statusElement.length === 0) {
            statusElement = $('<div class="marzpay-payment-status"></div>');
            $('.woocommerce-order-details').after(statusElement);
        }
        
        var statusHtml = '<h3>Payment Status</h3>';
        
        switch (data.status) {
            case 'successful':
            case 'completed':
                statusHtml += '<p class="marzpay-status-success">✓ Payment confirmed successfully!</p>';
                break;
            case 'pending':
            case 'processing':
                statusHtml += '<p class="marzpay-status-pending">⏳ Payment is being processed...</p>';
                statusHtml += '<p><small>You will receive a mobile money prompt on your phone. Please complete the payment.</small></p>';
                // Auto-refresh every 10 seconds for pending payments
                setTimeout(function() {
                    checkOrderPaymentStatus($('.woocommerce-order-details').data('order-id'));
                }, 10000);
                break;
            case 'failed':
            case 'cancelled':
                statusHtml += '<p class="marzpay-status-failed">✗ Payment failed or was cancelled.</p>';
                statusHtml += '<p><a href="' + marzpay_ajax.checkout_url + '" class="button">Try Again</a></p>';
                break;
        }
        
        statusElement.html(statusHtml);
    }
});
