<?php
/*
Plugin Name: RS Preview WooCommerce Emails
Version:     1.2.0
Plugin URI:  http://radleysustaire.com/
Description: Adds the ability to preview all WooCommerce order emails in a popup without sending an email. Accessible through the "Order Actions" dropdown when editing an order. A bonus option includes the link to view a customer's "Completed Order" page - the same they see after they complete a purchase.
Author:      Radley Sustaire
Author URI:  mailto:radleygh@gmail.com
License:     GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.txt
*/

if ( !defined( 'ABSPATH' ) ) exit;

define( 'PWE_URL', untrailingslashit(plugin_dir_url( __FILE__ )) );
define( 'PWE_PATH', dirname(__FILE__) );
define( 'PWE_VERSION', '1.2.0' );

add_action( 'plugins_loaded', 'pwe_init_plugin' );

// Initialize plugin: Load plugin files
function pwe_init_plugin() {
	// Require WooCommerce or show a message.
	if ( !function_exists('WC') ) {
		add_action( 'admin_notices', 'pwe_warn_no_woocommerce' );
		return;
	}
	
	include_once( PWE_PATH . '/includes/enqueue.php' );
	include_once( PWE_PATH . '/includes/order-actions.php' );
}

// Display a warning when WooCommerce is not active
function pwe_warn_no_woocommerce() {
	?>
	<div class="error">
		<p><strong>RS Preview WooCommerce Emails:</strong> This plugin requires WooCommerce in order to operate. Please install and activate WooCommerce, or disable this plugin.</p>
	</div>
	<?php
}