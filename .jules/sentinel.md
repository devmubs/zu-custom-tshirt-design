# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-22 - DoS Prevention in Design Data Sanitization
**Vulnerability:** Pricing and design saving methods were vulnerable to resource exhaustion (DoS) if provided with an excessively large number of design elements in the `design_data` array.
**Learning:** Sanitization logic should not only clean individual fields but also enforce structural constraints like array length limits when processing complex hierarchical data from user input.
**Prevention:** Implement a hard limit (e.g., 50 elements) using `array_slice` with `preserve_keys=true` in centralized sanitization methods like `ZU_CTSD_Security::sanitize_design_data` to ensure predictable resource consumption during database insertion and pricing calculations.
