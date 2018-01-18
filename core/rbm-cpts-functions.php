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
	
	if ( $meta = rbm_cpts_get_field( "p2p_{$p2p_type}", $post_ID ) ) {
		return $meta;
	}
	else {
		return get_post_meta( $post_ID, "_rbm_p2p_{$p2p_type}", true );
	}
	
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

/**
 * Quick access to plugin field helpers.
 *
 * @since {{VERSION}}
 *
 * @return RBM_FieldHelpers
 */
function rbm_cpts_field_helpers() {
	return RBM_CPTS()->field_helpers;
}

/**
 * Initializes a field group for automatic saving.
 *
 * @since {{VERSION}}
 *
 * @param $group
 */
function rbm_cpts_init_field_group( $group ) {
	rbm_cpts_field_helpers()->fields->save->initialize_fields( $group );
}

/**
 * Gets a meta field helpers field.
 *
 * @since {{VERSION}}
 *
 * @param string $name Field name.
 * @param string|int $post_ID Optional post ID.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function rbm_cpts_get_field( $name, $post_ID = false, $default = '', $args = array() ) {
    $value = rbm_cpts_field_helpers()->fields->get_meta_field( $name, $post_ID, $args );
    return $value !== false ? $value : $default;
}

/**
 * Gets a option field helpers field.
 *
 * @since {{VERSION}}
 *
 * @param string $name Field name.
 * @param mixed $default Default value if none is retrieved.
 * @param array $args
 *
 * @return mixed Field value
 */
function rbm_cpts_get_option_field( $name, $default = '', $args = array() ) {
	$value = rbm_cpts_field_helpers()->fields->get_option_field( $name, $args );
	return $value !== false ? $value : $default;
}

/**
 * Outputs a text field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_text( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_text( $args['name'], $args );
}

/**
 * Outputs a textarea field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_textarea( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_textarea( $args['name'], $args );
}

/**
 * Outputs a checkbox field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_checkbox( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_checkbox( $args['name'], $args );
}

/**
 * Outputs a toggle field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_toggle( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_toggle( $args['name'], $args );
}

/**
 * Outputs a radio field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_radio( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_radio( $args['name'], $args );
}

/**
 * Outputs a select field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_select( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_select( $args['name'], $args );
}

/**
 * Outputs a number field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_number( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_number( $args['name'], $args );
}

/**
 * Outputs an image field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_media( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_media( $args['name'], $args );
}

/**
 * Outputs a datepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_datepicker( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_datepicker( $args['name'], $args );
}

/**
 * Outputs a timepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_timepicker( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_timepicker( $args['name'], $args );
}

/**
 * Outputs a datetimepicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_datetimepicker( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_datetimepicker( $args['name'], $args );
}

/**
 * Outputs a colorpicker field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_colorpicker( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_colorpicker( $args['name'], $args );
}

/**
 * Outputs a list field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_list( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_list( $args['name'], $args );
}

/**
 * Outputs a hidden field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_hidden( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_hidden( $args['name'], $args );
}

/**
 * Outputs a table field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_table( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_table( $args['name'], $args );
}

/**
 * Outputs a HTML field.
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_do_field_html( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_html( $args['name'], $args );
}

/**
 * Outputs a repeater field.
 *
 * @since {{VERSION}}
 *
 * @param mixed $values
 */
function rbm_cpts_do_field_repeater( $args = array() ) {
	rbm_cpts_field_helpers()->fields->do_field_repeater( $args['name'], $args );
}

/**
 * Outputs a String if a Callback Function does not exist for an Options Page Field
 *
 * @since {{VERSION}}
 *
 * @param array $args
 */
function rbm_cpts_missing_callback( $args ) {
	
	printf( 
		_x( 'A callback function called "rbm_cpts_do_field_%s" does not exist.', '%s is the Field Type', 'rbm-cpts' ),
		$args['type']
	);
		
}