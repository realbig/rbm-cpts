<?php
/**
 * Plugin Name: RBM Custom Post Types
 * Description: Creates custom post types.
 * Version: 0.1.3
 * Author: Real Big Marketing
 * Author URI: http://realbigmarketing.com
 * GitHub Plugin URI: realbig/rbm-cpts
 * GitHub Branch: develop
 */

defined( 'ABSPATH' ) || die();

if ( ! class_exists( 'RBM_CPTS' ) ) {

	// Define plugin constants
	define( 'RBM_CPTS_VERSION', '0.1.3' );
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
	}

	add_action( 'plugins_loaded', 'rbm_cpt_init' );

	function rbm_cpt_init() {

		if ( defined( 'RBM_HELPER_FUNCTIONS' ) ) {
			require_once __DIR__ . '/core/rbm-cpts-functions.php';
			RBM_CPTS();
		} else {
			add_action( 'admin_notices', 'rbm_cpt_init_fail' );
		}
	}

	function rbm_cpt_init_fail() {
		?>
		<div class="error">
			<p>
				ERROR: <strong>RBM Custom Post Types</strong> could not load because RBM Field Helpers is not
				active as a must use plugin on this site.
			</p>
		</div>
		<?php
	}
}