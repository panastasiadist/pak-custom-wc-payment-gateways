<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Entities;

use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Action;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Represents a prompt (action) offered to the user by a notice.
 */
class NoticePrompt extends Entity {
	/**
	 * @var class-string<Action> The class of the Action that handles this prompt.
	 */
	private string $action_class;

	/**
	 * @var string The title of the prompt displayed to the user.
	 */
	private string $title;

	/**
	 * Constructor
	 *
	 * @param class-string<Action> $action_class The class of the Action that handles this prompt.
	 * @param string $title The title of the prompt displayed to the user.
	 */
	public function __construct( string $action_class, string $title ) {
		$this->action_class = $action_class;
		$this->title        = $title;
	}

	/**
	 * Returns the class of the Action that handles this prompt.
	 *
	 * @return class-string<Action>
	 */
	public function get_action_class(): string {
		return $this->action_class;
	}

	/**
	 * Returns the title of the prompt displayed to the user.
	 *
	 * @return string
	 */
	public function get_title(): string {
		return $this->title;
	}
}
