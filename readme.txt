=== Add to Cart links generator for WooCommerce ===
Contributors: damasovelazquez
Tags: woocommerce, cart, checkout, add to cart, direct link
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
WC requires at least: 5.0
WC tested up to: 9.0
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Generate quick links to add products to the cart and redirect to cart or checkout using custom parameters.

== Description ==

Generate custom links to add products to the WooCommerce cart and redirect to cart or checkout based on your configuration.

This plugin allows you to create direct "Add to Cart" links for your WooCommerce products with the following features:

* Generate direct links to add products to cart
* Choose redirect destination: cart page or checkout page
* Copy links directly from the product edit screen
* Configure default redirect behavior in WooCommerce settings
* Secure links with nonce verification
* Simple and lightweight implementation

Perfect for marketing campaigns, email newsletters, social media posts, or any situation where you want to provide a direct purchase link.

== Installation ==

1. Go to WordPress Dashboard > Plugins > Add New.
2. Search for "Add to Cart links generator for WooCommerce" in the plugin directory.
3. Click "Install Now" and then "Activate".
4. Make sure WooCommerce is active.
5. Configure default settings in WooCommerce > Settings > Cart Links.
6. Edit any product to find the link generator in the sidebar.

Alternatively, you can install manually:

1. Download the plugin from the WordPress repository
2. Upload the plugin folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes, WooCommerce must be installed and activated for this plugin to work.

= Can I use these links for variable products? =

Currently, the plugin works with simple products only. Variable products are not supported.

= Are the links secure? =

Yes, all generated links include nonce verification for security.

= Can I customize where the link redirects? =

Yes, you can choose between cart page or checkout page. Set a default in WooCommerce settings or specify it per link.

== Changelog ==

= 1.0.0 =
* Initial release.
* Generate add to cart links from product edit screen.
* Configure default redirect destination.
* Support for cart and checkout redirects.
* Nonce security verification.

== Screenshots ==

1. Settings page in WooCommerce settings
2. Link generator tool in product edit screen with copy buttons