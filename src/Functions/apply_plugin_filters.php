<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Constants;

/**
 * Applies WordPress filters after having prefixed their name with a plugin-specific prefix.
 *
 * @param string $hook_name The name of the filter hook.
 * @param mixed $value The value to filter.
 * @param mixed ...$args Optional. Additional parameters to pass to the callback functions.
 *
 * @return mixed The filtered value after all hooked functions are applied to it.
 */
function apply_plugin_filters( string $hook_name, $value, ...$args ) {
	return apply_filters( Constants::HOOK_NAMESPACE . '/' . $hook_name, $value, ...$args );
}
