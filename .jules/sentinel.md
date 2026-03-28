# Sentinel's Journal - CRITICAL Security Learnings

## 2024-03-28 - [IDOR in Custom T-Shirt Designer]
**Vulnerability:** Insecure Direct Object Reference (IDOR) on the public side.
**Learning:** The 'add_cart_item_data' function in 'public/class-zu-ctsd-public.php' allowed any user to add a custom design to their cart by its ID without verifying ownership. This occurred because the function did not check if the 'user_id' of the design matched the current user's ID.
**Prevention:** Always verify object ownership (e.g., matching 'user_id' with 'get_current_user_id()') before allowing actions that use an ID for data modification or purchase in public-facing endpoints.

## 2024-03-28 - [Missing Authorization Checks in Admin Partials]
**Vulnerability:** Missing authorization checks (capability checks) in admin screen data processing.
**Learning:** Administrative partials in 'admin/partials/' were processing POST requests for orders, pricing, and templates without explicitly checking if the user had the 'manage_zu_tshirt' capability. While the parent admin page checked this, the partials themselves lacked direct protection against unauthorized state changes.
**Prevention:** Implement explicit 'current_user_can()' checks at the start of any logical block that processes data modification (POST requests), following the principle of defense-in-depth.
