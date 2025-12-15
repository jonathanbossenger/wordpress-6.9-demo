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

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

add_action( 'plugins_loaded', 'wp69_demo_register_mcp_adapter' );
/**
 * Undocumented function
 *
 * @return void
 */
function wp69_demo_register_mcp_adapter() {
	if ( ! class_exists( WP\MCP\Core\McpAdapter::class ) ) {
		// Check if the MCP Adapter class is available, if not show some sort of error or admin notice.
		return;
	}

	// Initialize MCP Adapter and its default server.
	WP\MCP\Core\McpAdapter::instance();
}

/**
 * Fetches a post based on the provided slug and post type.
 *
 * @param string $slug The post slug (required).
 * @param string $post_type The post type (optional).
 */
function wp69_demo_get_post( $slug, $post_type = 'post' ) {
	$args  = array(
		'post_name'      => $slug,
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

/**
 * Function used to return clean post content
 *
 * @return string The new post content.
 */
function wp69_demo_post_content() {
	return file_get_contents( plugin_dir_path( __FILE__ ) . 'content/post.html' );
}

register_activation_hook( __FILE__, 'wp69_demo_enable_custom_fields' );
register_activation_hook( __FILE__, 'wp69_demo_update_initial_post' );
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

/**
 * Update the initial post with new content upon plugin activation.
 */
function wp69_demo_update_initial_post() {
	$updated_post = array(
		'ID'           => 1,
		'post_title'   => 'Hello Gene.',
		'post_name'    => 'hello-gene',
		'post_status'  => 'publish',
		'post_content' => wp69_demo_post_content(),
	);
	wp_update_post( $updated_post );
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
			'author',
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
}

add_action( 'wp_abilities_api_init', 'wp69_demo_create_book_ability' );
/**
 * Reset book ability
 *
 * @return void
 */
function wp69_demo_create_book_ability() {
	wp_register_ability(
		'wp69-demo/create-book',
		array(
			'label'               => __( 'Create book', 'wp69-demo' ),
			'description'         => __( 'Creates an initial book.', 'wp69-demo' ),
			'category'            => 'site',
			'output_schema'       => array(
				'type'        => 'string',
				'description' => 'Status message.',
			),
			'execute_callback'    => 'wp_69_demo_create_book_callback',
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
 * Create book ability callback
 *
 * @return string
 */
function wp_69_demo_create_book_callback() {
	$new_book = array(
		'post_title'   => 'Meditations',
		'post_name'    => 'meditations',
		'post_content' => wp69_demo_book_content(),
		'post_status'  => 'publish',
		'post_type'    => 'book',
	);
	$post_id  = wp_insert_post( $new_book );
	update_post_meta( $post_id, 'isbn', '9876543210' );
	update_post_meta( $post_id, 'author', 'Marcus Aurelius' );
	update_post_meta( $book->ID, 'quality', 'Good' );
	return 'Created new book.';
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
			'description'         => __( 'Resets the book content back to it\'s original state.', 'wp69-demo' ),
			'category'            => 'site',
			'output_schema'       => array(
				'type'        => 'string',
				'description' => 'Status message.',
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
		return 'Error fetching the book.';
	}
	$updated_book = array(
		'ID'           => $book->ID,
		'post_title'   => 'Meditations',
		'post_content' => wp69_demo_book_content(),
		'post_status'  => 'publish',
	);
	wp_update_post( $updated_book );
	update_post_meta( $book->ID, 'isbn', '9876543210' );
	update_post_meta( $book->ID, 'author', 'Marcus Aurelius' );
	update_post_meta( $book->ID, 'quality', 'Good' );
	return 'Updated the book content.';
}
