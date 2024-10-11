<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewIgnoreAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewRemindAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Base\Notice;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices\Traits\NoticeReviewActions;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\LogEntry;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Logger;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Memoizer;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\array_find;

class ReviewRemindNotice extends Notice {
	use NoticeReviewActions;

	/**
	 * @var Logger $logger An instance of the Logger service.
	 */
	private Logger $logger;

	/**
	 * @var Memoizer $memoizer An instance of the Memoizer service.
	 */
	private Memoizer $memoizer;

	public function __construct() {
		$this->logger   = Core::instance()->get_service( Logger::class );
		$this->memoizer = Core::instance()->get_service( Memoizer::class );
	}

	public function get_body(): string {
		return __( 'It seems that you have been using PAK Custom Payment Gateways for WooCommerce for some time! Would you like to give it a 5-star review?', 'pak-custom-wc-payment-gateways' );
	}

	public function get_type(): string {
		return self::TYPE_INFO;
	}

	public function is_applicable(): bool {
		$code_ignore = NoticeReviewIgnoreAction::get_id();
		$code_remind = NoticeReviewRemindAction::get_id();
		$args        = [ $code_ignore, $code_remind ];

		/**
		 * @var LogEntry[] $entries
		 */
		$entries = $this->memoizer->get( $args, fn() => $this->logger->get_most_recent_entry_per_code( $args ) );

		$entry_remind = array_find( $entries, fn( LogEntry $entry ) => $entry->get_code() === $code_remind );

		if ( $entry_remind ) {
			$entry_done = array_find( $entries, fn( LogEntry $entry ) => $entry->get_code() === $code_ignore );

			if ( $entry_done ) {
				return false;
			}

			$date_now = \DateTime::createFromFormat( 'U', time() );

			return $date_now->diff( $entry_remind->get_created_at() )->d >= 7;
		}

		return false;
	}
}
