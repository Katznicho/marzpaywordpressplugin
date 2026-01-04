/**
 * MarzPay Frontend JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        MarzPayFrontend.init();
    });
    
    // MarzPay Frontend Object
    window.MarzPayFrontend = {
        
        init: function() {
            this.bindEvents();
            this.initForms();
        },
        
        bindEvents: function() {
            // Form submissions
            $(document).on('submit', '.marzpay-form', this.handleFormSubmit);
            
            // Phone number formatting
            $(document).on('input', 'input[type="tel"]', this.formatPhoneNumber);
            
            // Amount formatting
            $(document).on('input', 'input[name="amount"]', this.formatAmount);
        },
        
        initForms: function() {
            // Add loading states to buttons
            $('.marzpay-button').each(function() {
                var $button = $(this);
                var originalText = $button.text();
                $button.data('original-text', originalText);
            });
        },
        
        handleFormSubmit: function(e) {
            e.preventDefault();
            
            var $form = $(this);
            var $button = $form.find('.marzpay-button');
            var action = $form.data('action') || 'marzpay_collect_money';
            
            // Validate form
            if (!MarzPayFrontend.validateForm($form)) {
                return false;
            }
            
            // Show loading state
            MarzPayFrontend.setButtonLoading($button, true);
            
            // Prepare form data
            var formData = {
                action: action,
                nonce: marzpay_ajax.nonce,
                amount: $form.find('input[name="amount"]').val(),
                phone: $form.find('input[name="phone"]').val(),
                reference: $form.find('input[name="reference"]').val(),
                description: $form.find('input[name="description"], textarea[name="description"]').val(),
                callback_url: $form.find('input[name="callback_url"]').val(),
                country: $form.find('select[name="country"]').val() || 'UG'
            };
            
            // Make AJAX request
            $.ajax({
                url: marzpay_ajax.ajax_url,
                type: 'POST',
                data: formData,
                success: function(response) {
                    MarzPayFrontend.handleResponse(response, $form);
                },
                error: function(xhr, status, error) {
                    MarzPayFrontend.showError('An error occurred. Please try again.');
                },
                complete: function() {
                    MarzPayFrontend.setButtonLoading($button, false);
                }
            });
        },
        
        validateForm: function($form) {
            var isValid = true;
            var errors = [];
            
            // Clear previous errors
            $form.find('.marzpay-error').remove();
            
            // Validate amount
            var amount = $form.find('input[name="amount"]').val();
            if (!amount || isNaN(amount) || parseInt(amount) < 100) {
                errors.push('Amount must be at least 100 UGX');
                isValid = false;
            }
            
            // Validate phone number
            var phone = $form.find('input[name="phone"]').val();
            if (!phone) {
                errors.push('Phone number is required');
                isValid = false;
            } else if (!MarzPayFrontend.validatePhoneNumber(phone)) {
                errors.push('Invalid phone number format. Please use: +256759983853, 256759983853, or 0759983853');
                isValid = false;
            }
            
            // Show errors
            if (!isValid) {
                MarzPayFrontend.showError(errors.join('<br>'));
            }
            
            return isValid;
        },
        
        validatePhoneNumber: function(phone) {
            // Remove all non-numeric characters except +
            var cleaned = phone.replace(/[^0-9+]/g, '');
            
            // Check if it's a valid Uganda phone number
            // Accept formats that match API client validation:
            // +256XXXXXXXXX (13 chars), 256XXXXXXXXX (12 digits), or 07XXXXXXXXX (10 digits)
            if (cleaned.length === 13 && cleaned.startsWith('+256')) {
                return true;
            } else if (cleaned.length === 12 && cleaned.startsWith('256')) {
                return true;
            } else if (cleaned.length === 10 && cleaned.startsWith('0')) {
                return true;
            }
            
            return false;
        },
        
        formatPhoneNumber: function() {
            var $input = $(this);
            var value = $input.val();
            
            // Allow users to type freely - don't restrict formatting during input
            // Just store the raw value, formatting can happen on blur if needed
            // The validation will handle different formats
        },
        
        formatAmount: function() {
            var $input = $(this);
            var value = $input.val().replace(/\D/g, '');
            
            // Add thousand separators
            if (value.length > 3) {
                value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            
            $input.val(value);
        },
        
        handleResponse: function(response, $form) {
            if (response.success) {
                MarzPayFrontend.showSuccess(response.data.message || 'Transaction initiated successfully!');
                
                // Clear form if it's a new transaction
                if ($form.hasClass('marzpay-clear-on-success')) {
                    $form[0].reset();
                }
                
                // Redirect if URL is provided
                if (response.data.redirect_url) {
                    setTimeout(function() {
                        window.location.href = response.data.redirect_url;
                    }, 2000);
                }
            } else {
                var errorMessage = response.data && response.data.message ? 
                    response.data.message : 'Transaction failed. Please try again.';
                MarzPayFrontend.showError(errorMessage);
            }
        },
        
        setButtonLoading: function($button, loading) {
            if (loading) {
                $button.addClass('loading').prop('disabled', true);
            } else {
                $button.removeClass('loading').prop('disabled', false);
            }
        },
        
        showSuccess: function(message) {
            MarzPayFrontend.showMessage(message, 'success');
        },
        
        showError: function(message) {
            MarzPayFrontend.showMessage(message, 'error');
        },
        
        showMessage: function(message, type) {
            // Remove existing messages
            $('.marzpay-message').remove();
            
            // Create new message
            var $message = $('<div class="marzpay-message marzpay-' + type + '">' + message + '</div>');
            
            // Insert at the top of the form or page
            var $target = $('.marzpay-form').first();
            if ($target.length) {
                $target.before($message);
            } else {
                $('body').prepend($message);
            }
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $message.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: $message.offset().top - 100
            }, 500);
        },
        
        // Utility functions
        formatCurrency: function(amount, currency) {
            currency = currency || 'UGX';
            return new Intl.NumberFormat('en-UG', {
                style: 'currency',
                currency: currency
            }).format(amount);
        },
        
        formatDate: function(date) {
            return new Intl.DateTimeFormat('en-UG', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            }).format(new Date(date));
        }
    };
    
    // Global utility functions
    window.marzpayFormatCurrency = MarzPayFrontend.formatCurrency;
    window.marzpayFormatDate = MarzPayFrontend.formatDate;
    
})(jQuery);
