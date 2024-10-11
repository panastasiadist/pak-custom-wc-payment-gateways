<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Notices\Traits;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewIgnoreAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewNavigateAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\NoticeReviewRemindAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Entities\NoticePrompt;

trait NoticeReviewActions {
	/**
	 * @return NoticePrompt[]
	 */
	public function get_prompts(): array {
		return [
			new NoticePrompt( NoticeReviewNavigateAction::class, __( "Yes, I'd love so!", 'pak-custom-wc-payment-gateways' ) ),
			new NoticePrompt( NoticeReviewIgnoreAction::class, __( "No / I've already done so", 'pak-custom-wc-payment-gateways' ) ),
			new NoticePrompt( NoticeReviewRemindAction::class, __( 'Remind me another time', 'pak-custom-wc-payment-gateways' ) ),
		];
	}
}
