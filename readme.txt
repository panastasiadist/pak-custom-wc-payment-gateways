=== PAK Custom Payment Gateways for WooCommerce ===
Contributors: panastasiadist
Tags: woocommerce, payments, gateways
Stable tag: 1.0.0
Requires PHP: 7.4
Requires at least: 5.3
Tested up to: 6.6
WC requires at least: 4.5
WC tested up to: 9.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Easily add simple, yet customizable payment gateways to WooCommerce.

== Description ==

Easily add simple, yet customizable payment gateways to WooCommerce.

== Features ==

* Creation of an arbitrary number of custom payment gateways (payment methods).
* Per-gateway configuration of title, description, instructions, and icon.
* Per-gateway availability configuration depending on an order's minimum and/or maximum amount.
* Per-gateway availability configuration depending on whether an order is virtual.
* Per-gateway availability configuration depending on the shipping zone and method chosen by the customer.
* Per-gateway ability to configure an order's status after it has been placed.
* Numerous hooks supported for easy programmatic extension and fine-tuning to a website's specific needs.
* Carefully coded and lightweight, yet extensible and powerful.

== How to use ==

1. Log in your WordPress administration site.
2. Install the plugin.
3. Navigate to **Settings -> PAK Custom Payment Gateways for WooCommerce**.
4. Click on the **New Gateway** button to create a new custom gateway. A dialog will appear.
5. Specify the (default) title and description of the new gateway.
6. Click on the **Create** button to save the new gateway and close the dialog.
7. The new gateway should appear in the list.
8. Navigate to **WooCommerce -> Settings -> Payments**.
9. You should now see the newly added gateway.
10. Access the settings page for each gateway and configure it as necessary.
11. Alternatively, click on the **Configure** icon/button appearing within the row corresponding to the new gateway. This will directly take you to the gateway's configuration page.

== Guide ==

Consult the **README** file available on [GitHub](https://github.com/panastasiadist/pak-custom-wc-payment-gateways/blob/main/README.md).

== Frequently Asked Questions ==

= What is the purpose of the plugin?
It enables creating new gateways which are made available in the **WooCommerce -> Settings -> Payments** configuration page and are able to have their title, description, instructions, and conditions of availability.

= Does the plugin logs or shares personal data?
No, not at all. However, the plugin contains links to third-party websites and users are responsible for consenting to use of their data by these systems.

= Does this plugin allow me to use third-party payment processors such as PayPal or Stripe?
No, out of the box, as it is meant to be versatile and not restricted to specific payment processors. Multiple hooks are supported by the plugin as a means of further extending it, making it able to act as a framework for a more advanced, custom payment gateway solution.

= I believe that I've found a problem. Can I report it?
You are very welcome to do so! Just follow this [link](https://wordpress.org/support/plugin/pak-custom-wc-payment-gateways/)

= Can I participate in some way?
Your contribution is important. You could translate the plugin in your own language, for example by using Loco Translate, or contribute with code on [GitHub](https://github.com/panastasiadist/pak-custom-wc-payment-gateways). Either way, your full name and your email will be logged in the plugin's information and on GitHub.

= Are any customization services offered?
You may contact the plugin's author at [anastasiadis.me](https://anastasiadis.me).

== Screenshots ==

1. Plugin's main configuration page.
2. Gateway creation dialog.
3. Gateway update dialog.
4. WooCommerce-based gateway configuration page.

== Source Code ==

* You may view the complete source code of this plugin on its [Official GitHub Repository](https://github.com/panastasiadist/pak-custom-wc-payment-gateways).
* The "admin/settings" directory of the plugin's release on WordPress.org contains the compiled and optimized files implementing the settings page of the plugin. These files are the result of a NPM/Node/Vite/Vue based project, the source files of which are available on the plugin's [Official GitHub Repository](https://github.com/panastasiadist/pak-custom-wc-payment-gateways).

== Changelog ==

= 1.0.0 =
* First release.
