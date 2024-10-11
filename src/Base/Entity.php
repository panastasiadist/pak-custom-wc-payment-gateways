<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Base;

use ReflectionClass;
use ReflectionMethod;

/**
 * Acts as a basis for objects containing properties which describe an entity in the system.
 */
class Entity {
	/**
	 * Transforms all public properties to an array and returns the array.
	 *
	 * @return array
	 */
	public function to_array(): array {
		$data       = array();
		$reflection = new ReflectionClass( $this );
		$properties = $reflection->getProperties();
		$methods    = $reflection->getMethods( ReflectionMethod::IS_PUBLIC );

		/**
		 * @var array<string, ReflectionMethod> $method_name_to_method
		 */
		$method_name_to_method = array_reduce( $methods, function ( $acc, ReflectionMethod $method ) {
			$acc[ $method->name ] = $method;

			return $acc;
		}, [] );

		foreach ( $properties as $property ) {
			if ( $getter_method = $method_name_to_method[ 'get_' . $property->name ] ?? null ) {
				$this->process_value( $data, $getter_method->invoke( $this ), $property->getName() );
			}
		}

		return $data;
	}

	/**
	 * Fills the provided array with data which results from processing the provided value.
	 *
	 * @param array $data The array to update with the result derived from processing the provided value.
	 * @param mixed $value A value to process. The result will be a scalar or array value.
	 * @param string $name The array key to use when updating the provided array.
	 */
	private function process_value( array &$data, $value, string $name ) {
		if ( $value instanceof Entity ) {
			$value = $value->to_array();
		} else if ( is_array( $value ) ) {
			$data[ $name ] = [];

			foreach ( $value as $k => $v ) {
				$this->process_value( $data[ $name ], $v, $k );
			}

			return;
		}

		$data[ $name ] = $value;
	}
}
