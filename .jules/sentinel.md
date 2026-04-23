# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-22 - Resource Exhaustion (DoS) and Dependency Resilience
**Vulnerability:** Processing unbounded design data elements and assuming PHP extensions like 'Fileinfo' are always present.
**Learning:** Maliciously large payloads (e.g., thousands of design elements) can cause CPU/memory exhaustion during sanitization or database insertion. Additionally, relying on specific PHP extensions without existence checks can lead to fatal errors on some environments.
**Prevention:** Enforce strict limits on the number of elements in complex data structures (e.g., 50 elements for designs). Always verify the existence of PHP extensions or functions (like `finfo_open`) before usage and fail securely if missing.
