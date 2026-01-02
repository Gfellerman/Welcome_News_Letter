# WORKFLOW.md

## Project: Lacasa Newsletter Sign-in Flow

### Objective
Automate the creation of a user account when a visitor subscribes to the newsletter via the homepage form.

### Workflow Steps

1.  **User Action:**
    *   Visitor fills out the Newsletter form on the homepage.
    *   Form ID: `115590`.
    *   Fields: Email (and potentially others).

2.  **WPForms Processing:**
    *   The `lacasa-newsletter-flow.php` plugin listens for the `wpforms_process_complete` event.
    *   It checks if the Form ID matches `115590`.

3.  **User Creation Logic:**
    *   The plugin extracts the email address from the form submission.
    *   **Check:** Does a user with this email already exist?
        *   **Yes:** Stop. Do nothing (to prevent spam/errors).
        *   **No:** Proceed to creation.
    *   **Create:** A new WordPress user is created.
        *   **Username:** Email address.
        *   **Role:** 'Customer' (if WooCommerce is active) or 'Subscriber'.
        *   **Password:** Randomly generated (temporary).

4.  **Email Notification:**
    *   The plugin generates a secure "Password Reset Key" for the new user.
    *   It constructs a "Set Password" link pointing to `wp-login.php?action=rp`.
    *   An email is sent to the user via `wp_mail`.
        *   **Sender:** `info@lacasa.market`
        *   **Subject:** Welcome to Lacasa Market - Set your password
        *   **Content:** HTML message with the link.

5.  **User Experience:**
    *   **Immediate:** User sees the "Thank You" page (handled by WPForms redirect settings).
    *   **Email:** User receives the welcome email.
    *   **Account Setup:** User clicks the link -> Sets a password -> Logs in.

### Integration Details
*   **SureMail SMTP:** Handles the actual delivery of the `wp_mail` function.
*   **WooCommerce:** If present, assigns the 'customer' role.
