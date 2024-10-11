<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Traits;

use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\string_to_snake_case;

/**
 * Implementation of a method which generates an id based on the class name it is part of.
 */
trait AutomaticId {
	/**
	 * Returns an id derived from the non-fully-qualified class name.
	 *
	 * @return string
	 */
	public static function get_id(): string {
		$class_name_parts = explode( '\\', get_called_class() );
		$class_name       = end( $class_name_parts );

		return string_to_snake_case( $class_name );
	}
}
