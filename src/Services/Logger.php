<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Services;

use DateTime;
use Exception;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Entities\LogEntry;
use function panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions\get_prefixed_table;

/**
 * Class that implements logging functionality.
 */
class Logger {
	/**
	 * Returns the most recent entry for each code. All stored codes are taken into account when a null value is provided.
	 *
	 * @param string[]|null $codes
	 *
	 * @return LogEntry[]
	 */
	public function get_most_recent_entry_per_code( array $codes = null ): array {
		$table     = get_prefixed_table( 'logs' );
		$condition = '';

		if ( $codes ) {
			$condition_in = implode( "','", array_fill( 0, count( $codes ), '%s' ) );
			$condition    = " WHERE code IN ('$condition_in')";
		}

		$sql = "SELECT * FROM {$table} WHERE id IN (SELECT MAX(id) FROM {$table} $condition GROUP BY code);";

		return $this->query( $sql, $codes ?: [] );
	}

	/**
	 * Stores a log entry of level 'information'.
	 *
	 * @param string $code A context-specific code describing the entry.
	 * @param array $data Context-specific data accompanying the entry.
	 *
	 * @return void
	 */
	public function information( string $code = '', array $data = array() ) {
		$this->log( LogEntry::LEVEL_INFORMATION, $code, $data, get_current_user_id() );
	}

	/**
	 * Creates the database table required by the service.
	 *
	 * @return void
	 */
	public static function install() {
		global $wpdb;

		$table = get_prefixed_table( 'logs' );

		$sql = [
			"CREATE TABLE $table (
				id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			  	level VARCHAR(16) NOT NULL,
			  	code VARCHAR(32) DEFAULT NULL,
			  	data LONGTEXT DEFAULT NULL,
			  	user_id INT UNSIGNED DEFAULT NULL,
			  	created_at DATETIME NOT NULL DEFAULT current_timestamp())",
			"ALTER TABLE $table
    			ADD INDEX code (code),
    			ADD INDEX created_at (created_at),
    			ADD INDEX level (level),
    			ADD INDEX user_id (user_id)"
		];

		$success = true;

		// Enclose the queries in a buffer to avoid spitting out any errors that may occur.

		ob_start();

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( 'START TRANSACTION' );

		foreach ( $sql as $query ) {
			if ( false === $wpdb->query( $query ) ) {
				$wpdb->query( 'ROLLBACK' );
				$success = false;
			}
		}

		if ( $success ) {
			$wpdb->query( 'COMMIT' );
		}
		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		ob_get_clean();
	}

	public function log( string $level, string $code, array $data = array(), int $user_id = 0 ) {
		$this->store( new LogEntry( DateTime::createFromFormat( 'U', time() ), $level, $code, $data, $user_id ) );
	}

	/**
	 * Deletes the database table associated with the service.
	 *
	 * @return void
	 */
	public static function uninstall() {
		global $wpdb;

		$table = get_prefixed_table( 'logs' );
		$sql   = "DROP TABLE $table";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$wpdb->query( $sql );
	}

	/**
	 * Queries the database for log entries and returns them.
	 *
	 * @param string $sql
	 * @param array $parameters
	 *
	 * @return array
	 * @throws Exception
	 */
	private function query( string $sql, array $parameters ): array {
		global $wpdb;

		// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		$query = $wpdb->prepare( $sql, ...$parameters );
		$rows  = $wpdb->get_results( $query, ARRAY_A );

		// phpcs:enable WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared

		return array_map( fn( array $row ) => new LogEntry(
			DateTime::createFromFormat( 'Y-m-d H:i:s', $row['created_at'] ),
			$row['level'],
			$row['code'] ?? '',
			$row['data'] ? json_decode( $row['data'], true ) : array(),
			intval( $row['user_id'] ?? 0 ),
		), $rows );
	}

	/**
	 * Stores an entry in the database.
	 *
	 * @param LogEntry $entry
	 *
	 * @return void
	 */
	private function store( LogEntry $entry ) {
		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->insert( get_prefixed_table( 'logs' ), [
			'code'       => $entry->get_code() ?: null,
			'created_at' => $entry->get_created_at()->format( 'Y-m-d H:i:s' ),
			'data'       => empty( $entry->get_data() ) ? null : wp_json_encode( $entry->get_data() ),
			'level'      => $entry->get_level(),
			'user_id'    => $entry->get_user_id() ?: null,
		] );
	}
}
