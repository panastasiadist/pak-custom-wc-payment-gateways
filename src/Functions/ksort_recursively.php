<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions;

/**
 * Recursively sort an array by key.
 *
 * @param array $array The input array.
 * @param int $flags You may modify the behavior of the sort using the optional parameter sort_flags, for details see sort.
 *
 * @return void
 */
function ksort_recursively( array &$array, int $flags = SORT_REGULAR ) {
	ksort( $array );

	foreach ( $array as &$value ) {
		if ( is_array( $value ) ) {
			ksort_recursively( $value, $flags );
		}
	}
}
