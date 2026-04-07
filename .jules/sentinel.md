# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-22 - Authorization in Administrative Partials
**Vulnerability:** Administrative partials processing POST requests were only verifying nonces, not user capabilities.
**Learning:** In WordPress, nonces only verify intent and prevent CSRF, but do not provide authorization. Without explicit capability checks, any logged-in user (even with low privileges) could potentially perform administrative actions if they obtain or forge a valid nonce.
**Prevention:** Always include explicit `current_user_can` checks (preferably via a centralized helper like `ZU_CTSD_Security::check_capability()`) at the entry point of any logic that performs state-changing operations, especially in admin-side POST handlers.
