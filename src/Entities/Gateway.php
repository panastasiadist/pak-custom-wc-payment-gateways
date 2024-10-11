<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities;

use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Acts as a bag of properties describing a gateway created by the user.
 */
class Gateway extends Entity {
	/**
	 * @var string The default description of the gateway as appearing in the WooCommence administration screen.
	 */
	private string $description;

	/**
	 * @var string The unique id of the gateway in WooCommerce.
	 */
	private string $id;

	/**
	 * @var string The default title of the gateway as appearing in the WooCommence administration screen.
	 */
	private string $title;

	/**
	 * Constructor
	 *
	 * @throws Exception When the provided arguments are invalid.
	 */
	public function __construct( string $id, string $title, string $description ) {
		if ( ! ( $id && $title && $description ) ) {
			throw new Exception( 'At least one of the provided parameters is empty' );
		}

		$this->description = $description;
		$this->id          = $id;
		$this->title       = $title;
	}

	/**
	 * Builds a Gateway object by examining the provided data array for valid key-value pairs.
	 *
	 * @throws Exception When unable to build a Gateway instance from the data contained in the provided array.
	 */
	public static function from_array( array $data ): Gateway {
		return new Gateway( $data['id'] ?? '', $data['title'] ?? '', $data['description'] ?? '' );
	}

	/**
	 * Return the description of the instance.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Return the id of the instance.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * Return the title of the instance.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}

	/**
	 * Sets the description of the instance.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function set_description( string $value ): self {
		$this->description = $value;

		return $this;
	}

	/**
	 * Sets the title of the instance.
	 *
	 * @param string $value
	 *
	 * @return $this
	 */
	public function set_title( string $value ): self {
		$this->title = $value;

		return $this;
	}

	/**
	 * Updates the instance's data with the data found in the provided Gateway instance.
	 *
	 * @param Gateway $gateway
	 *
	 * @return $this
	 */
	public function update_from_same( Gateway $gateway ): self {
		return $this
			->set_title( $gateway->title )
			->set_description( $gateway->description );
	}
}
