## 2024-05-15 - Missing Administrative Capability Checks in Admin Partials
**Vulnerability:** Administrative partials used for processing POST requests relied solely on nonce verification without explicit capability checks.
**Learning:** While these files are typically loaded via an admin controller that checks capabilities, direct access or indirect inclusion might bypass these checks if not carefully managed. Nonces protect against CSRF but don't inherently verify the user's authority to perform the action.
**Prevention:** Always implement an explicit `current_user_can()` (or a wrapper like `ZU_CTSD_Security::check_capability()`) check at the point of request processing, especially in files that are primarily for rendering but also handle logic.
