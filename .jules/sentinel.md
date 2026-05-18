# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - Resource Exhaustion in Design Data Processing
**Vulnerability:** Unauthenticated REST endpoints (like `/calculate-price`) accepting complex JSON structures (design elements) were processed without volume limits, allowing for Denial of Service (DoS) attacks via resource exhaustion.
**Learning:** Sanitization functions must not only clean values but also enforce hard structural limits (e.g., maximum array length) when processing complex nested data that can be submitted by unauthenticated users.
**Prevention:** Implement hard caps (e.g., `array_slice`) on input arrays early in the sanitization pipeline and ensure these limits are applied at the entry point of all public-facing endpoints.
