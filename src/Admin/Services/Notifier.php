<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Services;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Base\Notice;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices\FirstOrderWithGatewayNotice;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices\ReviewRemindNotice;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Controller;

class Notifier {
	/**
	 * @var class-string<Notice>[]
	 */
	private array $notices = [
		FirstOrderWithGatewayNotice::class,
		ReviewRemindNotice::class,
	];

	public function __construct() {
		add_action( 'admin_notices', fn() => $this->render() );

		$controller = Core::instance()->get_service( Controller::class );

		foreach ( $this->get_applicable_notices() as $notice ) {
			foreach ( $notice->get_prompts() as $action ) {
				$controller->register( $action->get_action_class() );
			}
		}
	}

	public function has_applicable_notices(): bool {
		return (bool) $this->get_applicable_notices();
	}

	private function render() {
		ob_start();
		foreach ( $this->get_applicable_notices() as $notice ) {
			require __DIR__ . '/../../../templates/notice.php';
		}
		echo wp_kses_post( ob_get_clean() );
	}

	/**
	 * @return Notice[]
	 */
	private function get_applicable_notices(): array {
		/**
		 * @var Notice[] $notices
		 */
		static $notice_group_to_notices = null;

		if ( null === $notice_group_to_notices ) {
			$notice_group_to_notices = [];

			foreach ( $this->notices as $class ) {
				$group = $class::get_group();

				if ( array_key_exists( $group, $notice_group_to_notices ) ) {
					continue;
				}

				$notice = new $class;

				if ( $notice->is_applicable() ) {
					$notice_group_to_notices[ $notice->get_group() ] = $notice;
				}
			}
		}

		return array_values( $notice_group_to_notices );
	}
}
