<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Acts as a bag of parameters which configure the inner-workings of the plugin.
 */
class SystemParameterBag extends Entity {
	/*
	 * @var bool $log_action_data Signifies whether the GET parameters of an action should be logged.
	 */
	private bool $log_action_query;

	/**
	 * @var bool $log_action_payload Signifies whether the POST data of an action should be logged.
	 */
	private bool $log_action_payload;

	/**
	 * @var bool $log_action_response Signifies whether the response data of an action should be logged.
	 */
	private bool $log_action_response;

	/**
	 * Constructor
	 *
	 * @param array<string, int|float|string|boolean> $parameters
	 */
	public function __construct( array $parameters ) {
		$this->log_action_query    = $parameters['log_action_query'] ?? false;
		$this->log_action_payload  = $parameters['log_action_payload'] ?? false;
		$this->log_action_response = $parameters['log_action_response'] ?? false;
	}

	/**
	 * @return bool Returns whether the GET parameters of an action should be logged.
	 */
	public function get_log_action_query(): bool {
		return $this->log_action_query;
	}

	/**
	 * @return bool Returns whether the POST data of an action should be logged.
	 */
	public function get_log_action_payload(): bool {
		return $this->log_action_payload;
	}

	/**
	 * @return bool Returns whether the response data of an action should be logged.
	 */
	public function get_log_action_response(): bool {
		return $this->log_action_response;
	}
}
