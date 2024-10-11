<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities;

use DateTime;
use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Base\Entity;

/**
 * Represents an entry in the logs.
 */
class LogEntry extends Entity {
	/**
	 * A level describing the importance of an entry.
	 */
	public const LEVEL_INFORMATION = 'info';

	/**
	 * @var string A context-specific code describing the entry.
	 */
	private string $code;

	/**
	 * @var DateTime The date and time of the event described by the entry.
	 */
	private DateTime $created_at;

	/**
	 * @var array Context-specific data accompanying the entry.
	 */
	private array $data;

	/**
	 * @var string The level of the entry. Can be one of the supported constants.
	 */
	private string $level;

	/**
	 * @var int The user's ID of the session during which the entry has been created. Zero for guests.
	 */
	private int $user_id;

	/**
	 * @param DateTime $created_at The date and time of the event described by the entry.
	 * @param string $level The level of the entry. Can be one of the supported constants.
	 * @param string $code A context-specific code describing the entry.
	 * @param int $user_id The user's ID of the session during which the entry has been created. Zero for guests.
	 * @param array $data Context-specific data accompanying the entry.
	 *
	 * @throws Exception When the provided level is not one of the supported constants.
	 */
	public function __construct( DateTime $created_at, string $level, string $code, array $data = array(), int $user_id = 0 ) {
		if ( $level !== self::LEVEL_INFORMATION ) {
			throw new Exception( 'Invalid level provided' );
		}

		$this->code       = $code;
		$this->created_at = $created_at;
		$this->data       = $data;
		$this->level      = $level;
		$this->user_id    = $user_id;
	}

	/**
	 * Return a context-specific code describing the entry.
	 *
	 * @return string
	 */
	public function get_code(): string {
		return $this->code;
	}

	/**
	 * Return the date and time of the event described by the entry.
	 *
	 * @return DateTime
	 */
	public function get_created_at(): DateTime {
		return $this->created_at;
	}

	/**
	 * Return any context-specific data accompanying the entry.
	 *
	 * @return array
	 */
	public function get_data(): array {
		return $this->data;
	}

	/**
	 * Returns the level of the entry.
	 *
	 * @return string
	 */
	public function get_level(): string {
		return $this->level;
	}

	/**
	 * Returns the user's ID of the session during which the entry has been created. Zero for guests.
	 *
	 * @return int
	 */
	public function get_user_id(): int {
		return $this->user_id;
	}
}
