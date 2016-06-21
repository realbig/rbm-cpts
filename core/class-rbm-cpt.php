<?php
/**
 * CPT Framework.
 *
 * @since      0.1.0
 *
 * @package    RBM_CPTS
 * @subpackage RBM_CPTS/core
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

abstract class RBM_CPT {

	public $post_type;
	public $label_singular;
	public $label_plural;
	public $icon;
	public $p2p;
	public $p2p_archive = false;
	public $supports = array( 'title', 'editor' );
	public $labels = array();

	function __construct() {

		$this->add_actions();
	}

	private function add_actions() {

		add_action( 'init', array( $this, 'create_cpt' ) );
		add_filter( 'post_updated_messages', array( $this, 'post_messages' ) );
		add_filter( 'p2p_relationships', array( $this, 'p2p' ) );

		if ( $this->p2p_archive ) {

			add_action( 'init', array( $this, 'add_rewrite_rules' ) );
			add_filter( 'template_include', array( $this, 'locate_template' ) );
		}
	}

	function create_cpt() {

		$labels = wp_parse_args( $this->labels, array(
			'name'               => $this->label_plural,
			'singular_name'      => $this->label_singular,
			'menu_name'          => $this->label_plural,
			'name_admin_bar'     => $this->label_singular,
			'add_new'            => "Add New",
			'add_new_item'       => "Add New $this->label_singular",
			'new_item'           => "New $this->label_singular",
			'edit_item'          => "Edit $this->label_singular",
			'view_item'          => "View $this->label_singular",
			'all_items'          => "All $this->label_plural",
			'search_items'       => "Search $this->label_plural",
			'parent_item_colon'  => "Parent $this->label_plural:",
			'not_found'          => "No $this->label_plural found.",
			'not_found_in_trash' => "No $this->label_plural found in Trash.",
		) );

		$supports = $this->supports;

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'menu_icon'          => 'dashicons-' . $this->icon,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => $supports,
		);

		register_post_type( $this->post_type, $args );
	}

	function post_messages( $messages ) {

		$post             = get_post();
		$post_type_object = get_post_type_object( $this->post_type );

		$messages[ $this->post_type ] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => "$this->label_singular updated.",
			2  => 'Custom field updated.',
			3  => 'Custom field deleted.',
			4  => "$this->label_singular updated.",
			/* translators: %s: date and time of the revision */
			5  => isset( $_GET['revision'] ) ? "$this->label_singular restored to revision from " . wp_post_revision_title( (int) $_GET['revision'], false ) : false,
			6  => "$this->label_singular published.",
			7  => "$this->label_singular saved.",
			8  => "$this->label_singular submitted.",
			9  => "$this->label_singular scheduled for: <strong>" . date( 'M j, Y @ G:i', strtotime( $post->post_date ) ) . '</strong>.',
			10 => "$this->label_singular draft updated.",
		);

		if ( $post_type_object->publicly_queryable ) {
			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), "View $this->label_singular" );
			$messages[ $this->post_type ][1] .= $view_link;
			$messages[ $this->post_type ][6] .= $view_link;
			$messages[ $this->post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link      = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), "Preview $this->label_singular" );
			$messages[ $this->post_type ][8] .= $preview_link;
			$messages[ $this->post_type ][10] .= $preview_link;
		}

		return $messages;
	}

	function p2p( $relationships ) {

		if ( $this->p2p ) {
			$relationships[ $this->post_type ] = $this->p2p;
		}

		return $relationships;
	}

	function add_rewrite_rules() {

		add_rewrite_rule(
			"^{$this->p2p_archive}/([^/]*)/{$this->post_type}/?",
			"index.php?{$this->post_type}_{$this->p2p_archive}=\$matches[1]&p2p_type={$this->post_type}&p2p_archive={$this->p2p_archive}",
			'top'
		);

		add_rewrite_tag( "%{$this->post_type}_{$this->p2p_archive}%", '([^&]+)' );
		add_rewrite_tag( "%p2p_type%", '([^&]+)' );
		add_rewrite_tag( "%p2p_archive%", '([^&]+)' );
	}

	function locate_template( $template ) {

		global $wp_query;

		if ( isset( $wp_query->query_vars["{$this->post_type}_{$this->p2p_archive}"] ) ) {

			$template = $template = locate_template( array(
				"{$this->p2p_archive}-archive-{$this->post_type}.php",
				'p2p-archive.php',
			) );

			if ( ! $template ) {
				$template = get_single_template();
			}
		}

		return $template;
	}
}