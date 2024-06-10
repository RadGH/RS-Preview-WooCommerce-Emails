jQuery(function() {
	// #post = legacy orders page form
	// form[name="order"] = new orders page form with block editor
	var $post = jQuery('#post, form[name="order"]').first();
	
	// Order Actions dropdown
	var $action_select = jQuery('#actions').find('select[name="wc_order_action"]');

	// Show an error if a require element isn't available. This script should only load on the edit order page, so something is wrong if this happens.
	if ( $post.length < 1 || $action_select.length < 1 ) {
		console.log("[A+A Preview WooCommerce Emails] Warning - Some required WooCommerce elements were not found. This script will not function.");
		return;
	}

	// Create a container that we will open in the lightbox. Once the popup is shown, we can't add to it.
	var $lightbox_container = jQuery('<div>').attr('id', 'pwe_lightbox_container').css('display', 'none');

	// Create a child div that we can edit even while the popup is visible.
	var $lightbox_content = jQuery('<div>').attr('id', 'pwe_lightbox_content');
	
	// Add our new divs.
	$lightbox_container.append( $lightbox_content );
	$post.append( $lightbox_container );

	// Performs an AJAX request and loads the resulting HTML into the popup. An example ajax_action is "New_Order".
	var pwe_load_to_popup = function( ajax_action ) {
		var url = ajaxurl + "?action=pwe_get_email_preview_" + ajax_action;
		var data = {
			order_id: jQuery('#post_ID').val()
		};

		jQuery.getJSON(
			url,
			data,
			function( data ) {
				if ( typeof data.html === "undefined" ) {
					$lightbox_content.html( "Error: Invalid response, expected data.html property." );
				}else{
					$lightbox_content.html( data.html );
				}
			}
		).fail(function() {
			$lightbox_content.html( "Error: Invalid response, expected json data." );
		});
	};

	// Opens a popup with a proper title, which then triggers an ajax request to fill the content.
	var pwe_preview_email = function( action ) {
		var title = 'Email preview';

		switch( action ) {
			case 'New_Order':
				title = '[Admin] New Order';
				break;
			case 'Cancelled_Order':
				title = '[Admin] Cancelled Order';
				break;
			case 'Failed_Order':
				title = '[Admin] Failed Order';
				break;
			case 'Customer_On_Hold_Order':
				title = 'On Hold';
				break;
			case 'Customer_Processing_Order':
				title = 'Processing';
				break;
			case 'Customer_Completed_Order':
				title = 'Completed';
				break;
			case 'Customer_Refunded_Order':
				title = 'Refunded';
				break;
			case 'Customer_Invoice':
				title = 'Invoice';
				break;
			case 'Customer_Note':
				title = 'Customer Note';
				break;
			case 'Customer_Reset_Password':
				title = 'Reset Password';
				break;
			case 'Customer_New_Account':
				title = 'New Account';
				break;
		}

		// Update the popup content to say Loading, while the ajax call is processed.
		$lightbox_content.html('<p><em>Loading&hellip;</em></p>').css('display', 'block');

		// Show the lightbox with Thickbox.
		var width = Math.min( 750, jQuery(window).width() * 0.9 );
		var height = jQuery(window).height() * 0.9;
		tb_show( title, "#TB_inline?inlineId=pwe_lightbox_container&width="+ width +"&height=" + height );

		// Do the ajax request which will fill the popup upon completion.
		pwe_load_to_popup( action );
	};

	// When submitting the order form, capture our events and do them instead of saving the order.
	// Unlike normal order actions, ours do not save the order.
	$post.on('submit', function() {
		var action = $action_select.val();

		if ( action === 'pwe_visit_receipt_page' ) {
			window.open( window.pwe.receipt_page_url, '_blank' );
			return false;
		}

		if ( action.indexOf('pwe_get_email_preview_') === 0 ) {
			pwe_preview_email( action.replace('pwe_get_email_preview_', '') );
			return false;
		}

		if ( action.indexOf('pwe_newtab_get_email_preview_') === 0 ) {
			var preview_action = action.replace('pwe_newtab_get_email_preview_', '');
			var order_id = window.pwe.order_id;
			window.open( window.pwe.preview_email_url + '&pwe_preview_template='+ preview_action + '&order_id=' + order_id , '_blank' );
			return false;
		}

		if ( action === 'pwe_empty' || action === 'pwe_separator' ) {
			return false;
		}
	});

	// Log message that it is ready
	console.log("[A+A Preview WooCommerce Emails] Ready.", $post[0], $action_select[0]);

});