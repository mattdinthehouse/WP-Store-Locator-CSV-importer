<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SI_Importer {

	public function __construct() {
	}

	public function do_import($path) {
		@ini_set('max_execution_time', 1800);
		set_time_limit(1800);

		$results = array(
			'errors' => array(),
			'ids' => array(),
		);

		$csv = fopen($path, 'r');
		if(!$csv) {
			throw new RuntimeException('Unable to open CSV file');
		}

		$headers = fgetcsv($csv);
		$headers = $this->sanitize_csv_headers($headers);

		while(($row = fgetcsv($csv)) !== false) {
			$row = $this->sanitize_csv_row($row);

			try {
				$data = array_combine($headers, $row);
				
				$results['ids'][] = $this->import_record($data);
			}
			catch(Exception $e) {
				$results['errors'][] = 'Error occured: '.$e->getMessage();
			}
		}

		return $results;
	}

	private function sanitize_csv_headers($headers) {
		array_walk($headers, function(&$h) {
			// Strip non-printable characters
			$h = preg_replace('/[[:^print:]]/', '', $h);

			// Convert underscores to spaces for consistency
			$h = str_replace('_', ' ', $h);
		});

		$headers = array_map('trim', $headers);
		$headers = array_map('sanitize_title', $headers);

		return $headers;
	}

	private function sanitize_csv_row($row) {
		$row = array_map('trim', $row);

		return $row;
	}

	private function import_record($data) {
		$record = new SI_Record($data);

		// Comment out these calls as needed if the import's being re-run and you don't want to overwrite existing data
		$record->populate_record();
		$record->populate_location();
		$record->populate_additional_info();

		return $record->get_record_id();
	}

}