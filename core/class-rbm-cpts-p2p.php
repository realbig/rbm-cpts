<?php
/**
 * Post to Post relationships.
 *
 * @since      0.1.0
 *
 * @package    RBM_CPTS
 * @subpackage RBM_CPTS/core
 */

defined( 'ABSPATH' ) || die();

class RBM_CPTS_P2P {

	/**
	 * All P2P relationships.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private $relationships;

	/**
	 * RBM_CPTS_P2P constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {

		add_action( 'init', array( $this, 'get_p2p_relationships' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_p2p_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_p2ps' ), 1 );
		add_action( 'before_delete_post', array( $this, 'delete_p2ps' ) );
	}

	/**
	 * Gets all p2ps.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function get_p2p_relationships() {

		if ( ! $this->relationships ) {

			/**
			 * Gets all p2p relationships and allows filtering.
			 *
			 * @since 1.0.0
			 */
			$this->relationships = apply_filters( 'p2p_relationships', array() );
		}
	}

	/**
	 * Adds metaboxes for all p2ps.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function add_p2p_meta_boxes() {

		$post_type = get_post_type();
		$this->get_p2p_relationships();

		if ( ! isset( $this->relationships[ $post_type ] ) &&
		     ! in_array( $post_type, $this->relationships )
		) {

			return;
		}

		if ( ! has_filter( 'rbm_load_select2', '__return_true' ) ) {

			add_filter( 'rbm_load_select2', '__return_true' );
		}

		add_meta_box(
			'rbm-p2ps',
			'Post to Post',
			array( $this, 'p2p_metabox' ),
			$post_type,
			'side'
		);
	}

	/**
	 * The metabox for establishing p2ps.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	function p2p_metabox() {

		$post_type = get_post_type();

		$this->get_p2p_relationships();

		// Load the proper metabox
		if ( isset( $this->relationships[ $post_type ] ) ) {

			$this->p2p_child_metabox();

		} elseif ( in_array( $post_type, $this->relationships ) ) {

			$this->p2p_parent_metabox();
		}
	}

	/**
	 * Loads the child metabox.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function p2p_child_metabox() {

		$post_type     = get_post_type();
		$post_type_obj = get_post_type_object( $post_type );

		$relationship = $this->relationships[ $post_type ];

		$relationship_post_type = get_post_type_object( $relationship );

		$relationship_posts = get_posts( apply_filters( 'rbm_cpts_available_p2p_posts', array(
			'post_type'   => $relationship,
			'numberposts' => - 1,
			'post_status' => 'any',
			'order'       => 'ASC',
			'orderby'     => 'title',
		), $post_type, $relationship ) );

		$options = array( '' => '- None -' ) + wp_list_pluck( $relationship_posts, 'post_title', 'ID' );

		foreach ( $options as $post_ID => $post_title ) {
			if ( $post_title == '' ) {
				$options[ $post_ID ] = "(no title) Post ID: $post_ID";
			}
		}

		$p2p_select_field_args = apply_filters( 'rbm_cpts_p2p_select_args', array(
			'group' => "p2p_{$relationship}",
			'label'       => "{$relationship_post_type->labels->singular_name} this {$post_type_obj->labels->singular_name} belongs to:",
			'options'     => $options,
			'input_class' => 'rbm-select2',
		), $post_type, $relationship );

		RBM_CPTS()->field_helpers->fields->do_field_select( "p2p_{$relationship}", $p2p_select_field_args );
		rbm_cpts_init_field_group( "p2p_{$relationship}" );

		echo '<hr/>';
	}

	/**
	 * Loads the parent metabox.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private function p2p_parent_metabox() {

		$post_type     = get_post_type();
		$post_type_obj = get_post_type_object( $post_type );

		$relationships = array_keys( $this->relationships, $post_type );

		foreach ( $relationships as $relationship ) {

			$child_post_type_obj = get_post_type_object( $relationship );

			if ( ! ( $relationship_posts = get_post_meta( get_the_ID(), "p2p_children_{$relationship}s", true ) ) ) {
				continue;
			}

			$relationship_posts = get_posts( array(
				'post_type'   => $relationship,
				'post__in'    => $relationship_posts,
				'order'       => 'ASC',
				'orderby'     => 'title',
				'numberposts' => - 1,
			) );

			if ( $relationship_posts ) : ?>

                <p class="p2p-relationship-posts-list-title">
                    <strong>
						<?php echo $child_post_type_obj->labels->name; ?> that belong to this
						<?php echo $post_type_obj->labels->singular_name; ?>:
                    </strong>
                </p>

                <ul class="p2p-relationship-posts-list">
					<?php foreach ( $relationship_posts as $relationship_post ) : ?>
                        <li>
                            <a href="<?php echo get_edit_post_link( $relationship_post->ID ); ?>">
								<?php echo $relationship_post->post_title; ?>
                            </a>
                        </li>
					<?php endforeach; ?>
                </ul>

                <hr/>
			<?php endif;
		}
	}

	/**
	 * Saves all p2ps for this post.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int $post_ID The post ID being saved.
	 */
	function save_p2ps( $post_ID ) {

		// Make sure this is the main post being saved
		if ( ! isset( $_POST['post_ID'] ) || 
					 $post_ID != $_POST['post_ID'] ) {

			return;
		}

		$post_type = get_post_type( $post_ID );
		$this->get_p2p_relationships();

		if ( ! isset( $this->relationships[ $post_type ] ) ) {

			return;
		}

		$relationship = $this->relationships[ $post_type ];

		$past_relationship_posts = rbm_cpts_get_field( "p2p_{$relationship}", $post_ID );
		if ( ! is_array( $past_relationship_posts ) ) {
			$past_relationship_posts = array( $past_relationship_posts );
		}

		// If there is none defined, delete any existing and move on
		if ( ! isset( $_POST["rbm_cpts_p2p_$relationship"] ) || ! $_POST["rbm_cpts_p2p_$relationship"] ) {

			foreach ( $past_relationship_posts as $past_relationship_post_ID ) {

				if ( get_post( $past_relationship_post_ID ) ) {

					delete_post_meta( $past_relationship_post_ID, "p2p_children_{$post_type}s" );

				}

			}

			return;
		}

		$relationship_posts = $_POST["rbm_cpts_p2p_$relationship"];
		if ( ! is_array( $relationship_posts ) ) {
			$relationship_posts = array( $relationship_posts );
		}

		// If there has already been saved relationships, delete any no longer there for each related post, just in case we've
		// removed some.
		if ( $past_relationship_posts ) {

			foreach ( $past_relationship_posts as $past_relationship_post_ID ) {

				if ( ! in_array( $past_relationship_post_ID, $relationship_posts ) ) {

					delete_post_meta( $past_relationship_post_ID, "p2p_children_{$post_type}s" );

				}

			}

		}

		foreach ( $relationship_posts as $relationship_post_ID ) {

			// Get new relationships
			if ( $relationship_post_relationships = get_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s", true ) ) {

				// Make sure all relationships are still valid!
				$valid_relationships = $this->validate_relationships( $relationship_post_relationships, $post_type );

				if ( empty( $valid_relationships ) ) {

					delete_post_meta( $past_relationship_post_ID, "p2p_children_{$post_type}s" );

					return;

				}

				// If there are already relationships established, add this one to it, if not already
				if ( ! in_array( $post_ID, $valid_relationships ) ) {

					$valid_relationships[] = $post_ID;
				}

				// If there's a difference, update it
				if ( $valid_relationships !== $relationship_post_relationships ) {

					update_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s", $valid_relationships );
				}

			} else {

				// If there are no relationships established yet, add this as the first
				update_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s", array( $post_ID ) );
			}

		}

	}

	/**
	 * If any relationship posts don't exist, remove them.
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param array $relationships Array of post IDs.
	 * @param bool|string $post_type If set, it will also match against a post type.
	 *
	 * @return array
	 */
	function validate_relationships( $relationships, $post_type = false ) {

		$valid_relationships = array();

		foreach ( $relationships as $relationship_post_ID ) {

			if ( ! get_post( $relationship_post_ID ) ) {

				continue;
			}

			if ( $post_type !== false && $post_type != get_post_type( $relationship_post_ID ) ) {

				continue;
			}

			$valid_relationships[] = $relationship_post_ID;
		}

		return $valid_relationships;
	}

	/**
	 * Deletes p2ps for this post
	 *
	 * @since 1.0.0
	 * @access private
	 *
	 * @param int $post_ID The post ID being deleted.
	 */
	function delete_p2ps( $post_ID ) {

		global $post_type;

		$this->get_p2p_relationships();

		// If is a child of posts
		if ( isset( $this->relationships[ $post_type ] ) ) {

			$relationship = $this->relationships[ $post_type ];

			// If this post made a p2p
			if ( $relationship_posts = rbm_cpts_get_field( "p2p_$relationship", $post_ID ) ) {

				if ( ! is_array( $relationship_posts ) ) {
					$relationship_posts = array( $relationship_posts );
				}

				foreach ( $relationship_posts as $relationship_post_ID ) {

					// If the p2p post does indeed have meta for this post ID
					if ( $relationship_post_relationships = get_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s", true ) ) {

						// If this post ID appears in the p2p post meta, remove it
						if ( ( $key = array_search( $post_ID, $relationship_post_relationships ) ) !== false ) {

							unset( $relationship_post_relationships[ $key ] );

							if ( empty( $relationship_post_relationships ) ) {

								// If it was the only p2p, delete entirley...
								delete_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s" );
							} else {

								// ...otherwise remove it and update it
								update_post_meta( $relationship_post_ID, "p2p_children_{$post_type}s", $relationship_post_relationships );
							}
						}
					}

				}

			}
		}

		// If is a parent of posts
		foreach ( $this->relationships as $child => $relationship ) {

			if ( $post_type != $relationship ) {
				continue;
			}

			// Cycle through any children this post has (if any) and delete them)
			if ( $child_relationships = get_post_meta( $post_ID, "p2p_children_{$child}s", true ) ) {
				foreach ( $child_relationships as $child_relationship ) {

					// If the p2p post does indeed have meta for this post ID
					if ( $child_post_relationship = rbm_cpts_get_field( "p2p_$post_type", $child_relationship ) ) {
						if ( $child_post_relationship == $post_ID ) {
							delete_post_meta( $child_relationship, "rbm_cpts_p2p_$post_type" );
						}
					}
				}
			}
		}
	}
}