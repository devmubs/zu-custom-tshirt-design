# Sentinel's Journal - Critical Security Learnings

## 2025-05-15 - Image Data Validation and PHP Code Detection
**Vulnerability:** User-provided base64 image data (e.g., design previews) was being written to the filesystem without adequate content validation, relying only on a fixed file extension.
**Learning:** Even when a safe extension like `.png` is enforced, raw buffers can contain malicious PHP code or have mismatched MIME types that could exploit server vulnerabilities or lead to file upload bypasses if combined with other issues.
**Prevention:** Always validate raw image buffers using `finfo_buffer` (with `class_exists('finfo')` checks for environment robustness) and perform regex-based detection of PHP tags (`<?php`, etc.) within the content before writing to disk. Explicitly handle file read failures (e.g., from `file_get_contents`) as validation errors rather than bypassing security logic.

## 2025-05-16 - DoS Protection for Unauthenticated API Endpoints
**Vulnerability:** Unauthenticated REST API endpoints (like `/calculate-price`) were vulnerable to Denial of Service (DoS) through rate-limit bypass and resource exhaustion via large input arrays.
**Learning:** Even "read-only" or calculation endpoints can be weaponized if they lack volume constraints. The `calculate_price` logic involved iterating over design elements, which could be exploited by sending thousands of elements.
**Prevention:** Implement mandatory rate limiting (`check_rate_limit`) on all unauthenticated endpoints. Enforce strict limits on the number of elements in input arrays (e.g., `array_slice($elements, 0, 50)`) and ensure type safety (`is_array`) before processing complex nested data.
