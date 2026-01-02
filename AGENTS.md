# AGENTS.md

## Scope
This file applies to the `lacasa-newsletter-flow.php` plugin and related workflows in this repository.

## Overview
This project contains a WordPress plugin designed to automate user creation upon newsletter subscription via WPForms.

## Key Components
1.  **Plugin File:** `lacasa-newsletter-flow.php`
    *   Hooks into `wpforms_process_complete`.
    *   Target Form ID: `115590`.
    *   Actions: Creates a WP user (Customer/Subscriber) -> Sends "Set Password" email.
2.  **Existing Coupon Plugin:** `welcome10-coupon-plugin.php` (Manages WELCOME10 coupon creation).

## Testing Instructions
*   Since this is a WordPress plugin, full functional testing requires a WordPress environment with WPForms and WooCommerce installed.
*   **Static Analysis:** Ensure code follows WordPress Coding Standards.
*   **Logic Verification:**
    *   Verify Form ID matches `115590`.
    *   Verify `email_exists` check prevents duplicates.
    *   Verify `wp_create_user` logic.
    *   Verify `get_password_reset_key` usage.
    *   Verify `wp_mail` arguments.

## Deployment
*   The plugin should be zipped for installation via the WordPress Admin dashboard.
*   Zip filename: `lacasa-newsletter-flow.zip`.

## Configuration
*   **Sender Email:** `info@lacasa.market`
*   **Redirects:** Handled by WPForms settings (Thank You page).
*   **Password Link:** Standard WP `wp-login.php?action=rp`.
