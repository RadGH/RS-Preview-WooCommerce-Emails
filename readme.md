RS Preview WooCommerce Emails (WordPress Plugin)
==

This plugin adds the ability to preview all WooCommerce order emails in a popup without sending an email. Accessible through the "Order Actions" dropdown when editing an order.

A bonus option includes the link to view a customer's "Completed Order" page - the same they see after they complete a purchase.

## Installation ##

1. Upload the plugin files to `/wp-content/plugins/rs-preview-woocommerce-emails/`, or install the plugin through the WordPress plugins screen.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Edit a WooCommerce order and find the new options in the Order Actions dropdown.
4. Select an order action and click the arrow, or submit button, to view the email in a popup. No email will be sent.
5. Note that you will have to change the order action dropdown back to the default in order to save an order.

## Frequently Asked Questions ##

**Are these email previews identical to what you would receive in your inbox?**

They are very close, but not exact. There are a few small downsides:

1. Some styles applied to the admin will apply in the popup. Most importantly, the fonts in the email preview may inherit from the dashboard and would not show in an email.
2. Some fields like the password reset key, new password, and other generated content are filled with dummy text such as XXXXXX.
3. Plugins that modify emails may not work properly in the popup. Although the popup uses official WooCommerce methods to generate content, it does not run any of the email filters which other plugins might otherwise hook into.

## Screenshots ##

![The new options added to the Order Actions dropdown.](screenshot-1.png)
![An example of the Email Preview shown in a popup.](screenshot-2.png)


## Changelog ##

#### 1.2.3
* Updated to support WooCommerce 8.9.3

#### 1.2.1
* Added Git Updater support, allowing this plugin to automatically receive updates if installed.

#### 1.0
* First release

## Upgrade Notice ##

None yet.