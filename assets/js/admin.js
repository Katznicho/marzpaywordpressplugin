/**
 * MarzPay Admin JavaScript
 */

(function($) {
    'use strict';
    
    // Initialize when document is ready
    $(document).ready(function() {
        MarzPayAdmin.init();
    });
    
    // MarzPay Admin Object
    window.MarzPayAdmin = {
        
        init: function() {
            this.bindEvents();
            this.initComponents();
        },
        
        bindEvents: function() {
            // Tab switching
            $(document).on('click', '.nav-tab', this.handleTabSwitch);
            
            // Form submissions
            $(document).on('submit', '.marzpay-admin-form', this.handleFormSubmit);
            
            // Button clicks
            $(document).on('click', '.marzpay-button', this.handleButtonClick);
            
            // Webhook management
            $(document).on('click', '.test-webhook', this.testWebhook);
            $(document).on('click', '.toggle-webhook', this.toggleWebhook);
            $(document).on('click', '.delete-webhook', this.deleteWebhook);
            
            // Transaction actions
            $(document).on('click', '.refresh-transaction', this.refreshTransaction);
            $(document).on('click', '.view-transaction', this.viewTransaction);
            
            // Copy to clipboard
            $(document).on('click', '.copy-to-clipboard', this.copyToClipboard);
            
            // Generate webhook secret
            $(document).on('click', '#generate-webhook-secret', this.generateWebhookSecret);
            
            // Test API connection
            $(document).on('click', '#test-api-connection', this.testApiConnection);
        },
        
        initComponents: function() {
            // Initialize tooltips
            this.initTooltips();
            
            // Initialize data tables
            this.initDataTables();
            
            // Initialize filters
            this.initFilters();
        },
        
        handleTabSwitch: function(e) {
            e.preventDefault();
            
            var $tab = $(this);
            var target = $tab.attr('href');
            
            // Update active tab
            $tab.siblings('.nav-tab').removeClass('nav-tab-active');
            $tab.addClass('nav-tab-active');
            
            // Show target content
            $('.tab-content').removeClass('active');
            $(target).addClass('active');
        },
        
        handleFormSubmit: function(e) {
            var $form = $(this);
            var $button = $form.find('input[type="submit"], button[type="submit"]');
            
            // Show loading state
            MarzPayAdmin.setButtonLoading($button, true);
            
            // Form will submit normally, loading state will be reset on page reload
        },
        
        handleButtonClick: function(e) {
            var $button = $(this);
            var action = $button.data('action');
            
            if (action) {
                e.preventDefault();
                MarzPayAdmin.handleAction(action, $button);
            }
        },
        
        handleAction: function(action, $button) {
            switch (action) {
                case 'test-api':
                    MarzPayAdmin.testApiConnection();
                    break;
                case 'generate-secret':
                    MarzPayAdmin.generateWebhookSecret();
                    break;
                case 'copy-url':
                    MarzPayAdmin.copyToClipboard($button);
                    break;
                default:
                    console.log('Unknown action:', action);
            }
        },
        
        testApiConnection: function() {
            var $button = $('#test-api-connection');
            var $result = $('#api-test-result');
            
            MarzPayAdmin.setButtonLoading($button, true);
            $result.html('');
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'marzpay_test_api',
                    nonce: marzpay_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<div class="notice notice-success inline"><p>' + response.data.message + '</p></div>');
                    } else {
                        $result.html('<div class="notice notice-error inline"><p>' + response.data.message + '</p></div>');
                    }
                },
                error: function() {
                    $result.html('<div class="notice notice-error inline"><p>Connection test failed.</p></div>');
                },
                complete: function() {
                    MarzPayAdmin.setButtonLoading($button, false);
                }
            });
        },
        
        generateWebhookSecret: function() {
            var secret = Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
            $('#marzpay_webhook_secret').val(secret);
            
            MarzPayAdmin.showNotice('New webhook secret generated.', 'success');
        },
        
        copyToClipboard: function($button) {
            var text = $button.data('text') || $button.text();
            var url = $button.data('url');
            
            if (url) {
                text = url;
            }
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(function() {
                    MarzPayAdmin.showNotice('Copied to clipboard!', 'success');
                });
            } else {
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = text;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                MarzPayAdmin.showNotice('Copied to clipboard!', 'success');
            }
        },
        
        testWebhook: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var webhookId = $button.data('webhook-id');
            
            if (!confirm('Are you sure you want to test this webhook?')) {
                return;
            }
            
            MarzPayAdmin.setButtonLoading($button, true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'marzpay_test_webhook',
                    webhook_id: webhookId,
                    nonce: marzpay_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MarzPayAdmin.showNotice('Test webhook sent successfully!', 'success');
                    } else {
                        MarzPayAdmin.showNotice('Test webhook failed: ' + response.data.message, 'error');
                    }
                },
                error: function() {
                    MarzPayAdmin.showNotice('Test webhook failed.', 'error');
                },
                complete: function() {
                    MarzPayAdmin.setButtonLoading($button, false);
                }
            });
        },
        
        toggleWebhook: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var webhookId = $button.data('webhook-id');
            var currentStatus = $button.data('status');
            var newStatus = currentStatus === 'active' ? 'inactive' : 'active';
            
            MarzPayAdmin.setButtonLoading($button, true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'marzpay_toggle_webhook',
                    webhook_id: webhookId,
                    status: newStatus,
                    nonce: marzpay_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MarzPayAdmin.showNotice('Webhook status updated successfully!', 'success');
                        location.reload();
                    } else {
                        MarzPayAdmin.showNotice('Failed to update webhook status.', 'error');
                    }
                },
                error: function() {
                    MarzPayAdmin.showNotice('Failed to update webhook status.', 'error');
                },
                complete: function() {
                    MarzPayAdmin.setButtonLoading($button, false);
                }
            });
        },
        
        deleteWebhook: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var webhookId = $button.data('webhook-id');
            var webhookName = $button.data('webhook-name');
            
            if (!confirm('Are you sure you want to delete the webhook "' + webhookName + '"? This action cannot be undone.')) {
                return;
            }
            
            MarzPayAdmin.setButtonLoading($button, true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'marzpay_delete_webhook',
                    webhook_id: webhookId,
                    nonce: marzpay_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MarzPayAdmin.showNotice('Webhook deleted successfully!', 'success');
                        $button.closest('.marzpay-webhook-item').fadeOut();
                    } else {
                        MarzPayAdmin.showNotice('Failed to delete webhook.', 'error');
                    }
                },
                error: function() {
                    MarzPayAdmin.showNotice('Failed to delete webhook.', 'error');
                },
                complete: function() {
                    MarzPayAdmin.setButtonLoading($button, false);
                }
            });
        },
        
        refreshTransaction: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var transactionId = $button.data('transaction-id');
            
            MarzPayAdmin.setButtonLoading($button, true);
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'marzpay_refresh_transaction',
                    transaction_id: transactionId,
                    nonce: marzpay_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        MarzPayAdmin.showNotice('Transaction status updated successfully!', 'success');
                        location.reload();
                    } else {
                        MarzPayAdmin.showNotice('Failed to update transaction status.', 'error');
                    }
                },
                error: function() {
                    MarzPayAdmin.showNotice('Failed to update transaction status.', 'error');
                },
                complete: function() {
                    MarzPayAdmin.setButtonLoading($button, false);
                }
            });
        },
        
        viewTransaction: function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var transactionId = $button.data('transaction-id');
            
            // Open transaction details in a modal or new window
            window.open(admin_url + 'admin.php?page=marzpay-transactions&action=view&transaction_id=' + transactionId, '_blank');
        },
        
        initTooltips: function() {
            // Initialize tooltips if tooltip library is available
            if (typeof $.fn.tooltip === 'function') {
                $('[data-tooltip]').tooltip();
            }
        },
        
        initDataTables: function() {
            // Initialize data tables if DataTables library is available
            if (typeof $.fn.DataTable === 'function') {
                $('.marzpay-table').DataTable({
                    pageLength: 25,
                    order: [[0, 'desc']],
                    responsive: true,
                    language: {
                        search: "Search:",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        paginate: {
                            first: "First",
                            last: "Last",
                            next: "Next",
                            previous: "Previous"
                        }
                    }
                });
            }
        },
        
        initFilters: function() {
            // Auto-submit filter forms on change
            $('.marzpay-filters select').on('change', function() {
                $(this).closest('form').submit();
            });
        },
        
        setButtonLoading: function($button, loading) {
            if (loading) {
                $button.addClass('loading').prop('disabled', true);
                if ($button.is('input[type="submit"]')) {
                    $button.val('Loading...');
                } else {
                    $button.data('original-text', $button.text());
                    $button.text('Loading...');
                }
            } else {
                $button.removeClass('loading').prop('disabled', false);
                if ($button.is('input[type="submit"]')) {
                    $button.val($button.data('original-value') || 'Save');
                } else {
                    $button.text($button.data('original-text') || 'Submit');
                }
            }
        },
        
        showNotice: function(message, type) {
            type = type || 'info';
            
            // Remove existing notices
            $('.marzpay-admin-notice').remove();
            
            // Create new notice
            var $notice = $('<div class="marzpay-admin-notice notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
            
            // Insert at the top of the page
            $('.wrap h1').after($notice);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                $notice.fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Scroll to notice
            $('html, body').animate({
                scrollTop: $notice.offset().top - 100
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
        },
        
        formatPhone: function(phone) {
            // Format phone number for display
            phone = phone.replace(/\D/g, '');
            if (phone.length === 12 && phone.startsWith('256')) {
                return phone.substring(3, 6) + ' ' + phone.substring(6, 9) + ' ' + phone.substring(9);
            } else if (phone.length === 9) {
                return phone.substring(0, 3) + ' ' + phone.substring(3, 6) + ' ' + phone.substring(6);
            }
            return phone;
        }
    };
    
    // Global utility functions
    window.marzpayAdminFormatCurrency = MarzPayAdmin.formatCurrency;
    window.marzpayAdminFormatDate = MarzPayAdmin.formatDate;
    window.marzpayAdminFormatPhone = MarzPayAdmin.formatPhone;
    
})(jQuery);
