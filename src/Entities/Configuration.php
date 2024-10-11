<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities;

use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Acts as a bag of properties describing the total configuration of the plugin.
 */
class Configuration extends Entity {
	/**
	 * @var Gateway[] Holds an array of Gateway instances corresponding to the gateways that have been created for the website.
	 */
	private array $gateways = array();

	/**
	 * Builds a Configuration object by examining the provided data array for valid key-value pairs.
	 *
	 * @param array $data
	 *
	 * @return Configuration
	 */
	public static function from_array( array $data ): Configuration {
		$configuration = new Configuration();

		foreach ( ( $data['gateways'] ?? array() ) as $parameters ) {
			try {
				$configuration->gateways[] = Gateway::from_array( $parameters );
			} catch ( Exception $exception ) {
			}
		}

		return $configuration;
	}

	/**
	 * Return the gateways of the instance.
	 *
	 * @return Gateway[]
	 */
	public function get_gateways(): array {
		return $this->gateways;
	}

	/**
	 * Set the gateways of the instance.
	 *
	 * @param Gateway[] $gateways
	 *
	 * @return $this
	 */
	public function set_gateways( array $gateways ): self {
		$this->gateways = $gateways;

		return $this;
	}
}
