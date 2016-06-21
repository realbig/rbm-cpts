<?php
/**
 * Creates and manages the Palm Tree CPT.
 *
 * @since      0.1.0
 *
 * @package    RBM_CPTS
 * @subpackage RBM_CPTS/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

class RBM_CPT_PalmTree extends RBM_CPT {

	public $post_type = 'palm-tree';
	public $label_singular = 'Palm Tree';
	public $label_plural = 'Palm Trees';
	public $icon = 'palmtree';
}