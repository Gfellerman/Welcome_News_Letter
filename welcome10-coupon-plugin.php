<?php
/**
 * Plugin Name: WELCOME10 Coupon Creator
 * Description: Automatically creates and manages the WELCOME10 coupon for newsletter subscribers
 * Version: 1.0.0
 * Author: Lacasa Market
 * Author URI: https://lacasa.market
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: welcome10-coupon
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Requires Plugins: woocommerce
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WELCOME10_COUPON_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WELCOME10_COUPON_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WELCOME10_COUPON_VERSION', '1.0.0');
define('WELCOME10_COUPON_CODE', 'WELCOME10');

/**
 * Main Plugin Class
 */
class Welcome10_Coupon_Creator {
    
    /**
     * Constructor
     */
    public function __construct() {
        // Hook into plugin activation
        register_activation_hook(__FILE__, array($this, 'activate_plugin'));
        
        // Hook into plugin deactivation
        register_deactivation_hook(__FILE__, array($this, 'deactivate_plugin'));
        
        // Initialize plugin on WordPress load
        add_action('plugins_loaded', array($this, 'init'));
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Add admin notices
        add_action('admin_notices', array($this, 'admin_notices'));
    }
    
    /**
     * Plugin Activation Hook
     */
    public function activate_plugin() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die('This plugin requires WooCommerce to be installed and activated.');
        }
        
        // Create the coupon
        $this->create_welcome10_coupon();
        
        // Set plugin activated flag
        update_option('welcome10_coupon_activated', true);
        update_option('welcome10_coupon_created_date', current_time('mysql'));
    }
    
    /**
     * Plugin Deactivation Hook
     */
    public function deactivate_plugin() {
        // Optional: You can delete the coupon on deactivation
        // Uncomment the line below if you want to remove the coupon when plugin is deactivated
        // $this->delete_welcome10_coupon();
    }
    
    /**
     * Initialize Plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        // Create coupon if it doesn't exist
        $this->create_welcome10_coupon();
        
        // Load text domain for translations
        load_plugin_textdomain('welcome10-coupon', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Create WELCOME10 Coupon
     * 
     * @return int|bool Coupon ID on success, false on failure
     */
    public function create_welcome10_coupon() {
        // Check if coupon already exists
        $existing_coupon = new WC_Coupon(WELCOME10_COUPON_CODE);
        
        if ($existing_coupon->get_id() !== 0) {
            // Coupon already exists, return its ID
            return $existing_coupon->get_id();
        }
        
        try {
            // Create new coupon object
            $coupon = new WC_Coupon();
            
            // Set coupon code
            $coupon->set_code(WELCOME10_COUPON_CODE);
            
            // Set discount type (percentage)
            $coupon->set_discount_type('percent');
            
            // Set discount amount (10%)
            $coupon->set_amount(10);
            
            // Set description
            $coupon->set_description(__('10% welcome discount for first-time newsletter subscribers', 'welcome10-coupon'));
            
            // Set usage restrictions
            $coupon->set_usage_limit(1);                    // Total uses: 1
            $coupon->set_usage_limit_per_user(1);          // Uses per customer: 1
            
            // Set expiry date (1 year from now)
            $expiry_date = date('Y-m-d', strtotime('+1 year'));
            $coupon->set_date_expires($expiry_date);
            
            // Additional settings
            $coupon->set_individual_use(false);            // Can combine with other coupons
            $coupon->set_exclude_sale_items(false);        // Apply to sale items
            $coupon->set_minimum_amount(0);                // No minimum spend
            $coupon->set_maximum_amount(0);                // No maximum spend
            
            // Save the coupon
            $coupon->save();
            
            // Store creation details
            update_option('welcome10_coupon_id', $coupon->get_id());
            update_option('welcome10_coupon_created', true);
            
            return $coupon->get_id();
            
        } catch (Exception $e) {
            // Log error
            error_log('WELCOME10 Coupon Creation Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete WELCOME10 Coupon
     * 
     * @return bool True on success, false on failure
     */
    public function delete_welcome10_coupon() {
        $coupon = new WC_Coupon(WELCOME10_COUPON_CODE);
        
        if ($coupon->get_id() !== 0) {
            wp_delete_post($coupon->get_id(), true);
            delete_option('welcome10_coupon_id');
            delete_option('welcome10_coupon_created');
            return true;
        }
        
        return false;
    }
    
    /**
     * Get Coupon Details
     * 
     * @return array Coupon details
     */
    public function get_coupon_details() {
        $coupon = new WC_Coupon(WELCOME10_COUPON_CODE);
        
        if ($coupon->get_id() === 0) {
            return array('exists' => false);
        }
        
        return array(
            'exists' => true,
            'id' => $coupon->get_id(),
            'code' => $coupon->get_code(),
            'discount_type' => $coupon->get_discount_type(),
            'amount' => $coupon->get_amount(),
            'description' => $coupon->get_description(),
            'usage_limit' => $coupon->get_usage_limit(),
            'usage_limit_per_user' => $coupon->get_usage_limit_per_user(),
            'used_by' => $coupon->get_used_by(),
            'date_expires' => $coupon->get_date_expires(),
            'individual_use' => $coupon->get_individual_use(),
            'exclude_sale_items' => $coupon->get_exclude_sale_items(),
            'minimum_amount' => $coupon->get_minimum_amount(),
            'maximum_amount' => $coupon->get_maximum_amount(),
        );
    }
    
    /**
     * Add Admin Menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __('WELCOME10 Coupon', 'welcome10-coupon'),
            __('WELCOME10 Coupon', 'welcome10-coupon'),
            'manage_woocommerce',
            'welcome10-coupon-settings',
            array($this, 'render_admin_page')
        );
    }
    
    /**
     * Render Admin Settings Page
     */
    public function render_admin_page() {
        // Check user capabilities
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'welcome10-coupon'));
        }
        
        // Get coupon details
        $coupon_details = $this->get_coupon_details();
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(__('WELCOME10 Coupon Manager', 'welcome10-coupon')); ?></h1>
            
            <div class="notice notice-info">
                <p><?php echo esc_html(__('This page displays information about the WELCOME10 coupon used for newsletter subscribers.', 'welcome10-coupon')); ?></p>
            </div>
            
            <?php if ($coupon_details['exists']): ?>
                <h2><?php echo esc_html(__('Coupon Status: Active', 'welcome10-coupon')); ?></h2>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Coupon Code', 'welcome10-coupon')); ?></label></th>
                        <td><strong><?php echo esc_html($coupon_details['code']); ?></strong></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Discount Type', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['discount_type']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Discount Amount', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['amount']); ?>%</td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Description', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['description']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Total Usage Limit', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['usage_limit']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Usage Per User', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['usage_limit_per_user']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Times Used', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html(count($coupon_details['used_by'])); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Expiry Date', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo $coupon_details['date_expires'] ? esc_html($coupon_details['date_expires']->date('Y-m-d')) : __('No expiration', 'welcome10-coupon'); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Minimum Amount', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['minimum_amount']); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php echo esc_html(__('Maximum Amount', 'welcome10-coupon')); ?></label></th>
                        <td><?php echo esc_html($coupon_details['maximum_amount']); ?></td>
                    </tr>
                </table>
                
                <p>
                    <a href="<?php echo esc_url(admin_url('post.php?post=' . $coupon_details['id'] . '&action=edit')); ?>" class="button button-primary">
                        <?php echo esc_html(__('Edit Coupon', 'welcome10-coupon')); ?>
                    </a>
                </p>
                
            <?php else: ?>
                <h2><?php echo esc_html(__('Coupon Status: Not Found', 'welcome10-coupon')); ?></h2>
                
                <div class="notice notice-warning">
                    <p><?php echo esc_html(__('The WELCOME10 coupon does not exist. Click the button below to create it.', 'welcome10-coupon')); ?></p>
                </div>
                
                <form method="post">
                    <?php wp_nonce_field('welcome10_create_coupon'); ?>
                    <input type="hidden" name="action" value="welcome10_create_coupon">
                    <button type="submit" class="button button-primary">
                        <?php echo esc_html(__('Create WELCOME10 Coupon', 'welcome10-coupon')); ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Display Admin Notices
     */
    public function admin_notices() {
        // Check if coupon was just created
        if (get_transient('welcome10_coupon_created_notice')) {
            delete_transient('welcome10_coupon_created_notice');
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo esc_html(__('WELCOME10 coupon has been successfully created!', 'welcome10-coupon')); ?></p>
            </div>
            <?php
        }
    }
}

// Initialize the plugin
new Welcome10_Coupon_Creator();

/**
 * Helper Function: Get WELCOME10 Coupon Code
 * 
 * Usage: $code = welcome10_get_coupon_code();
 * 
 * @return string Coupon code
 */
function welcome10_get_coupon_code() {
    return WELCOME10_COUPON_CODE;
}

/**
 * Helper Function: Get WELCOME10 Coupon Details
 * 
 * Usage: $details = welcome10_get_coupon_details();
 * 
 * @return array Coupon details
 */
function welcome10_get_coupon_details() {
    $plugin = new Welcome10_Coupon_Creator();
    return $plugin->get_coupon_details();
}

/**
 * Helper Function: Check if WELCOME10 Coupon Exists
 * 
 * Usage: if (welcome10_coupon_exists()) { ... }
 * 
 * @return bool True if coupon exists, false otherwise
 */
function welcome10_coupon_exists() {
    $coupon = new WC_Coupon(WELCOME10_COUPON_CODE);
    return $coupon->get_id() !== 0;
}
