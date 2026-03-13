<?php
/**
 * Want to debug, test the scripts and avoid "Access denied" ?
 * Just put $debug = true; instead of false !
 */
$debug = false;
//$debug = true;
session_start();

/**
 * Send recommended security headers for every response.
 * Call this function before any output is sent.
 */
function sendSecurityHeaders()
{
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    // Allow only same-origin scripts; adjust if you use a CDN
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:;");
}

/**
 * Validate that the incoming POST request carries a valid CSRF token.
 * HTTP_REFERER is intentionally NOT used because it can be spoofed by the client.
 *
 * @return bool
 */
function isValidRequest()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        return false;
    }
    // Use hash_equals to prevent timing attacks
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        return false;
    }
    return true;
}

sendSecurityHeaders();

if (!$debug && !isValidRequest()) {
    header('Content-Type: application/json');
    echo "{\"error\":\"Access denied\"}";
    exit;
}
