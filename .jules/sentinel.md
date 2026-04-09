# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - Broken Access Control in Administrative Action Handlers
**Vulnerability:** Administrative action handlers in partials (pricing, orders, templates) relied solely on CSRF nonces but lacked explicit authorization checks (e.g., `current_user_can`).
**Learning:** Nonces only ensure the user intended to perform the action (CSRF protection) but do not verify if the user has the required permissions to perform that action (Authorization). In WordPress plugins, action handlers in admin templates often bypass the initial menu capability check if directly accessed or triggered via different hooks.
**Prevention:** Always include an explicit `current_user_can()` check at the beginning of any logic that processes `POST` requests or modifies data in administrative partials, even if nonces are present. Use `wp_die()` to terminate execution for unauthorized requests.
