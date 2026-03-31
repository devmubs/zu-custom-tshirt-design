## 2025-02-14 - [Broken Access Control in Admin Partials]
**Vulnerability:** Admin partials (dashboard components and handlers) lacked explicit capability checks, relying solely on WordPress menu restrictions for security.
**Learning:** In WordPress plugins, administrative pages and actions should implement defense-in-depth by verifying user capabilities before processing sensitive POST requests or performing destructive actions (like template deletion), even if they are only accessible through a restricted menu.
**Prevention:** Always use `current_user_can()` (or a centralized wrapper like `ZU_CTSD_Security::check_capability()`) and terminate execution with `wp_die()` when a capability check fails in an admin-side handler.
