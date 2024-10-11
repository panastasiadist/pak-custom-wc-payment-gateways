<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Services;

use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\ksort_recursively;

/**
 * Class that provides facilities for value generation and memoization.
 */
class Memoizer {
	/**
	 * @var array<string, mixed> Associated array holding keys to memoized values.
	 */
	private array $bag = array();

	/**
	 * Returns a memoized value based on the provided key.
	 * If a value does not exist for the given key, the calculator function is called for a value to be calculated.
	 *
	 * @template T
	 *
	 * @param mixed $key
	 * @param callable(): T $fn_calculator
	 *
	 * @return T
	 */
	public function get( $key, callable $fn_calculator ) {
		$keys = is_array( $key ) ? $key : array( $key );
		ksort_recursively( $keys );
		$key = serialize( $key );

		if ( isset( $this->bag[ $key ] ) ) {
			return $this->bag[ $key ];
		}

		$value             = $fn_calculator();
		$this->bag[ $key ] = $value;

		return $value;
	}
}
