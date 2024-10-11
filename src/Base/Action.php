<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Base;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Result;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Traits\AutomaticId;

/**
 * Represents an action (request) performed by the user.
 */
abstract class Action {
	use AutomaticId;

	/**
	 * Processes the action and returns a result containing whether the action is successful and any associated data.
	 *
	 * @param array<string, string|int|float> $query Associative array with its keys being the query parameters.
	 * @param array $data Potentially associative array containing any data required for the action to be processed.
	 *
	 * @return Result
	 */
	abstract public function handle( array $query, array $data ): Result;
}
