## 2024-05-23 - [SQL Injection in ORDER BY Clause]
**Vulnerability:** Unsanitized user input was being passed directly into an `ORDER BY` clause in `ZU_CTSD_Database::get_designs_by_user` via a non-existent `sanitize_sql_orderby` function.
**Learning:** WordPress `$wpdb->prepare()` cannot sanitize SQL identifiers or keywords like `ASC/DESC`. Using non-existent functions for security is a fatal risk.
**Prevention:** Implement strict whitelisting for both column names and sort directions before interpolating them into SQL strings.

## 2024-05-23 - [MIME Type Verification Fail-Open]
**Vulnerability:** MIME type verification was being skipped if the `fileinfo` PHP extension was missing, potentially allowing malicious file uploads.
**Learning:** Security checks that depend on environment features must "fail secure" by explicitly rejecting the operation if the verification cannot be performed.
**Prevention:** Check for the existence of required security functions (e.g., `finfo_open`) and return a validation error if they are unavailable.

## 2024-05-23 - [Admin POST Handler Authorization]
**Vulnerability:** Administrative partials were relying solely on nonce verification without explicit capability checks, creating a defense-in-depth gap.
**Learning:** While WordPress core handles many permission checks, custom POST handlers in admin templates require manual `current_user_can()` validation to ensure only authorized users can perform sensitive actions.
**Prevention:** Always add a `current_user_can('manage_zu_tshirt')` check at the beginning of administrative POST request handlers.
