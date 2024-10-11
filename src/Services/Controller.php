<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Services;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Action;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\SystemParameterBag;

/**
 * Class that accepts and routes ajax requests to their handlers for processing.
 */
class Controller {
	/**
	 * @var array<string, Action> Action IDs to their respective instances. Used for lookup purposes.
	 */
	private array $action_id_to_instance = [];

	/**
	 * @var Logger An instance of a Logger service.
	 */
	private Logger $logger;

	/**
	 * @var SystemParameterBag A SystemParameterBag instance.
	 */
	private SystemParameterBag $parameters;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->logger     = Core::instance()->get_service( Logger::class );
		$this->parameters = Core::instance()->get_parameters();
	}

	/**
	 * Handle a specific request recognized by a unique action id.
	 *
	 * @param $action_id
	 *
	 * @return void
	 */
	private function handle( $action_id ) {
		$action = $this->action_id_to_instance[ $action_id ] ?? null;

		if ( ! ( $action && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ?? '' ) ) ) ) ) {
			die();
		}

		unset( $_POST['nonce'] );

		$result   = $action->handle( $_GET, $_POST );
		$response = $result->to_array();

		/**
		 * @var array{ 0: callable, 1: callable, 2: string } $log_data_predicates
		 */
		$log_data_predicates = [
			[ fn() => $this->parameters->get_log_action_query(), fn() => $_GET, 'query' ],
			[ fn() => $this->parameters->get_log_action_payload(), fn() => $_POST, 'payload' ],
			[ fn() => $this->parameters->get_log_action_response(), fn() => $response, 'response' ],
		];

		$log_data = array_reduce( $log_data_predicates, function ( $acc, $item ) {
			if ( $item[0]() ) {
				$acc[ $item[2] ] = $item[1]();
			}

			return $acc;
		}, [] );

		$this->logger->information( $action->get_id(), $log_data );

		die( wp_json_encode( $response ) );
	}

	/**
	 * Makes the Controller aware of an Action to be used for serving requests.
	 *
	 * @param string $action_class The class name of the Action to be registered.
	 *
	 * @return void
	 */
	public function register( string $action_class ) {
		/**
		 * @var class-string<Action> $action_class
		 */
		$action = new $action_class();

		$this->action_id_to_instance[ $action->get_id() ] = $action;

		add_action( 'wp_ajax_pak_custom_wc_payment_gateways_' . $action->get_id(), fn() => $this->handle( $action->get_id() ) );
	}
}
