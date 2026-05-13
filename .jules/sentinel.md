# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - REST API Rate Limiting and Input Sanitization for DoS Prevention
**Vulnerability:** The publicly accessible `/calculate-price` REST API endpoint lacked rate limiting and deep input sanitization, allowing for potential DoS attacks via complex or malformed design data payloads.
**Learning:** Even non-destructive endpoints can be a security risk if they perform resource-intensive calculations based on user input. Sanitization should not only strip dangerous characters but also enforce hard limits on the size and complexity of nested data structures (like design elements).
**Prevention:** Implement rate limiting on sensitive public REST endpoints. Enforce strict type checking and hard limits (e.g., `array_slice` to limit element counts) during data sanitization to prevent computational exhaustion and memory-related DoS.
