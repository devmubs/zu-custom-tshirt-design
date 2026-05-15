# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - DoS Mitigation for Complex Design Objects
**Vulnerability:** Deeply nested or high-count design elements in JSON payloads could be used to trigger resource exhaustion (CPU/Memory) during server-side sanitization and processing loops.
**Learning:** Even with individual field sanitization, the total quantity of elements must be capped. When sanitizing such collections, it is critical to preserve associative keys if the frontend or subsequent database logic (like `layer_order`) relies on the original sequence or identifiers.
**Prevention:** Enforce a hard limit on the number of elements (e.g., 50) using `array_slice` with `preserve_keys=true` and use `foreach ($elements as $key => $value)` to maintain the original index structure while sanitizing.
