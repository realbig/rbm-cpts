<?php
/**
 * Plugin Name: RBM Custom Post Types
 * Description: Creates custom post types.
 * Version: 1.1.3
 * Author: Real Big Marketing
 * Author URI: http://realbigmarketing.com
 * GitHub Plugin URI: realbig/rbm-cpts
 * Release Asset: true
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'RBM_CPTS' ) ) {

	// Define plugin constants
	define( 'RBM_CPTS_VERSION', '1.1.3' );
	define( 'RBM_CPTS_DIR', plugin_dir_path( __FILE__ ) );
	define( 'RBM_CPTS_URL', plugins_url( '', __FILE__ ) );

	/**
	 * Class RBM_CPTS
	 *
	 * Initiates the plugin.
	 *
	 * @since   0.1.0
	 *
	 * @package RBM_CPTS
	 */
	class RBM_CPTS {

		/**
		 * Custom Post Types.
		 *
		 * @since 0.1.0
		 *
		 * @var array
		 */
		public $cpts;

		/**
		 * Post to Post relationships.
		 *
		 * @since 0.1.0
		 *
		 * @var RBM_CPTS_P2P
		 */
		public $p2ps;

		/**
		 * RBM Field Helpers instance.
		 *
		 * @since 1.1.0
		 *
		 * @var RBM_FieldHelpers
		 */
		public $fieldhelpers;

		private function __clone() {
		}

		private function __wakeup() {
		}

		/**
		 * Returns the *Singleton* instance of this class.
		 *
		 * @since     0.1.0
		 *
		 * @staticvar Singleton $instance The *Singleton* instances of this class.
		 *
		 * @return RBM_CPTS Outline The *Singleton* instance.
		 */
		public static function getInstance() {

			static $instance = null;

			if ( null === $instance ) {
				$instance = new static();
			}

			return $instance;
		}

		/**
		 * Initializes the plugin.
		 *
		 * @since 0.1.0
		 */
		protected function __construct() {

			$this->setup_fieldhelpers();
			$this->require_necessities();
			
		}

		/**
		 * Requires necessary base files.
		 *
		 * @since 0.1.0
		 */
		public function require_necessities() {

			require_once __DIR__ . '/core/class-rbm-cpt.php';
			require_once __DIR__ . '/core/class-rbm-cpts-p2p.php';

			$this->p2ps = new RBM_CPTS_P2P();
		}

		/**
		 * Initializes Field Helpers.
		 *
		 * @since 1.2.0
		 * @access private
		 */
		private function setup_fieldhelpers() {

			require_once __DIR__ . '/core/library/rbm-field-helpers/rbm-field-helpers.php';

			$this->field_helpers = new RBM_FieldHelpers( array(
				'ID'   => 'rbm_cpts',
				'l10n' => array(
					'field_table'    => array(
						'delete_row'    => __( 'Delete Row', 'rbm-cpts' ),
						'delete_column' => __( 'Delete Column', 'rbm-cpts' ),
					),
					'field_select'   => array(
						'no_options'       => __( 'No select options.', 'rbm-cpts' ),
						'error_loading'    => __( 'The results could not be loaded', 'rbm-cpts' ),
						/* translators: %d is number of characters over input limit */
						'input_too_long'   => __( 'Please delete %d character(s)', 'rbm-cpts' ),
						/* translators: %d is number of characters under input limit */
						'input_too_short'  => __( 'Please enter %d or more characters', 'rbm-cpts' ),
						'loading_more'     => __( 'Loading more results...', 'rbm-cpts' ),
						/* translators: %d is maximum number items selectable */
						'maximum_selected' => __( 'You can only select %d item(s)', 'rbm-cpts' ),
						'no_results'       => __( 'No results found', 'rbm-cpts' ),
						'searching'        => __( 'Searching...', 'rbm-cpts' ),
					),
					'field_repeater' => array(
						'collapsable_title' => __( 'New Row', 'rbm-cpts' ),
						'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'rbm-cpts' ),
						'delete_item'       => __( 'Delete', 'rbm-cpts' ),
						'add_item'          => __( 'Add', 'rbm-cpts' ),
					),
					'field_media'    => array(
						'button_text'        => __( 'Upload / Choose Media', 'rbm-cpts' ),
						'button_remove_text' => __( 'Remove Media', 'rbm-cpts' ),
						'window_title'       => __( 'Choose Media', 'rbm-cpts' ),
					),
					'field_checkbox' => array(
						'no_options_text' => __( 'No options available.', 'rbm-cpts' ),
					),
				),
			) );
		}
	}

	add_action( 'plugins_loaded', 'rbm_cpt_init' );

	function rbm_cpt_init() {

		require_once __DIR__ . '/core/rbm-cpts-functions.php';
		RBM_CPTS();
		
	}
	
}