<?php
/**
 * Provides helper functions.
 *
 * @since      0.1.0
 *
 * @package    RBM_CPTS
 * @subpackage RBM_CPTS/core
 */

defined( 'ABSPATH' ) || die();

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

/**
 * Retrieves the p2p parent.
 *
 * @since {{VERSION}}
 *
 * @param string $p2p_type The P2P post type.
 * @param bool|int $post_ID ID of the post to use.
 *
 * @return bool|mixed
 */
function rbm_cpts_get_p2p_parent( $p2p_type, $post_ID = false ) {

	if ( $post_ID === false ) {

		if ( ! ( $post_ID = get_the_ID() ) ) {

			return false;
		}
	}

	return get_post_meta( $post_ID, "p2p_$p2p_type", true );
}

/**
 * Retrieves the p2p children.
 *
 * @since {{VERSION}}
 *
 * @param string $p2p_type The P2P post type.
 * @param bool|int $post_ID ID of the post to use.
 *
 * @return bool|mixed
 */
function rbm_cpts_get_p2p_children( $p2p_type, $post_ID = false ) {

	if ( $post_ID === false ) {

		if ( ! ( $post_ID = get_the_ID() ) ) {

			return false;
		}
	}

	return get_post_meta( $post_ID, "p2p_children_{$p2p_type}s", true );
}