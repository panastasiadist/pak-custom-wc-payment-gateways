<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways;

use WC_Payment_Gateway;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\SystemParameterBag;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Configurator;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Logger;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Memoizer;

/**
 * The main class coordinating the plugin's subsystems.
 */
class Core {
	/**
	 * @var string The absolute file system path to the main plugin file.
	 */
	private string $main_plugin_file_path = '';

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return self
	 */
	public static function instance(): self {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	/**
	 * Execute initialization procedures, useful for the proper functioning of the plugin's subsystems.
	 *
	 * @param string $main_plugin_file_path The absolute file system path to the main plugin file.
	 *
	 * @return void
	 */
	public function initialize( string $main_plugin_file_path ) {
		static $initialized = false;

		if ( $initialized ) {
			return;
		}

		$this->main_plugin_file_path = $main_plugin_file_path;

		add_action( 'woocommerce_payment_gateways', fn( array $methods ) => $this->load_configured_gateways( $methods ) );

		if ( is_admin() ) {
			new Admin\Provider();
		}

		$initialized = true;
	}

	/**
	 * Returns a singleton instance of the requested service.
	 *
	 * @template T
	 * @param class-string<T> $class_name
	 *
	 * @return T
	 */
	public function get_service( string $class_name ) {
		if ( $class_name === Memoizer::class ) {
			return $this->get_memoizer();
		}

		return $this->get_memoizer()->get( $class_name, fn() => new $class_name() );
	}

	/**
	 * Returns the absolute file system path to the main plugin file.
	 *
	 * @return string
	 */
	public function get_main_plugin_file_path(): string {
		return $this->main_plugin_file_path;
	}

	/**
	 * Returns an object containing parameters which configure the inner-workings of the plugin.
	 *
	 * @return SystemParameterBag
	 */
	public function get_parameters(): SystemParameterBag {
		return $this->get_memoizer()->get( SystemParameterBag::class, fn() => new SystemParameterBag(
			defined( Constants::SYSTEM_PARAMETERS_CONSTANT ) ?
				constant( Constants::SYSTEM_PARAMETERS_CONSTANT ) :
				array()
		) );
	}

	/**
	 * Constructor
	 */
	private function __construct() {
	}

	/**
	 * Returns a Memoizer instance used to return values cached for the lifetime of the request.
	 *
	 * @return Memoizer
	 */
	private function get_memoizer(): Memoizer {
		static $instance = null;

		if ( is_null( $instance ) ) {
			$instance = new Memoizer();
		}

		return $instance;
	}

	/**
	 * Augments the provided array of gateway processors with additional ones, constructed according to the plugin's settings.
	 *
	 * @param WC_Payment_Gateway[] $methods
	 *
	 * @return WC_Payment_Gateway[]
	 */
	private function load_configured_gateways( array $methods ): array {
		// Add new gateways according to the configuration.

		$configurator = $this->get_service( Configurator::class );

		foreach ( $configurator->get_configuration()->get_gateways() as $gateway ) {
			$id          = $gateway->get_id();
			$title       = $gateway->get_title();
			$description = $gateway->get_description();

			if ( ! ( $id && $title && $description ) ) {
				continue;
			}

			$methods[] = new GatewayProcessor( $id, $title, $description );
		}

		return $methods;
	}

	/**
	 * Executes plugin activation procedures.
	 *
	 * @param string $plugin_file_path The absolute filesystem path to the main plugin file.
	 *
	 * @return void
	 */
	public static function install( string $plugin_file_path ) {
		$data = get_file_data( $plugin_file_path, [ 'Version' => 'Version' ] );
		update_option( Constants::WP_OPTIONS_KEY_VERSION, $data['Version'], false );

		Logger::install();
	}

	/**
	 * Executes plugin uninstallation procedures.
	 *
	 * @return void
	 */
	public static function uninstall() {
		Configurator::reset();
		Logger::uninstall();

		delete_option( Constants::WP_OPTIONS_KEY_VERSION );
	}
}
