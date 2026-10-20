<?php
/**
 * Security Headers Helper
 * Sets standard HTTP security headers to protect against common web attacks.
 */
if (!headers_sent()) {
    header("X-Frame-Options: SAMEORIGIN");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("X-XSS-Protection: 1; mode=block");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
}
