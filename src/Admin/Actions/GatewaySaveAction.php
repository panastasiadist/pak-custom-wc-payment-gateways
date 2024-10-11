<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions;

use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Action;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Gateway;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Result;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Configurator;

/**
 * Handles a request from the settings page to create or update a gateway.
 */
class GatewaySaveAction extends Action {
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
		$gateway_data = $data['data'] ?? array();

		try {
			$gateway = Gateway::from_array( $gateway_data );

			if ( $this->configurator->save_gateway( $gateway ) ) {
				return new Result( Result::CODE_SUCCESS, $gateway );
			}
		} catch ( Exception $e ) {
		}

		return new Result( Result::CODE_FAILURE );
	}
}
