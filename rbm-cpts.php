<?php
/**
 * Plugin Name: RBM Custom Post Types
 * Description: Creates custom post types.
 * Version: 1.2.1
 * Author: Real Big Marketing
 * Author URI: http://realbigmarketing.com
 * GitHub Plugin URI: realbig/rbm-cpts
 * GitHub Branch: develop
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'RBM_CPTS' ) ) {

	// Define plugin constants
	define( 'RBM_CPTS_VERSION', '1.2.1' );
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

		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		1.2.1
		 */
		public $plugin_data;

		/**
		 * @var			array $admin_errors Stores all our Admin Errors to fire at once
		 * @since		1.2.1
		 */
		private $admin_errors;

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

			$this->setup_constants();
			$this->load_textdomain();

			if ( ! class_exists( 'RBM_FieldHelpers' ) ) {

				$this->admin_errors[] = sprintf( __( 'To use the %s Plugin, %s must be installed!', 'rbm-cpts' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//github.com/realbig/rbm-field-helpers-wrapper/" target="_blank">' . __( 'RBM Field Helpers', 'rbm-cpts' ) . '</a>' );
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_errors' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_errors' ) );
				}
				
				return false;

			}

			$this->setup_fieldhelpers();
			$this->require_necessities();
			
		}

		/**
		 * Setup plugin constants
		 *
		 * @access	  private
		 * @since	  1.2.1
		 * @return	  void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'RBM_CPTS_VER' ) ) {
				// Plugin version
				define( 'RBM_CPTS_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'RBM_CPTS_DIR' ) ) {
				// Plugin path
				define( 'RBM_CPTS_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'RBM_CPTS_URL' ) ) {
				// Plugin URL
				define( 'RBM_CPTS_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'RBM_CPTS_FILE' ) ) {
				// Plugin File
				define( 'RBM_CPTS_FILE', __FILE__ );
			}

		}

		/**
		 * Internationalization
		 *
		 * @access	  private 
		 * @since	  1.2.1
		 * @return	  void
		 */
		private function load_textdomain() {

			// Set filter for language directory
			$lang_dir = RBM_CPTS_DIR . '/languages/';
			$lang_dir = apply_filters( 'rbm_cpts_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale = apply_filters( 'plugin_locale', get_locale(), 'rbm-cpts' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'rbm-cpts', $locale );

			// Setup paths to current locale file
			$mofile_local   = $lang_dir . $mofile;
			$mofile_global  = WP_LANG_DIR . '/rbm-cpts/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/rbm-cpts/ folder
				// This way translations can be overridden via the Theme/Child Theme
				load_textdomain( 'rbm-cpts', $mofile_global );
			}
			else if ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/rbm-cpts/languages/ folder
				load_textdomain( 'rbm-cpts', $mofile_local );
			}
			else {
				// Load the default language files
				load_plugin_textdomain( 'rbm-cpts', false, $lang_dir );
			}

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

		/**
		 * Show admin errors.
		 * 
		 * @access	  public
		 * @since	  1.2.1
		 * @return	  void
		 */
		public function admin_errors() {
			?>
			<div class="error">
				<?php foreach ( $this->admin_errors as $notice ) : ?>
					<p>
						<?php echo $notice; ?>
					</p>
				<?php endforeach; ?>
			</div>
			<?php
		}

	}

	add_action( 'plugins_loaded', 'rbm_cpt_init' );

	function rbm_cpt_init() {

		require_once __DIR__ . '/core/rbm-cpts-functions.php';
		RBM_CPTS();
		
	}
	
}