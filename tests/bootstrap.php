<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {

	// Bypass requirement of having field helpers, we're not testing integration.
	define( 'RBM_HELPER_FUNCTIONS', '1' );

	require dirname( dirname( __FILE__ ) ) . '/rbm-cpts.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';