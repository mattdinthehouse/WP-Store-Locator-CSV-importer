<?php
/**
 * Plugin Name: Store importer
 * Plugin URI: 
 * Description: 
 * Author: 
 * Author URI: 
 * Version: 1.0
 * License: GPL2
 *
 */

// Exit if accessed directly
if(!defined('ABSPATH')) exit;

class StoreImporter {
	
	private static $_instance;

	public static function _instance() {
		if(!self::$_instance) {
			self::$_instance = new StoreImporter();
			self::$_instance->constants();
			self::$_instance->includes();
			self::$_instance->hooks();
		}

		return self::$_instance;
	}


	public $settings = array();

	private function __construct() { }

	private function constants() {
		define('SI_DIR', plugin_dir_path(__FILE__)); // Plugin file path
		define('SI_FILE', plugin_basename(__FILE__)); // Plugin file name
		define('SI_URL', plugins_url('store-importer', 'store-importer.php')); // Plugin URL
	}

	private function includes() {
		require SI_DIR.'/includes/class.importer.php';
		require SI_DIR.'/includes/class.store.php';

		if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
			// Frontend-only
		}
		else {
			// Backend-only
			require SI_DIR.'/includes/class.admin.php';
		}
	}

	private function hooks() {
		if(!is_admin() || (defined('DOING_AJAX') && DOING_AJAX)) {
			// Frontend-only
		}
		else {
			// Backend-only
			SI_Admin::register();
		}
	}

	/*
		Utilities
	*/
	public static function log($data, $message = '') {
		file_put_contents(BBUI_DIR.'/log.txt', date('Y-m-d h:i:s').PHP_EOL.$message.PHP_EOL.var_export($data, true).PHP_EOL, FILE_APPEND | LOCK_EX);
	}

}

/**
 * The main function responsible for returning the singleton StoreImporter instance
 *
 * @since       1.0.0
 * @return      \StoreImporter Singleton StoreImporter instance
 *
 */
function StoreImporter_load() {
	return StoreImporter::_instance();
}
add_action('plugins_loaded', 'StoreImporter_load');

/**
 * Set text field defaults when the plugin is activated for the first time
 *
 * @since       1.0.0
 *
 */
function StoreImporter_activated() {
}
register_activation_hook(__FILE__, 'StoreImporter_activated');

/**
 * Clear cron jobs on deactivation
 *
 * @since       1.0.0
 *
 */
function StoreImporter_deactivated() {
}
register_deactivation_hook(__FILE__, 'StoreImporter_deactivated');
