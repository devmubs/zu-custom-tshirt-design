# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - SQL Injection Prevention in ORDER BY Clauses
**Vulnerability:** Dynamic `ORDER BY` clauses in `ZU_CTSD_Database::get_designs_by_user` were using a non-existent function `sanitize_sql_orderby`, leading to both a fatal error and potential SQL injection since `$wpdb->prepare` does not handle SQL identifiers (column names) or keywords (ASC/DESC).
**Learning:** In WordPress, dynamic SQL identifiers must be strictly whitelisted. While a centralized security class exists, using it for simple whitelisting can introduce unnecessary dependencies and potential fatal errors if the class is not loaded or correctly referenced.
**Prevention:** Use strict inline whitelisting with `in_array()` for column names and sort directions directly before the query. Always provide safe default values (e.g., 'created_at' and 'DESC') if the input does not match the whitelist.
