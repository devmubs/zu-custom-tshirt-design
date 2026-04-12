# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2026-04-12 - Dynamic SQL Identifier Whitelisting
**Vulnerability:** Use of a non-existent `sanitize_sql_orderby` function and lack of strict whitelisting for dynamic `ORDER BY` clauses in database queries.
**Learning:** In WordPress, `$wpdb->prepare` does not support parameterization of SQL identifiers (like table/column names) or keywords (like `ASC`/`DESC`). Relying on generic sanitization functions or non-existent ones can lead to SQL injection or fatal errors.
**Prevention:** Implement strict inline whitelisting for all dynamic SQL identifiers and keywords. Use `in_array()` with strict type checking to validate input against a hardcoded list of allowed values, and provide safe default values for fallbacks.
