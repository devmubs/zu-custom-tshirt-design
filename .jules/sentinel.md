## 2025-05-15 - [Defense-in-Depth for Admin Partials]
**Vulnerability:** Broken Access Control in administrative partials.
**Learning:** Administrative partials (templates) that process POST requests for sensitive data (pricing, orders, templates) relied on WordPress menu-level capability checks and nonces, but lacked explicit per-request authorization in the processing logic.
**Prevention:** Always perform an explicit capability check (e.g., `current_user_can('manage_zu_tshirt')`) and `wp_die()` if unauthorized at the start of any POST request handling in administrative partials, even if the page itself is restricted.
