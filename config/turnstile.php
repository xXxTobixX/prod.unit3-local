<?php
/**
 * Cloudflare Turnstile Configuration
 * 
 * Get your keys from the Cloudflare Dashboard:
 * https://dash.cloudflare.com/?to=/:account/turnstile
 */

// Default to test keys if not set in environment
// Test Site Key: 1x00000000000000000000AA
// Test Secret Key: 1x00000000000000000000AA

define('TURNSTILE_SITE_KEY', '1x00000000000000000000AA'); 
define('TURNSTILE_SECRET_KEY', '1x00000000000000000000AA');

/**
 * Verify Turnstile Response
 * 
 * @param string $token The cf-turnstile-response token from the frontend
 * @param string $ip The user's IP address (optional)
 * @return array ['success' => bool, 'message' => string]
 */
function verifyTurnstile($token, $ip = null) {
    if (empty($token)) {
        return ['success' => false, 'message' => 'Please complete the CAPTCHA.'];
    }

    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    $data = [
        'secret' => TURNSTILE_SECRET_KEY,
        'response' => $token,
        'remoteip' => $ip
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    if ($result === FALSE) {
        return ['success' => false, 'message' => 'Failed to validate CAPTCHA.'];
    }

    $response = json_decode($result, true);

    if ($response['success']) {
        return ['success' => true];
    } else {
        // Log error codes for debugging
        if (isset($response['error-codes'])) {
            error_log('Turnstile Error: ' . implode(', ', $response['error-codes']));
        }
        return ['success' => false, 'message' => 'CAPTCHA validation failed. Please try again.'];
    }
}
