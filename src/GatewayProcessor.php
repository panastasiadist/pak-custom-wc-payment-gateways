<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways;

use Exception;
use WC_Data_Store;
use WC_Order;
use WC_Order_Item;
use WC_Payment_Gateway;
use WC_Shipping_Zone;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\array_find;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\apply_plugin_filters;

class GatewayProcessor extends WC_Payment_Gateway {
	/**
	 * Default WooCommerce-native order status to use as the default value of the respective configuration option.
	 */
	const DEFAULT_ORDER_PLACED_STATUS = 'wc-on-hold';

	/**
	 * @var string The id of the gateway without the prefix, as configured in the configuration array.
	 */
	private string $config_id;

	/**
	 * @var array<string> Holds strings representing generic or zone-specific shipping methods for which the gateway should be available. It is empty when there should be no such restriction.
	 */
	private array $enable_for_shipping_methods;

	/**
	 * @var bool Whether the gateway should be available for orders containing virtual products only.
	 */
	private bool $enable_for_virtual_orders;

	/**
	 * @var string The text shown to the user as the instructions accompanying the gateway.
	 */
	private string $instructions;

	/**
	 * @var int The minimum order amount required for this gateway to be available. Set to zero when there should be no such restriction.
	 */
	private int $order_min_amount;

	/**
	 * @var int The maximum order amount required for this gateway to be available. Set to zero when there should be no such restriction.
	 */
	private int $order_max_amount;

	/**
	 * @var string The code of the status to set an order to as soon as it has been placed.
	 */
	private string $order_placed_status;

	/**
	 * Constructor
	 */
	public function __construct( string $id, string $method_title, string $method_description ) {
		$this->config_id          = $id;
		$this->has_fields         = false;
		$this->id                 = $id;
		$this->method_description = $method_description;
		$this->method_title       = $method_title;

		// Prepare configuration loading
		$this->description                 = $this->get_filtered_option( 'description', $method_description );
		$this->enable_for_shipping_methods = (array) $this->get_filtered_option( 'enable_for_shipping_methods', array() );
		$this->enable_for_virtual_orders   = $this->get_filtered_option( 'enable_for_virtual_orders', 'yes' ) === 'yes';
		$this->icon                        = $this->get_filtered_option( 'icon', '' );
		$this->instructions                = $this->get_filtered_option( 'instructions', $this->description );
		$this->order_max_amount            = absint( $this->get_filtered_option( 'order_max_amount', 0 ) );
		$this->order_min_amount            = absint( $this->get_filtered_option( 'order_min_amount', 0 ) );
		$this->order_placed_status         = $this->get_filtered_option( 'order_placed_status', self::DEFAULT_ORDER_PLACED_STATUS );
		$this->title                       = $this->get_filtered_option( 'title', $method_title );

		// Check that the stored order status is actually supported by the current WooCommerce installation.
		// Otherwise, set the order status to the default one.
		if ( ! in_array( $this->order_placed_status, array_keys( wc_get_order_statuses() ) ) ) {
			$this->order_placed_status = self::DEFAULT_ORDER_PLACED_STATUS;
		}

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Required for the configuration of the gateway to be saved by the system.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array(
			$this,
			'process_admin_options'
		) );

		// Output gateway's instructions on the thank-you page.
		add_action( 'woocommerce_thankyou_' . $this->id, fn() => $this->output_order_received_page_instructions() );

		// Include gateway's instructions in customer emails.
		add_action( 'woocommerce_email_before_order_table', fn( WC_Order $order, bool $sent_to_admin, $plain_text = false ) => $this->output_email_instructions( $order, $sent_to_admin, $plain_text ), 10, 3 );
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled'                     => array(
				'title'   => __( 'Enable/Disable', 'pak-custom-wc-payment-gateways' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this gateway', 'pak-custom-wc-payment-gateways' ),
				'default' => 'no',
			),
			'icon'                        => array(
				'title'       => __( 'Icon URL', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'safe_text',
				'description' => __( 'This controls the image which the user sees during checkout. Leave it blank to disable this feature.', 'pak-custom-wc-payment-gateways' ),
				'default'     => $this->icon,
				'desc_tip'    => true,
			),
			'title'                       => array(
				'title'       => __( 'Title', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'safe_text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'pak-custom-wc-payment-gateways' ),
				'default'     => $this->title,
				'desc_tip'    => true,
			),
			'description'                 => array(
				'title'       => __( 'Description', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'textarea',
				'description' => __( 'Payment method description that the customer will see on your checkout.', 'pak-custom-wc-payment-gateways' ),
				'default'     => $this->description,
				'desc_tip'    => true,
			),
			'instructions'                => array(
				'title'       => __( 'Instructions', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'textarea',
				'description' => __( 'Instructions that will be added to the thank you page and emails.', 'pak-custom-wc-payment-gateways' ),
				'default'     => $this->description,
				'desc_tip'    => true,
			),
			'order_placed_status'         => array(
				'title'       => __( 'Order Status', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'select',
				'description' => __( 'Order status to set when the checkout has been completed.', 'pak-custom-wc-payment-gateways' ),
				'default'     => $this->order_placed_status,
				'desc_tip'    => true,
				'options'     => wc_get_order_statuses(),
			),
			'order_min_amount'            => array(
				'title'             => __( "Order's Min Amount", 'pak-custom-wc-payment-gateways' ),
				'type'              => 'number',
				'description'       => __( 'The minimum amount of an order for the gateway to become available. Set it to 0 to disable the restriction.', 'pak-custom-wc-payment-gateways' ),
				'default'           => $this->order_min_amount,
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min' => 0,
				),
			),
			'order_max_amount'            => array(
				'title'             => __( "Order's Max Amount", 'pak-custom-wc-payment-gateways' ),
				'type'              => 'number',
				'description'       => __( 'The maximum amount of an order for the gateway to become available. Set it to 0 to disable the restriction.', 'pak-custom-wc-payment-gateways' ),
				'default'           => $this->order_max_amount,
				'desc_tip'          => true,
				'custom_attributes' => array(
					'min' => 0,
				),
			),
			'enable_for_shipping_methods' => array(
				'title'             => __( 'Enable for shipping methods', 'pak-custom-wc-payment-gateways' ),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select',
				'default'           => '',
				'description'       => __( 'Whether the gateway is available for specific shipping methods only. Leave it blank to enable the gateway for all methods.', 'pak-custom-wc-payment-gateways' ),
				'options'           => $this->get_shipping_method_options(),
				'desc_tip'          => true,
				'custom_attributes' => array(
					'data-placeholder' => __( 'Select shipping methods', 'pak-custom-wc-payment-gateways' ),
				),
			),
			'enable_for_virtual_orders'   => array(
				'title'       => __( 'Enable for virtual orders', 'pak-custom-wc-payment-gateways' ),
				'label'       => __( 'Enable the gateway for virtual orders', 'pak-custom-wc-payment-gateways' ),
				'description' => __( 'Whether the gateway is available for orders containing virtual products only.', 'pak-custom-wc-payment-gateways' ),
				'type'        => 'checkbox',
				'default'     => 'yes',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Returns whether the gateway should be available for usage.
	 *
	 * @return bool
	 */
	public function is_available(): bool {
		/**
		 * Array of functions that implement a check on whether the gateway should be available or not.
		 *
		 * @var callable[] $condition_fns
		 */
		$condition_fns = [
			fn() => 'yes' === $this->enabled,
			fn() => $this->are_shipping_conditions_met(),
		];

		if ( WC()->cart && 0 < $this->get_order_total() ) {
			$condition_fns[] = fn() => ! ( 0 < $this->order_min_amount ) || $this->get_order_total() >= $this->order_min_amount;
			$condition_fns[] = fn() => ! ( 0 < $this->order_max_amount ) || $this->get_order_total() <= $this->order_max_amount;
		}

		// Go through all functions implementing the different checks and early return on the first failure.
		foreach ( $condition_fns as $condition_fn ) {
			$is_available = $condition_fn();

			if ( ! $is_available ) {
				break;
			}
		}

		return (bool) $this->apply_plugin_filters( 'is_available', $is_available );
	}

	/**
	 * Process the payment and return the result.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array
	 */
	public function process_payment( $order_id ): array {
		$order = wc_get_order( $order_id );

		if ( $order->get_total() > 0 ) {
			$order->update_status( $this->order_placed_status );
		} else {
			$order->payment_complete();
		}

		// Payment is considered "complete". Empty the cart.
		WC()->cart->empty_cart();

		// Instruct the system to redirect to the "thank-you" page.
		return array(
			'result'   => 'success',
			'redirect' => $this->get_return_url( $order ),
		);
	}

	/**
	 * Apply two-form filters, generic and gateway-specific.
	 *
	 * @param string $name
	 * @param $value
	 * @param ...$params
	 *
	 * @return mixed|null
	 */
	private function apply_plugin_filters( string $name, $value, ...$params ) {
		$value = apply_plugin_filters( $this->config_id . '/' . $name, $value, ...$params );

		return apply_plugin_filters( $name, $value, ...[
			...$params,
			$this->config_id,
		] );
	}

	/**
	 * Considers the shipping details and returns whether the gateway should be available.
	 *
	 * @return bool
	 */
	private function are_shipping_conditions_met(): bool {
		$is_shipping_needed = WC()->cart && WC()->cart->needs_shipping();

		if ( ! $is_shipping_needed && is_checkout() && 0 < get_query_var( 'order-pay' ) ) {
			$order              = wc_get_order( absint( get_query_var( 'order-pay' ) ) );
			$is_shipping_needed = $order && null !== array_find( $order->get_items(), fn( WC_Order_Item $item ) => $item->get_product() ? $item->get_product()->needs_shipping() : false );
		}

		$is_shipping_needed = apply_filters( 'woocommerce_cart_needs_shipping', $is_shipping_needed );

		// If shipping is not needed, then this is an order containing virtual products only.
		// The decision on whether the gateway should be available or not, depends on its settings regarding virtual orders.
		if ( ! $is_shipping_needed ) {
			return $this->enable_for_virtual_orders;
		}

		// We are here because shipping is needed, so this order is not a virtual one.
		// If the gateway is configured to be available only for specific shipping methods, make sure that the chosen shipping method is within the gateway's allowed ones.
		// Otherwise, early return, as the gateway is available for every shipping method.
		if ( empty( $this->enable_for_shipping_methods ) ) {
			return true;
		}

		$rates = isset( $order ) ? $order->get_shipping_methods() : array();

		if ( empty( $rates ) ) {
			$packages = WC()->shipping()->get_packages();
			$methods  = WC()->session->get( 'chosen_shipping_methods' ) ?: array();

			foreach ( $methods as $key => $rate_id ) {
				if ( $rate = $packages[ $key ]['rates'][ $rate_id ] ?? null ) {
					$rates[] = $rate;
				}
			}
		}

		$canonical_rate_ids = [
			// Account for generic, zone-independent shipping methods.
			...array_map( fn( $item ) => $item->get_method_id(), $rates ),
			// Account for zone-dependent shipping methods.
			...array_map( fn( $item ) => $item->get_method_id() . ':' . $item->get_instance_id(), $rates )
		];

		return ! empty( array_intersect( $this->enable_for_shipping_methods, $canonical_rate_ids ) );
	}

	/**
	 * Returns a stored setting, after having given third-party code the chance to modify it.
	 *
	 * @param string $key
	 * @param $empty_value
	 *
	 * @return mixed|string|null
	 */
	private function get_filtered_option( string $key, $empty_value = null ) {
		// Filters which modify an option are meant to override the settings configured by the administrator.
		// Therefore, avoid calling filters when configuring a gateway.
		if ( $this->is_accessing_settings() ) {
			return $this->get_option( $key, $empty_value );
		}

		return $this->apply_plugin_filters( 'settings/' . $key, $this->get_option( $key, $empty_value ) );
	}

	/**
	 * Returns an associative array of all generic and zone-dependent shipping methods.
	 *
	 * @return array<string, string> The keys and values represent the ids and titles of the shipping methods, respectively.
	 */
	private function get_shipping_method_options(): array {
		// Since this is expensive, we only want to do it if we're actually on the settings page.
		if ( ! $this->is_accessing_settings() ) {
			return array();
		}

		foreach ( WC()->shipping()->load_shipping_methods() as $method ) {
			/* translators: 1: Shipping method title */
			$options[ $method->get_rate_id() ] = sprintf( __( 'Any &quot;%1$s&quot; method', 'pak-custom-wc-payment-gateways' ), $method->get_method_title() );
		}

		try {
			$store = WC_Data_Store::load( 'shipping-zone' );
			$zones = array_map( fn( $raw_zone ) => new WC_Shipping_Zone( $raw_zone ), $store->get_zones() );
		} catch ( Exception $e ) {
		}

		$zones[] = new WC_Shipping_Zone( 0 ); // This is the "Other locations" zone

		foreach ( $zones as $zone ) {
			foreach ( $zone->get_shipping_methods() as $method ) {
				/* translators: 1: Shipping method title 2: Shipping instance id */
				$option_instance_title = sprintf( __( '%1$s (#%2$s)', 'pak-custom-wc-payment-gateways' ), $method->get_title(), $method->get_instance_id() );
				/* translators: 1: Shipping zone title 2: Shipping method title and instance id */
				$option_title                      = sprintf( __( '%1$s &ndash; %2$s', 'pak-custom-wc-payment-gateways' ), $zone->get_id() ? $zone->get_zone_name() : __( 'Other locations', 'pak-custom-wc-payment-gateways' ), $option_instance_title );
				$options[ $method->get_rate_id() ] = $option_title;
			}
		}

		return $options ?? array();
	}

	/**
	 * Checks to see whether the admin settings are being accessed by the current request.
	 *
	 * @return bool
	 */
	private function is_accessing_settings(): bool {
		if ( defined( 'REST_REQUEST' ) ) {
			global $wp;
			if ( isset( $wp->query_vars['rest_route'] ) && false !== strpos( $wp->query_vars['rest_route'], '/payment_gateways' ) ) {
				return true;
			}
		}

		if ( ! is_admin() ) {
			return false;
		}

		/* phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended	 */

		if ( 'wc-settings' !== ( $_REQUEST['page'] ?? '' ) ||
		     'checkout' !== ( $_REQUEST['tab'] ?? '' ) ||
		     $this->id !== ( $_REQUEST['section'] ?? '' ) ) {
			return false;
		}

		/* phpcs:enable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.NonceVerification.Recommended	 */

		return true;
	}

	/**
	 * Include the gateway's instructions in the WooCommerce emails.
	 *
	 * @param WC_Order $order The object of the order for which the email will be sent.
	 * @param bool $is_sent_to_admin Whether the email is intended for the administrator.
	 * @param bool $is_plain_text Whether the email's format is plain or rich text.
	 */
	private function output_email_instructions( WC_Order $order, bool $is_sent_to_admin, bool $is_plain_text = false ) {
		// Instructions should be included only in emails which are the result of orders which have been placed using the gateway.
		if ( $this->id !== $order->get_payment_method() ) {
			return;
		}

		// By default, instructions should be emailed for customer-targeting emails and for orders which are considered non paid.
		$send_email = $this->instructions && ( ! $is_sent_to_admin ) && ( ! $order->is_paid() );

		// Give third party code the chance of deciding on whether the instructions should be sent.
		if ( ! $this->apply_plugin_filters( 'send_email_instructions', $send_email, $order ) ) {
			return;
		}

		// We've made it till here. Include the instructions in the email.

		if ( ! $is_plain_text ) {
			echo '<div class="' . esc_attr( $this->id ) . '-instructions">';
		}

		echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) . PHP_EOL );

		if ( ! $is_plain_text ) {
			echo '</div>';
		}
	}

	/**
	 * Output the gateway's instructions on the order received page.
	 */
	private function output_order_received_page_instructions() {
		if ( ! $this->instructions ) {
			// There are no instructions to output. Early return.
			return;
		}

		echo '<div class="' . esc_attr( $this->id ) . '-instructions">';
		echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		echo '</div>';
	}
}
