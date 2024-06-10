<?php

if ( !defined( 'ABSPATH' ) ) exit;

function pwe_admin_enqueue() {
	if ( !function_exists('get_current_screen') ) return;
	
	$screen = get_current_screen();
	// shop_order = legacy orders page
	// woocommerce_page_wc-orders = new orders page with block editor
	if ( $screen->id != 'shop_order' && $screen->id != 'woocommerce_page_wc-orders' ) return;
	
	// Include lightbox scripts
	add_thickbox();
	
	wp_enqueue_script( 'rs-pwe-admin', PWE_URL . '/assets/preview-woocommerce-emails.js', array('jquery'), PWE_VERSION );
}
add_action( 'admin_enqueue_scripts', 'pwe_admin_enqueue' );