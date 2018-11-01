<?php

if ( !defined( 'ABSPATH' ) ) exit;

function pwe_ajax_return_WC_Email_New_Order() { pwe_ajax_return_html_template( 'WC_Email_New_Order' ); }
function pwe_ajax_return_WC_Email_Cancelled_Order() { pwe_ajax_return_html_template( 'WC_Email_Cancelled_Order' ); }
function pwe_ajax_return_WC_Email_Failed_Order() { pwe_ajax_return_html_template( 'WC_Email_Failed_Order' ); }
function pwe_ajax_return_WC_Email_Customer_On_Hold_Order() { pwe_ajax_return_html_template( 'WC_Email_Customer_On_Hold_Order' ); }
function pwe_ajax_return_WC_Email_Customer_Processing_Order() { pwe_ajax_return_html_template( 'WC_Email_Customer_Processing_Order' ); }
function pwe_ajax_return_WC_Email_Customer_Completed_Order() { pwe_ajax_return_html_template( 'WC_Email_Customer_Completed_Order' ); }
function pwe_ajax_return_WC_Email_Customer_Refunded_Order() { pwe_ajax_return_html_template( 'WC_Email_Customer_Refunded_Order' ); }
function pwe_ajax_return_WC_Email_Customer_Invoice() { pwe_ajax_return_html_template( 'WC_Email_Customer_Invoice' ); }
function pwe_ajax_return_WC_Email_Customer_Note() { pwe_ajax_return_html_template( 'WC_Email_Customer_Note' ); }
function pwe_ajax_return_WC_Email_Customer_Reset_Password() { pwe_ajax_return_html_template( 'WC_Email_Customer_Reset_Password' ); }
function pwe_ajax_return_WC_Email_Customer_New_Account() { pwe_ajax_return_html_template( 'WC_Email_Customer_New_Account' ); }

add_action( 'wp_ajax_pwe_get_email_preview_New_Order', 'pwe_ajax_return_WC_Email_New_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Cancelled_Order', 'pwe_ajax_return_WC_Email_Cancelled_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Failed_Order', 'pwe_ajax_return_WC_Email_Failed_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_On_Hold_Order', 'pwe_ajax_return_WC_Email_Customer_On_Hold_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Processing_Order', 'pwe_ajax_return_WC_Email_Customer_Processing_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Completed_Order', 'pwe_ajax_return_WC_Email_Customer_Completed_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Refunded_Order', 'pwe_ajax_return_WC_Email_Customer_Refunded_Order' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Invoice', 'pwe_ajax_return_WC_Email_Customer_Invoice' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Note', 'pwe_ajax_return_WC_Email_Customer_Note' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_Reset_Password', 'pwe_ajax_return_WC_Email_Customer_Reset_Password' );
add_action( 'wp_ajax_pwe_get_email_preview_Customer_New_Account', 'pwe_ajax_return_WC_Email_Customer_New_Account' );

function pwe_add_order_actions( $actions ) {
	$actions['pwe_visit_receipt_page'] = 'View customer receipt page';
	
	$actions['pwe_empty'] = '';
	$actions['pwe_separator'] = 'Email Previews:';
	$actions['pwe_get_email_preview_New_Order']                 = '&nbsp; &nbsp; [Admin] New Order';
	$actions['pwe_get_email_preview_Cancelled_Order']           = '&nbsp; &nbsp; [Admin] Cancelled Order';
	$actions['pwe_get_email_preview_Failed_Order']              = '&nbsp; &nbsp; [Admin] Failed Order';
	$actions['pwe_get_email_preview_Customer_On_Hold_Order']    = '&nbsp; &nbsp; On Hold';
	$actions['pwe_get_email_preview_Customer_Processing_Order'] = '&nbsp; &nbsp; Processing';
	$actions['pwe_get_email_preview_Customer_Completed_Order']  = '&nbsp; &nbsp; Completed';
	$actions['pwe_get_email_preview_Customer_Refunded_Order']   = '&nbsp; &nbsp; Refunded';
	$actions['pwe_get_email_preview_Customer_Invoice']          = '&nbsp; &nbsp; Invoice';
	$actions['pwe_get_email_preview_Customer_Note']             = '&nbsp; &nbsp; Customer Note';
	$actions['pwe_get_email_preview_Customer_Reset_Password']   = '&nbsp; &nbsp; Reset Password';
	$actions['pwe_get_email_preview_Customer_New_Account']      = '&nbsp; &nbsp; New Account';
	
	$order_id = isset($_REQUEST['post']) ? $_REQUEST['post'] : get_the_ID();
	$key = get_post_meta( $order_id, '_order_key', true );
	$checkout_url = untrailingslashit(wc_get_checkout_url()) . '/order-received/'. $order_id .'/?key=' . $key;
	?>
<script type="text/javascript">
window.pwe = {
	receipt_page_url: <?php echo json_encode($checkout_url); ?>
};
</script>
<?php
	
	return $actions;
}
add_filter( 'woocommerce_order_actions', 'pwe_add_order_actions', 15 );

function pwe_return_html_and_exit( $html ) {
	$result = array( 'html' => $html );
	echo json_encode($result);
	exit;
}

function _pwe_extra_placeholders( $html, $Email ) {
	global $pwe_placeholders;
	
	$find    = array_keys( $pwe_placeholders );
	$replace = array_values( $pwe_placeholders );
	
	return str_replace( $find, $replace, $html );
}

function pwe_ajax_return_html_template( $mail_class = false ) {
	
	// Use a filter to identify that the the preview is running
	add_filter( 'rspwe_is-previewing-wc-email', '__return_true' );
	
	$order_id = isset($_REQUEST['order_id']) ? (int) $_REQUEST['order_id'] : false;
	$order = wc_get_order( absint( $order_id ) );
	
	if ( !$order_id || !$order || is_wp_error($order) || !is_a( $order, 'WC_Order' ) ) {
		pwe_return_html_and_exit( '<p><strong>Error: Provided order is invalid.</strong></p>' );
		exit;
	}
	
	/*
	From class-wc-emails.php
	
	$Email->emails['WC_Email_New_Order']                 = include 'emails/class-wc-email-new-order.php';
	$Email->emails['WC_Email_Cancelled_Order']           = include 'emails/class-wc-email-cancelled-order.php';
	$Email->emails['WC_Email_Failed_Order']              = include 'emails/class-wc-email-failed-order.php';
	$Email->emails['WC_Email_Customer_On_Hold_Order']    = include 'emails/class-wc-email-customer-on-hold-order.php';
	$Email->emails['WC_Email_Customer_Processing_Order'] = include 'emails/class-wc-email-customer-processing-order.php';
	$Email->emails['WC_Email_Customer_Completed_Order']  = include 'emails/class-wc-email-customer-completed-order.php';
	$Email->emails['WC_Email_Customer_Refunded_Order']   = include 'emails/class-wc-email-customer-refunded-order.php';
	$Email->emails['WC_Email_Customer_Invoice']          = include 'emails/class-wc-email-customer-invoice.php';
	$Email->emails['WC_Email_Customer_Note']             = include 'emails/class-wc-email-customer-note.php';
	$Email->emails['WC_Email_Customer_Reset_Password']   = include 'emails/class-wc-email-customer-reset-password.php';
	$Email->emails['WC_Email_Customer_New_Account']      = include 'emails/class-wc-email-customer-new-account.php';
	*/
	
	$user_id = $order->get_user_id();
	$user = $user_id ? get_user_by('id', $user_id) : false;
	
	// Set up WooCommerce objects
	WC()->payment_gateways();
	WC()->shipping();
	
	// Get the email object
	$Email = WC()->mailer()->emails[$mail_class];
	
	// Set up the email object
	$Email->setup_locale();
	$Email->object = $order;
	$Email->recipient = $order->get_billing_email();
	
	global $pwe_placeholders;
	
	$pwe_placeholders = array(
		'{order_date}' => wc_format_datetime( $order->get_date_created() ),
		'{order_number}' => $order->get_order_number(),
	);
	
	switch( $mail_class ) {
		case 'WC_Email_New_Order':
		case 'WC_Email_Cancelled_Order':
		case 'WC_Email_Failed_Order':
		case 'WC_Email_Customer_On_Hold_Order':
		case 'WC_Email_Customer_Processing_Order':
		case 'WC_Email_Customer_Completed_Order':
		case 'WC_Email_Customer_Refunded_Order':
		case 'WC_Email_Customer_Invoice':
			// No customizations for these
			break;
		
		case 'WC_Email_Customer_Note':
			$customer_note = "Testing - This is a new customer note!";
			$Email->recipient     = $order->get_billing_email();
			$Email->customer_note = $customer_note;
			break;
		
		case 'WC_Email_Customer_Reset_Password':
			if ( !$user_id ) {
				pwe_return_html_and_exit('<p><strong>Error: This email can only be sent if the customer had an account, but it appears the order was purchased by a guest.');
				exit;
			}
			
			$reset_key = "XXXXXXXXXXXXXXXXXXX";
			
			$Email->object     = $user;
			$Email->user_id    = $user_id;
			$Email->user_login = $user->get('user_login');
			$Email->reset_key  = $reset_key;
			$Email->user_email = stripslashes( $user->get('user_email') );
			$Email->recipient  = $Email->user_email;
			break;
		
		case 'WC_Email_Customer_New_Account':
			$Email->object     = $user;
			$user_pass = "XXXXXXXXXXXXXXXXXXX";
			$password_generated = "YYYYYYYYYYYYYYYYYY";
			$Email->user_pass          = $user_pass;
			$Email->user_login         = stripslashes( $user->user_login );
			$Email->user_email         = stripslashes( $user->user_email );
			$Email->recipient          = $Email->user_email;
			$Email->password_generated = $password_generated;
			break;
	}
	
	// We can't modify $Email->placeholders, so we have to filter placeholders ourselves. Note subject doesn't use this filter so we call it below too.
	add_filter( 'woocommerce_email_format_string', '_pwe_extra_placeholders', 20, 2 );
	
	// Get email text
	$to = $Email->get_recipient();
	$subject = $Email->format_string( _pwe_extra_placeholders($Email->get_option( 'subject', $Email->get_default_subject() ), $Email) ); // Customized because this doesn't fill properly: $Email->get_subject();
	$body = $Email->get_content();
	$headers = $Email->get_headers();
	$attachments = $Email->get_attachments();
	
	// Apply styles to the email
	$body = apply_filters( 'woocommerce_mail_content', $Email->style_inline( $body ) );
	
	// Remove <html><head> elements and unfold the <body>. This only return the contents from the <body>.
	$body = preg_replace('/.*?<body.*?>\s*/s', '', $body);
	$body = preg_replace('/\s*<\/body>.*/s', '', $body);
	
	// Output the popup content, which is captured in an output buffer
	ob_start();
?>
<p>
	<strong>To: </strong> <?php echo esc_html($to); ?><br>
	
	<strong>Subject: </strong> <?php echo esc_html($subject); ?><br>
	
	<strong>Headers: </strong> <?php if ( empty($headers) ) {
		echo '<br> &nbsp; &nbsp; <em>(None)</em>';
	} else {
		echo '<br> &nbsp; &nbsp; ' . str_replace(array("\r\n", "\r", "\n"), '<br> &nbsp; &nbsp; ', trim($headers));
	}
	?><br>
	
	<strong>Attachments: </strong> <?php if ( count($attachments) < 1 ) {
		echo '<br> &nbsp; &nbsp; <em>(None)</em>';
	} else {
		foreach( $attachments as $i => $attachment ) {
			echo '<br> &nbsp; &nbsp; ';
			echo 'Attachment [' . esc_html($i) . ']: ' . esc_html(print_r($attachment, true));
		}
	}
	?></p>

<p><strong>Body:</strong></p>

<div id="pwe-email-body"><?php echo $body; ?></div>
<?php
	
	// Return the HTML with our ajax function
	pwe_return_html_and_exit( ob_get_clean() );
	exit;
}