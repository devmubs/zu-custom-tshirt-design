# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-20 - Unbounded Array Processing and DoS Mitigation
**Vulnerability:** The `calculate_price` REST API endpoint and design saving logic processed an unbounded number of design elements, allowing for Denial of Service (DoS) attacks via resource exhaustion.
**Learning:** Publicly accessible endpoints (or those with low privilege requirements) that iterate over user-provided arrays are highly susceptible to DoS if the array size is not strictly capped. This is especially critical when the processing involves complex sanitization or database operations for each element.
**Prevention:** Centralize sanitization logic for complex data structures and enforce hard limits on the number of processable elements (e.g., using `array_slice`). Ensure these limits are applied at the earliest possible stage in the request lifecycle.
