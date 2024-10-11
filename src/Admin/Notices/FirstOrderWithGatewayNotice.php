<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewIgnoreAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewRemindAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Base\Notice;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices\Traits\NoticeReviewActions;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Gateway;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\LogEntry;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Configurator;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Logger;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Memoizer;

class FirstOrderWithGatewayNotice extends Notice {
	use NoticeReviewActions;

	/**
	 * @var Configurator $configurator An instance of the Configuration service.
	 */
	private Configurator $configurator;

	/**
	 * @var Logger $logger An instance of the Logger service.
	 */
	private Logger $logger;

	/**
	 * @var Memoizer $memoizer An instance of the Memoizer service.
	 */
	private Memoizer $memoizer;

	public function __construct() {
		$this->configurator = Core::instance()->get_service( Configurator::class );
		$this->logger       = Core::instance()->get_service( Logger::class );
		$this->memoizer     = Core::instance()->get_service( Memoizer::class );
	}

	public function get_body(): string {
		return __( 'You have received your first order using PAK Custom Payment Gateways for WooCommerce! Would you like to give it a 5-star review?', 'pak-custom-wc-payment-gateways' );
	}

	public function get_type(): string {
		return self::TYPE_SUCCESS;
	}

	public function is_applicable(): bool {
		if ( ! class_exists( 'woocommerce' ) ) {
			return false;
		}

		$args = [
			NoticeReviewIgnoreAction::get_id(),
			NoticeReviewRemindAction::get_id(),
		];

		/**
		 * @var LogEntry[] $entries
		 */
		$entries = $this->memoizer->get( $args, fn() => $this->logger->get_most_recent_entry_per_code( $args ) );

		if ( $entries ) {
			return false;
		}

		$gateways = $this->configurator->get_configuration()->get_gateways();

		$gateway_ids = array_map( fn( Gateway $gateway ) => $gateway->get_id(), $gateways );

		foreach ( $gateway_ids as $gateway_id ) {
			$orders = wc_get_orders( array(
				'limit'          => 1,
				'return'         => 'ids',
				'payment_method' => $gateway_id,
			) );

			if ( $orders ) {
				return true;
			}
		}

		return false;
	}
}
