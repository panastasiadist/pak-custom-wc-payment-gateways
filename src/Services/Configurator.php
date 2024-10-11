<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Services;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Constants;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Configuration;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Gateway;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\array_find;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\apply_plugin_filters;

/**
 * Class that handles everything regarding the storage and retrieval of the plugin's settings.
 */
class Configurator {
	/**
	 * Deletes a gateway given its unique id.
	 *
	 * @param string $gateway_id
	 *
	 * @return bool Whether the operation is successful.
	 */
	public function delete_gateway( string $gateway_id ): bool {
		$new_gateways = array_filter( $this->get_configuration()->get_gateways(), fn( Gateway $item ) => $item->get_id() !== $gateway_id );

		return $this->set_gateways( $new_gateways );
	}

	/**
	 * Returns a string the ids of the gateways registered by the plugin should be prefixed with.
	 *
	 * @return string
	 */
	public static function get_gateway_id_prefix(): string {
		return 'pakcwcpg_';
	}

	/**
	 * Returns a Configuration instance containing the plugin's settings.
	 *
	 * @return Configuration
	 */
	public function get_configuration(): Configuration {
		$options = get_option( Constants::WP_OPTIONS_KEY_CONFIG, null );

		if ( ! is_array( $options ) ) {
			$this->set_configuration( new Configuration() );

			return $this->get_configuration();
		}

		return apply_plugin_filters( 'configuration', Configuration::from_array( $options ) );
	}

	/**
	 * Deletes the stored configuration.
	 *
	 * @return void
	 */
	public static function reset() {
		delete_option( Constants::WP_OPTIONS_KEY_CONFIG );
	}

	/**
	 * Creates or updates (depending on whether the id is already existent or not) the provided gateway.
	 *
	 * @param Gateway $gateway
	 *
	 * @return bool Whether the operation is successful.
	 */
	public function save_gateway( Gateway $gateway ): bool {
		$current_gateways = $this->get_configuration()->get_gateways();

		/**
		 * @var Gateway|null $current_gateway
		 */
		$current_gateway = array_find( $current_gateways, fn( Gateway $item ) => $item->get_id() === $gateway->get_id() );

		if ( $current_gateway ) {
			$current_gateway->update_from_same( $gateway );
		} else {
			$current_gateways[] = $gateway;
		}

		return $this->set_gateways( $current_gateways );
	}

	/**
	 * Serialize and save in the database the settings contained in the provided Configuration instance.
	 *
	 * @param Configuration $configuration
	 *
	 * @return bool Whether the operation is successful.
	 */
	private function set_configuration( Configuration $configuration ): bool {
		$data = $configuration->to_array();

		return update_option( Constants::WP_OPTIONS_KEY_CONFIG, $data, true );
	}

	/**
	 * Completely replaces all saved gateways with the provided ones.
	 *
	 * @param Gateway[] $gateways
	 *
	 * @return void
	 */
	private function set_gateways( array $gateways ): bool {
		$configuration = $this->get_configuration();

		foreach ( $gateways as $gateway ) {
			if ( mb_strpos( $gateway->get_id(), self::get_gateway_id_prefix() ) !== 0 ) {
				return false;
			}
		}

		$gateways_current_ids = array_map( fn( Gateway $gateway ) => $gateway->get_id(), $configuration->get_gateways() );
		$gateways_updated_ids = array_map( fn( Gateway $gateway ) => $gateway->get_id(), $gateways );
		$gateways_deleted_ids = array_diff( $gateways_current_ids, $gateways_updated_ids );

		foreach ( $gateways_deleted_ids as $gateway_id ) {
			$gateway_settings_option = "woocommerce_{$gateway_id}_settings";

			if ( false !== get_option( $gateway_settings_option ) ) {
				delete_option( $gateway_settings_option );
			}
		}

		$configuration->set_gateways( $gateways );

		return $this->set_configuration( $configuration );
	}
}
