<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities;

use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Represents the outcome of an operation.
 */
class Result extends Entity {
	/**
	 * A type of result signaling how the result should be treated by its consumers.
	 */
	public const CODE_FAILURE = 'failure';

	/**
	 * A type of result signaling how the result should be treated by its consumers.
	 */
	public const CODE_SUCCESS = 'success';

	/**
	 * @var string The result's code. Must be equal to one of the exposed constants.
	 */
	private string $code;

	/**
	 * @var mixed|null Any data accompanying the result.
	 */
	private $data;

	/**
	 * Constructor
	 *
	 * @param string $code The result's code. Must be equal to one of the exposed constants.
	 * @param mixed $data Any data accompanying the result.
	 *
	 * @throws Exception When the provided code is not one of the supported constants.
	 */
	public function __construct( string $code, $data = null ) {
		if ( ! in_array( $code, [ self::CODE_FAILURE, self::CODE_SUCCESS ] ) ) {
			throw new Exception( 'Invalid code provided' );
		}

		$this->code = $code;
		$this->data = $data;
	}

	/**
	 * Returns the code of the result.
	 *
	 * @return string
	 */
	public function get_code(): string {
		return $this->code;
	}

	/**
	 * Returns any (possibly null) data accompanying the result.
	 *
	 * @return mixed|null
	 */
	public function get_data() {
		return $this->data;
	}
}
