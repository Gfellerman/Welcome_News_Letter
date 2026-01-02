<?php
/**
 * Plugin Name: Lacasa Newsletter User Creator
 * Description: Creates a user and sends a password set link when WPForms #115590 is submitted.
 * Version: 1.0.0
 * Author: Lacasa Market
 * Author URI: https://lacasa.market
 * Text Domain: lacasa-newsletter
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Class
 */
class Lacasa_Newsletter_User_Creator {

    const FORM_ID = 115590;

    /**
     * Constructor
     */
    public function __construct() {
        // Hook into WPForms processing
        add_action('wpforms_process_complete', array($this, 'process_entry'), 10, 4);
    }

    /**
     * Process the WPForms entry
     *
     * @param array $fields    The sanitized field data.
     * @param array $entry     The original entry data.
     * @param array $form_data The form data.
     * @param int   $entry_id  The entry ID.
     */
    public function process_entry($fields, $entry, $form_data, $entry_id) {
        // Check if it's the correct form
        if (absint($form_data['id']) !== self::FORM_ID) {
            return;
        }

        // Find email field
        $email = '';
        foreach ($fields as $field) {
            // Check based on field type 'email'
            // WPForms fields usually have 'type' or 'value' and 'name'.
            // For sanitized fields, it's usually $field['value'] and $field['type'] might not be there in all versions,
            // but we can try to guess or iterate.
            // A reliable way if we don't know the field ID is to look for a value that looks like an email.
            // Or assume the field type is available.

            if (isset($field['type']) && $field['type'] === 'email') {
                $email = sanitize_email($field['value']);
                break;
            }

            // Fallback: Check if value is a valid email
            if (is_email($field['value'])) {
                $email = sanitize_email($field['value']);
                // We don't break immediately just in case there are multiple, but usually the first one is the main one.
                // However, type check is safer.
                break;
            }
        }

        if (empty($email)) {
            // Try looking into raw entry data if fields didn't work (fallback)
            // But fields should work.
            return;
        }

        // Check if user exists
        if (email_exists($email)) {
            // User exists, do nothing
            return;
        }

        // Create new user
        $this->create_new_user($email);
    }

    /**
     * Create a new user and send welcome email
     *
     * @param string $email User email
     */
    private function create_new_user($email) {
        $username = $email; // Use email as username
        $password = wp_generate_password();

        // Create user
        $user_id = wp_create_user($username, $password, $email);

        if (is_wp_error($user_id)) {
            error_log('Lacasa Newsletter: Failed to create user ' . $email . ' - ' . $user_id->get_error_message());
            return;
        }

        // Set role to Subscriber (default) or Customer if WooCommerce is active
        $user = get_user_by('id', $user_id);
        if ($user) {
            // Check if WooCommerce is active to assign 'customer' role
            if (class_exists('WooCommerce')) {
                 $user->set_role('customer');
            } else {
                 $user->set_role('subscriber');
            }
        }

        // Generate Password Reset Key
        $key = get_password_reset_key($user);
        if (is_wp_error($key)) {
            error_log('Lacasa Newsletter: Failed to generate reset key for ' . $email);
            return;
        }

        // Construct Link
        $login_url = site_url('wp-login.php');
        $action = 'rp';
        $reset_link = add_query_arg(array(
            'action' => $action,
            'key' => $key,
            'login' => rawurlencode($user->user_login)
        ), $login_url);

        // Send Email
        $this->send_welcome_email($email, $reset_link);
    }

    /**
     * Send Welcome Email
     *
     * @param string $email
     * @param string $reset_link
     */
    private function send_welcome_email($email, $reset_link) {
        $to = $email;
        $subject = __('Welcome to Lacasa Market - Set your password', 'lacasa-newsletter');
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $headers[] = 'From: Lacasa Market <info@lacasa.market>';

        // HTML Message
        $message = '<!DOCTYPE html>';
        $message .= '<html><body>';
        $message .= '<h2>' . __('Welcome to Lacasa Market!', 'lacasa-newsletter') . '</h2>';
        $message .= '<p>' . __('Thank you for subscribing to our newsletter.', 'lacasa-newsletter') . '</p>';
        $message .= '<p>' . __('To complete your account setup and set your password, please click the link below:', 'lacasa-newsletter') . '</p>';
        $message .= '<p><a href="' . esc_url($reset_link) . '">' . __('Set My Password', 'lacasa-newsletter') . '</a></p>';
        $message .= '<p>' . __('If you did not request this, please ignore this email.', 'lacasa-newsletter') . '</p>';
        $message .= '<p>' . __('Best regards,', 'lacasa-newsletter') . '<br>Lacasa Market Team</p>';
        $message .= '</body></html>';

        wp_mail($to, $subject, $message, $headers);
    }
}

// Initialize
new Lacasa_Newsletter_User_Creator();
