<?php
/**
 * Plugin Name: Pohoda WooCommerce Integration
 * Description: Synchronizes data between Pohoda E1 SQL and WooCommerce
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: pohoda-woo-sync
 */

if (!defined('ABSPATH')) {
    exit;
}

class PohodaWooSync {
    private static $instance = null;
    private $db_connector;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->define_constants();
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    private function define_constants() {
        define('POHODA_WOO_PATH', plugin_dir_path(__FILE__));
        define('POHODA_WOO_URL', plugin_dir_url(__FILE__));
        define('POHODA_WOO_VERSION', '1.0.0');
    }
    
    private function load_dependencies() {
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-db-connector.php';
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-product-sync.php';
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-customer-sync.php';
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-order-sync.php';
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-settings.php';
        require_once POHODA_WOO_PATH . 'includes/class-pohoda-scheduler.php';
    }
    
    private function init_hooks() {
        add_action('plugins_loaded', array($this, 'init_plugin'));
    }
    
    public function init_plugin() {
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'woocommerce_missing_notice'));
            return;
        }
        
        $this->init_classes();
    }
    
    private function init_classes() {
        new Pohoda_Settings();
        new Pohoda_Scheduler();
    }
    
    public function woocommerce_missing_notice() {
        echo '<div class="error"><p>' . 
             esc_html__('Pohoda WooCommerce Integration requires WooCommerce to be installed and activated.', 'pohoda-woo-sync') . 
             '</p></div>';
    }
}

// Initialize the plugin
function pohoda_woo_sync_init() {
    return PohodaWooSync::get_instance();
}

add_action('plugins_loaded', 'pohoda_woo_sync_init');