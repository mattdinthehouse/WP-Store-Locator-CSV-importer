<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SI_Admin {

	public static function register() {
		add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_scripts'));
	}

	public static function add_admin_menu() {
		add_submenu_page('tools.php', 'Store Importer', 'Store Importer', 'manage_options', 'store_importer', array(__CLASS__, 'display') );
	}

	public static function enqueue_scripts() {
	}

	public static function display() {
		if(isset($_POST['si-import'])) {
			$csv_path = (isset($_FILES['si-csv']) && $_FILES['si-csv']['error'] == UPLOAD_ERR_OK ? $_FILES['si-csv']['tmp_name'] : '');

			$importer = new SI_Importer();
			$results = $importer->do_import($csv_path);

			if(!empty($results['errors'])) {
				foreach($results['errors'] as $e) {
					print '<div class="error"><p>'.$e.'</p></div>';
				}
			}

			print '<div class="updated"><p>Import completed, '.count($results['ids']).' records were imported</p></div>';
		}

		require SI_DIR.'/assets/partials/admin.php';
	}

}