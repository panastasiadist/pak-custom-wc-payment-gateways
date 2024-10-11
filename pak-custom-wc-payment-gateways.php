<?php

/**
 * PAK Custom Payment Gateways for WooCommerce
 *
 * Plugin Name:          PAK Custom Payment Gateways for WooCommerce
 * Description:          Easily add simple, yet customizable payment gateways to WooCommerce.
 * Version:              1.0.0
 * Author:               Panagiotis (Panos) Anastasiadis
 * Author URI:           https://anastasiadis.me
 * License:              GPLv2 or later
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          pak-custom-wc-payment-gateways
 * Domain Path:          /languages
 * Requires PHP:         7.4
 * Requires at least:    5.3
 * Tested up to:         6.6
 * WC requires at least: 4.5
 * WC tested up to:      9.3
 */

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/vendor/autoload.php';

register_activation_hook( __FILE__, 'pak_custom_wc_payment_gateways_activate' );
register_uninstall_hook( __FILE__, 'pak_custom_wc_payment_gateways_uninstall' );

function pak_custom_wc_payment_gateways_activate() {
	Core::install( __FILE__ );
}

function pak_custom_wc_payment_gateways_uninstall() {
	Core::uninstall();
}

add_action( 'init', function () {
	load_plugin_textdomain( 'pak-custom-wc-payment-gateways', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

add_action( 'before_woocommerce_init', function () {
	if ( class_exists( FeaturesUtil::class ) ) {
		FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

Core::instance()->initialize( __FILE__ );
