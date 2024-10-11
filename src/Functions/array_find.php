<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions;

/**
 * User-land implementation of the (as of 2024-08-06) upcoming implementation of the array_find function in PHP 8.4.
 *
 * @param array $array The array that should be searched.
 * @param callable $callback The callback function to call to check each element. The first parameter contains the value, the second parameter contains the corresponding key. If this function returns true, the value is returned from array_find and the callback will not be called for further elements.
 *
 * @return mixed|null The value of the first element for which the $callback returns true. If no matching element is found NULL is returned.
 *
 * @see https://wiki.php.net/rfc/array_find
 */
function array_find( array $array, callable $callback ) {
	// If the function is natively supported by the current PHP runtime, use it instead of our custom implementation.
	if ( function_exists( '\array_find' ) ) {
		return \array_find( $array, $callback );
	}

	foreach ( $array as $key => $value ) {
		if ( $callback( $value, $key ) ) {
			return $value;
		}
	}

	return null;
}
