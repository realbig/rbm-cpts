<?php
/**
 * Provides helper functions.
 *
 * @since      0.1.0
 *
 * @package    RBM_CPTS
 * @subpackage RBM_CPTS/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Returns the main plugin object
 *
 * @since 0.1.0
 *
 * @return RBM_CPTS
 */
function RBM_CPTS() {
	return RBM_CPTS::getInstance();
}