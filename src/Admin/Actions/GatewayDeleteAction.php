<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Action;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Result;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Configurator;

/**
 * Handles a request from the settings page to delete a gateway.
 */
class GatewayDeleteAction extends Action {
	/**
	 * @var Configurator An instance of the Configurator service.
	 */
	private Configurator $configurator;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->configurator = Core::instance()->get_service( Configurator::class );
	}

	public function handle( array $query, array $data ): Result {
		$gateway_id = $data['data'] ?? null;

		if ( $this->configurator->delete_gateway( $gateway_id ) ) {
			return new Result( Result::CODE_SUCCESS, [
				'gateway_id_deleted' => $gateway_id,
			] );
		}

		return new Result( Result::CODE_FAILURE );
	}
}
