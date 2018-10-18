<?php

if ( !defined( 'ABSPATH' ) ) exit;

function pwe_admin_enqueue() {
	if ( !function_exists('get_current_screen') ) return;
	
	$screen = get_current_screen();
	if ( $screen->id != 'shop_order' ) return;
	
	// Include lightbox scripts
	add_thickbox();
	
	wp_enqueue_script( 'aa-pwe-admin', PWE_URL . '/assets/aa-preview-woocommerce-emails.js', array('jquery'), PWE_VERSION );
}
add_action( 'admin_enqueue_scripts', 'pwe_admin_enqueue' );