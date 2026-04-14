# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before wiring to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-22 - Missing Authorization in Administrative Partials
**Vulnerability:** Several administrative partials (`pricing.php`, `orders.php`, `template-edit.php`) processed sensitive `POST` requests without explicit `current_user_can()` checks, relying solely on menu-level access and nonces.
**Learning:** Nonces provide CSRF protection but do not substitute for authorization. Menu-level access control in WordPress may not be sufficient if partials are reused or if the request handling logic is reached by an authenticated user with insufficient privileges.
**Prevention:** Always enforce explicit capability checks using `current_user_can()` at the beginning of any logic that handles `POST` data or administrative actions. Use `wp_die()` with a localized message to terminate execution for unauthorized users.
