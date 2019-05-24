<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SI_Record {

	private $data;
	private $record_id;

	public function __construct($data) {
		$this->data = $data;

		// Load the user or create them if they don't exist
		$this->record_id = $this->find_or_create();

		// Save import data so it's not lost
		update_post_meta($this->record_id, '_import_timestamp', time());
		update_post_meta($this->record_id, '_import_data', $this->data);
	}

	private function find_or_create() {
		$record_ids = get_posts(array(
			'fields'         => 'ids',
			'posts_per_page' => -1,
			'post_type'      => 'wpsl_stores',
			'post_status'    => 'any',
			'meta_key'       => 'account_id',
			'meta_value'     => $this->data['account-id'],
		));

		if(count($record_ids) > 1) {
			throw new Exception('More than one record found for: '.$this->data['account-id']);
		}

		$record_id = (count($record_ids) == 1 ? current($record_ids) : false);
		if(!$record_id) {
			$record_id = $this->create_record();
		}

		if(!$record_id) {
			throw new Exception('Unable to load record: '.$this->data['account-id']);
		}

		return $record_id;
	}

	private function create_record() {
		$record_id = wp_insert_post(array(
			'post_type'  => 'wpsl_stores',
			'post_title' => $this->data['account-name'],
			'meta_input' => array(
				'account_id' => $this->data['account-id'],
			),
		));

		if(!$record_id || is_wp_error($record_id)) {
			throw new Exception('Unable to create new record ('.$this->data['account-id'].')'.(is_wp_error($record_id) ? ': '.$record_id->get_error_message() : ''));
		}

		return $record_id;
	}

	public function get_record_id() {
		return $this->record_id;
	}

	public function populate_record() {
		wp_update_post(array(
			'ID'           => $this->record_id,
			'post_title'   => $this->data['account-name'],
			'post_status'  => 'publish',
		));
	}

	public function populate_location() {
		global $wpsl_admin;

		$data = array(
			'address' => $this->data['shipping-street'],
			'city'    => $this->data['shipping-city'],
			'state'   => $this->data['shipping-state-province'],
			'country' => 'Australia',
		);

		$needs_geocode = false;
		foreach($data as $key => $value) {
			if($value != get_post_meta($this->record_id, 'wpsl_'.$key, true)) {
				$needs_geocode = true;
			}

			update_post_meta($this->record_id, 'wpsl_'.$key, $value);
		}

		if(!get_post_meta($this->record_id, 'wpsl_lat', true) || !get_post_meta($this->record_id, 'wpsl_lng', true)) {
			$needs_geocode = true;
		}

		if($needs_geocode) {
			$data['lat'] = '';
			$data['lng'] = '';

			$wpsl_admin->geocode->check_geocode_data($this->record_id, $data);
		}
	}

	public function populate_additional_info() {
		update_post_meta($this->record_id, 'wpsl_phone', $this->data['phone']);
		update_post_meta($this->record_id, 'wpsl_fax',   $this->data['fax']);
		update_post_meta($this->record_id, 'wpsl_url',   $this->data['website']);
	}

}