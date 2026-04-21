# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-22 - REST API Rate Limiting and DoS Protection in Pricing Calculations
**Vulnerability:** The public `calculate-price` REST API endpoint lacked rate limiting and proper input sanitization, allowing for potential resource exhaustion (DoS) by submitting large, complex design objects for pricing calculation.
**Learning:** Publicly accessible endpoints that perform iterative processing on user-provided arrays (like design elements) are prime targets for DoS. Sanitization should not only clean data but also enforce structural constraints like array length limits.
**Prevention:** Implement mandatory rate limiting on public endpoints and enforce a maximum element count (e.g., 50) using `array_slice` during the sanitization phase of nested data structures.
