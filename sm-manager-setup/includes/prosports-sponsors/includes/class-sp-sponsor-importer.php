<?php
/**
 * Sponsor importer - import sponsors into ProSports.
 *
 * @author 		ProSports
 * @category 	Admin
 * @package 	ProSports Sponsors
 * @version     1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( class_exists( 'WP_Importer' ) ) {
	class SP_Sponsor_Importer extends SP_Importer {

		/**
		 * __construct function.
		 *
		 * @access public
		 * @return void
		 */
		public function __construct() {
			$this->import_page = 'prosports_sponsor_csv';
			$this->import_label = __( 'Import Sponsors', 'prosports' );
			$this->columns = array(
				'post_title' => __( 'Name', 'prosports' ),
				'sp_url' => __( 'URL', 'prosports' ),
			);
			parent::__construct();
		}

		/**
		 * import function.
		 *
		 * @access public
		 * @param array $array
		 * @param array $columns
		 * @return void
		 */
		function import( $array = array(), $columns = array( 'post_title' ) ) {
			$this->imported = $this->skipped = 0;

			if ( ! is_array( $array ) || ! sizeof( $array ) ):
				$this->footer();
				die();
			endif;

			$rows = array_chunk( $array, sizeof( $columns ) );

			foreach ( $rows as $row ):

				$row = array_filter( $row );

				if ( empty( $row ) ) continue;

				$meta = array();

				foreach ( $columns as $index => $key ):
					$meta[ $key ] = sp_array_value( $row, $index );
				endforeach;

				$name = sp_array_value( $meta, 'post_title' );

				if ( ! $name ):
					$this->skipped++;
					continue;
				endif;

				$args = array( 'post_type' => 'sp_sponsor', 'post_status' => 'publish', 'post_title' => $name );

				$id = wp_insert_post( $args );

				// Update URL
				update_post_meta( $id, 'sp_url', sp_array_value( $meta, 'sp_url' ) );

				$this->imported++;

			endforeach;

			// Show Result
			echo '<div class="updated settings-error below-h2"><p>
				'.sprintf( __( 'Import complete - imported <strong>%s</strong> sponsors and skipped <strong>%s</strong>.', 'prosports' ), $this->imported, $this->skipped ).'
			</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		function import_end() {
			echo '<p>' . __( 'All done!', 'prosports' ) . ' <a href="' . admin_url('edit.php?post_type=sp_sponsor') . '">' . __( 'View Sponsors', 'prosports' ) . '</a>' . '</p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 *
		 * @access public
		 * @return void
		 */
		function header() {
			echo '<div class="wrap"><h2>' . __( 'Import Sponsors', 'prosports' ) . '</h2>';
		}

		/**
		 * greet function.
		 *
		 * @access public
		 * @return void
		 */
		function greet() {
			echo '<div class="narrow">';
			echo '<p>' . __( 'Hi there! Choose a .csv file to upload, then click "Upload file and import".', 'prosports' ).'</p>';
			echo '<p>' . sprintf( __( 'Sponsors need to be defined with columns in a specific order (2 columns). <a href="%s">Click here to download a sample</a>.', 'prosports' ), SP_SPONSORS_URL . 'dummy-data/sponsors-sample.csv' ) . '</p>';
			wp_import_upload_form( 'admin.php?import=prosports_sponsor_csv&step=1' );
			echo '</div>';
		}
	}
}
