<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions;

/**
 * Converts the provided string to snake_case and returns it.
 *
 * @param string $string
 *
 * @return string
 */
function string_to_snake_case( string $string ): string {
	$result = '';

	foreach ( str_split( $string ) as $character ) {
		$result .= ctype_upper( $character ) ? '_' . strtolower( $character ) : $character;
	}

	return ltrim( $result, '_' );
}
