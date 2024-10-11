<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Base;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Entities\NoticePrompt;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Traits\AutomaticId;

/**
 * Represents an admin-side WordPress notice.
 */
abstract class Notice {
	use AutomaticId;

	/**
	 * A type of notice influencing how to notice is presented to the user.
	 */
	public const TYPE_SUCCESS = 'success';

	/**
	 * A type of notice influencing how to notice is presented to the user.
	 */
	public const TYPE_INFO = 'info';

	/**
	 * Returns an HTML string that should act as the body of the notice.
	 *
	 * @return string
	 */
	abstract public function get_body(): string;

	/**
	 * Returns the type of the notice that should be one of the constants exposed by this class.
	 *
	 * @return string
	 */
	abstract public function get_type(): string;

	/**
	 * Returns whether the notice should be displayed to the user.
	 *
	 * @return bool
	 */
	abstract public function is_applicable(): bool;

	/**
	 * Returns the prompts (actions) that should be made available to the user as part of the notice.
	 *
	 * @return NoticePrompt[]
	 */
	public function get_prompts(): array {
		return [];
	}

	/**
	 * Returns a string identifying the group to which this notice belongs.
	 * Notices that belong to the same group are considered interchangeable.
	 * By considering a notice's group the system is able to avoid showing multiple notices serving the same purpose.
	 *
	 * @return string
	 */
	public static function get_group(): string {
		return 'default';
	}
}
