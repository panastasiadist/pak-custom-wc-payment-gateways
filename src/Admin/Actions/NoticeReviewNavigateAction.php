<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Action;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Constants;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\Result;

/**
 * Handles a request made through an admin-side notice claiming that the user would like to write a review.
 */
class NoticeReviewNavigateAction extends Action {
	public function handle( array $query, array $data ): Result {
		return new Result( Result::CODE_SUCCESS, [
			'url'     => Constants::URL_FEEDBACK,
			'dismiss' => false,
		] );
	}
}
