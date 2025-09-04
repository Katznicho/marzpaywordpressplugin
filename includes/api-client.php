<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function marzpay_request_payment($amount, $phone, $reference = null) {
    // Validate inputs according to MarzPay API requirements
    if (empty($amount) || !is_numeric($amount)) {
        return array('status' => 'error', 'message' => 'Invalid amount. Amount must be a numeric value.');
    }
    
    // Check amount limits: min 500, max 10,000,000 UGX
    if ($amount < 500) {
        return array('status' => 'error', 'message' => 'The minimum amount for collection is 500 UGX.');
    }
    
    if ($amount > 10000000) {
        return array('status' => 'error', 'message' => 'The maximum amount for collection is 10,000,000 UGX.');
    }
    
    if (empty($phone)) {
        return array('status' => 'error', 'message' => 'Phone number is required.');
    }

    $api_user = get_option('marzpay_api_user');
    $api_key  = get_option('marzpay_api_key');

    if (empty($api_user) || empty($api_key)) {
        return array('status' => 'error', 'message' => 'Missing API credentials. Please configure your MarzPay API settings in the admin panel.');
    }

    // Clean and validate phone number according to MarzPay regex: /^\+[0-9]{10,15}$/
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    // Ensure phone has + prefix and meets length requirements (10-15 digits after +)
    if (strpos($phone, '+') !== 0) {
        // Handle different formats and convert to + format
        if (strlen($phone) === 12 && substr($phone, 0, 3) === '256') {
            $phone = '+' . $phone;
        } elseif (strlen($phone) === 9 && substr($phone, 0, 1) === '0') {
            $phone = '+256' . substr($phone, 1);
        } elseif (strlen($phone) === 10 && substr($phone, 0, 2) === '07') {
            $phone = '+256' . substr($phone, 1);
        } else {
            $phone = '+256' . $phone;
        }
    }
    
    // Validate phone number format according to MarzPay requirements
    if (!preg_match('/^\+[0-9]{10,15}$/', $phone)) {
        return array('status' => 'error', 'message' => 'Please enter a valid phone number with country code (e.g., +256712345678). Phone number must start with + and have 10-15 digits.');
    }

    // Generate proper UUID v4 for reference if not provided (must match regex: /^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i)
    if (empty($reference)) {
        $reference = generate_uuid_v4_robust();
    }
    
    // Validate reference UUID format
    if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $reference)) {
        return array('status' => 'error', 'message' => 'Reference must be a valid UUID format (e.g., 123e4567-e89b-12d3-a456-426614174000). Generated: ' . $reference . ' (Length: ' . strlen($reference) . ')');
    }

    // Use the correct MarzPay API endpoint
    $endpoint = 'https://wallet.wearemarz.com/api/v1/collect-money';

    // Log request for debugging (only if WP_DEBUG is enabled)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('MarzPay API Request - Endpoint: ' . $endpoint);
        error_log('MarzPay API Request - Phone: ' . $phone);
        error_log('MarzPay API Request - Amount: ' . $amount . ' UGX');
        error_log('MarzPay API Request - Reference (UUID): ' . $reference);
        error_log('MarzPay API Request - User: ' . $api_user);
    }

    // Use the exact request body format that MarzPay expects
    $body = array(
        'amount' => (int) $amount,
        'phone_number' => $phone,
        'reference' => $reference,
        'description' => 'Payment for services',
        'callback_url' => get_option('marzpay_callback_url') ?: home_url('/marzpay-callback'),
        'country' => 'UG'
    );

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('MarzPay API Request - Body: ' . json_encode($body));
    }

    $response = wp_remote_post($endpoint, array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($api_user . ':' . $api_key)
        ),
        'body'    => json_encode($body),
        'timeout' => 45
    ));

    if (is_wp_error($response)) {
        return array('status' => 'error', 'message' => 'Network error: ' . $response->get_error_message());
    }

    // Check HTTP status code
    $status_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    $response_headers = wp_remote_retrieve_headers($response);

    // Log response for debugging (only if WP_DEBUG is enabled)
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('MarzPay API Response - Status: ' . $status_code);
        error_log('MarzPay API Response - Headers: ' . print_r($response_headers, true));
        error_log('MarzPay API Response - Body: ' . $response_body);
    }

    // Check if the request was successful
    if ($status_code < 200 || $status_code >= 300) {
        // Truncate response body to avoid overly long error messages
        $truncated_body = strlen($response_body) > 500 ? substr($response_body, 0, 500) . '...' : $response_body;
        
        return array(
            'status' => 'error', 
            'message' => 'API request failed with status code: ' . $status_code . '. Endpoint: ' . $endpoint . '. Response: ' . $truncated_body,
            'status_code' => $status_code,
            'endpoint' => $endpoint,
            'full_response' => $response_body
        );
    }

    // Try to decode JSON response
    $data = json_decode($response_body, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Truncate response body to avoid overly long error messages
        $truncated_body = strlen($response_body) > 500 ? substr($response_body, 0, 500) . '...' : $response_body;
        
        return array(
            'status' => 'error', 
            'message' => 'Invalid JSON response from API. Endpoint: ' . $endpoint . '. JSON Error: ' . json_last_error_msg() . '. Response: ' . $truncated_body,
            'json_error' => json_last_error_msg(),
            'endpoint' => $endpoint,
            'full_response' => $response_body
        );
    }

    // If we have data, return it; otherwise return a more specific error
    if ($data && is_array($data)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('MarzPay API Success - Response received successfully');
        }
        return $data;
    } else {
        // Truncate response body to avoid overly long error messages
        $truncated_body = strlen($response_body) > 500 ? substr($response_body, 0, 500) . '...' : $response_body;
        
        return array(
            'status' => 'error', 
            'message' => 'Empty or invalid response from API. Endpoint: ' . $endpoint . '. Response: ' . $truncated_body,
            'endpoint' => $endpoint,
            'full_response' => $response_body
        );
    }
}

/**
 * Generate a valid UUID v4 that works across all PHP versions
 * Format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
 * Where x is any hexadecimal digit and y is one of 8, 9, A, or B
 */
function generate_uuid_v4_robust() {
    // Try to use random_bytes if available (PHP 7+)
    if (function_exists('random_bytes')) {
        try {
            $hex = bin2hex(random_bytes(16));
        } catch (Exception $e) {
            $hex = generate_random_hex(16);
        }
    } else {
        // Fallback for older PHP versions
        $hex = generate_random_hex(16);
    }
    
    // Format as UUID v4: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
    $uuid = sprintf(
        '%s-%s-4%s-%s-%s',
        substr($hex, 0, 8),
        substr($hex, 8, 4),
        substr($hex, 12, 3),
        substr($hex, 16, 4),
        substr($hex, 20, 12)
    );
    
    return $uuid;
}

/**
 * Generate random hex string as fallback
 */
function generate_random_hex($length) {
    $hex = '';
    for ($i = 0; $i < $length; $i++) {
        $hex .= dechex(mt_rand(0, 15));
    }
    return $hex;
}
