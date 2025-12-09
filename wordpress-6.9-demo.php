<?php
/**
 * Plugin Name: WordPress 6.9 Demo
 * Description: A simple demo plugin for WordPress 6.9 features.
 * Version: 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'wp69_demo_admin_enqueue_scripts' );
function wp69_demo_admin_enqueue_scripts() {
	wp_enqueue_script(
		'demo-admin-script',
		plugins_url( 'src/index.js', __FILE__ ),
		array(),
		'1.0.0',
		true
	);
}

add_action( 'init', 'wp69_demo_init' );
function wp69_demo_init() {
	$labels = array(
		'name'          => 'Books',
		'singular_name' => 'Book',
	);

	register_post_type(
		'book',
		array(
			'labels'        => $labels,
			'public'       => true,
			'show_in_rest' => true,
			'supports'     => array(
				'title',
				'editor' => array(
					'notes' => true
				),
				'author',
			),
		)
	);
}