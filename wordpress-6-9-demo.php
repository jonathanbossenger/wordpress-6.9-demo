<?php
/**
 * Plugin Name: WordPress 6.9 Demo
 * Description: A simple demo plugin for WordPress 6.9 features.
 * Version: 1.0.0
 *
 * @package wordpress-6-9-demo
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fetches the book with the slug meditations
 *
 * @param string $slug The post slug (required).
 * @param string $post_type The post type (optional).
 */
function wp69_demo_get_post( $slug, $post_type = 'post' ) {
	$args  = array(
		'name'           => $slug,
		'post_type'      => $post_type,
		'posts_per_page' => 1,
		'post_status'    => 'publish',
	);
	$posts = get_posts( $args );
	if ( empty( $posts ) ) {
		return null;
	}

	return $posts[0];
}
/**
 * Function used to return clean book content
 *
 * @return string The new book content.
 */
function wp69_demo_book_content() {
	return file_get_contents( plugin_dir_path( __FILE__ ) . 'content/book.html' );
}

register_activation_hook( __FILE__, 'wp69_demo_enable_custom_fields' );
/**
 * Enable custom fields for the current user upon plugin activation.
 */
function wp69_demo_enable_custom_fields() {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		update_user_meta( $user_id, 'enable_custom_fields', 'true' );
	}
	flush_rewrite_rules();
}


register_deactivation_hook( __FILE__, 'wp69_demo_disable_custom_fields' );
/**
 * Disable custom fields for the current user upon plugin deactivation.
 */
function wp69_demo_disable_custom_fields() {
	$user_id = get_current_user_id();
	if ( $user_id ) {
		delete_user_meta( $user_id, 'enable_custom_fields' );
	}
	flush_rewrite_rules();
}

add_filter( 'postmeta_form_keys', 'wp69_demo_add_meta_to_quick_edit', 10, 2 );
/**
 * Adds our meta keys to the Custom Fields panel when adding/editing a book
 *
 * @param array  $keys The array of meta keys.
 * @param object $post The post object.
 *
 * @return array The updated array of meta keys.
 */
function wp69_demo_add_meta_to_quick_edit( $keys, $post ) {
	if ( 'book' === $post->post_type ) {
		if ( ! is_array( $keys ) ) {
			$keys = array();
		}
		$keys_to_add = array( 'isbn', 'author' );
		foreach ( $keys_to_add as $key ) {
			if ( ! in_array( $key, $keys, true ) ) {
				$keys[] = $key;
			}
		}
	}
	return $keys;
}

add_action( 'init', 'wp69_demo_init' );
/**
 * Initialize the demo plugin by registering the 'book' custom post type
 *
 * @return void
 */
function wp69_demo_init() {
	$args = array(
		'labels'       => array(
			'name'          => 'Books',
			'singular_name' => 'Book',
			'menu_name'     => 'Books',
			'add_new'       => 'Add New Book',
			'add_new_item'  => 'Add New Book',
			'new_item'      => 'New Book',
			'edit_item'     => 'Edit Book',
			'view_item'     => 'View Book',
			'all_items'     => 'All Books',
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'rest_base'    => 'books',
		'supports'     => array(
			'title',
			'editor',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'editor' => array( 'notes' => true ),
		),
	);

	register_post_type( 'book', $args );

	register_post_meta(
		'book',
		'isbn',
		array(
			'single'       => true,
			'type'         => 'string',
			'show_in_rest' => true,
			'label'        => __( 'ISBN', 'wp-learn-block-bindings' ),
		)
	);

	register_post_meta(
		'book',
		'author',
		array(
			'show_in_rest' => true,
			'single'       => true,
			'type'         => 'string',
			'label'        => __( 'Author', 'wp-learn-block-bindings' ),
		)
	);

	flush_rewrite_rules();
}

add_action( 'admin_enqueue_scripts', 'wp69_demo_admin_enqueue_scripts' );
/**
 * Enqueue and localize admin scripts for the demo plugin.
 */
function wp69_demo_admin_enqueue_scripts() {
	wp_enqueue_script(
		'demo-admin-script',
		plugins_url( 'src/index.js', __FILE__ ),
		array( 'wp-api', 'wp-api-fetch' ),
		'1.0.0',
		true
	);

	$page_slugs = array(
		'posts' => array(
			'hello-gene',
		),
		'books' => array(
			'meditations',
		),
	);

	wp_localize_script(
		'demo-admin-script',
		'wpDemoData',
		array(
			'pageSlugs' => $page_slugs,
		)
	);
}

add_action( 'wp_abilities_api_init', 'wp69_demo_reset_book_ability' );
/**
 * Reset book ability
 *
 * @return void
 */
function wp69_demo_reset_book_ability() {
	wp_register_ability(
		'wp69-demo/reset-book',
		array(
			'label'               => __( 'Reset book content', 'wp69-demo' ),
			'description'         => __( 'Retrieves the title of the current WordPress site.', 'wp69-demo' ),
			'category'            => 'site',
			'output_schema'       => array(
				'type'        => 'string',
				'description' => 'Update message.',
			),
			'execute_callback'    => 'wp_69_demo_reset_book_callback',
			'permission_callback' => function () {
				return current_user_can( 'manage_options' );
			},
			'meta'                => array(
				'show_in_rest' => true,
			),
		)
	);
}

/**
 * Reset book ability callback
 *
 * @return string
 */
function wp_69_demo_reset_book_callback() {
	$book = wp69_demo_get_post( 'meditations', 'book' );
	if ( ! $book ) {
		return 'Error fetching book';
	}
	$updated_book = array(
		'ID'           => $book->ID,
		'post_title'   => 'Meditations.',
		'post_content' => wp69_demo_book_content(),
	);
	wp_update_post( $updated_book );
	return 'Updated book.';
}
