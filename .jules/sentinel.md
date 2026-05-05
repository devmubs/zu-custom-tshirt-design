# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-15 - Security Validation: Fail Secure vs Fail Open
**Vulnerability:** A modification to MIME type validation introduced a "Fail Open" logic where failing to detect the MIME type (e.g., due to missing PHP extensions) would skip the security check and allow the file.
**Learning:** Security checks must be exhaustive and conservative. If a validation step cannot be performed due to environment limitations, the process must fail by default ("Fail Secure") rather than assuming the input is safe ("Fail Open").
**Prevention:** Use strict conditions (e.g., `if (!$detected || !in_array($detected, $allowed))` instead of `if ($detected && !in_array($detected, $allowed))`) to ensure that any deviation from the expected successful validation results in rejection.
