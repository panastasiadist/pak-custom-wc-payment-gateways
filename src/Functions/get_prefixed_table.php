<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Functions;

/**
 * Returns the provided database table name appropriately prefixed for the plugin's needs.
 *
 * @param string $table
 *
 * @return string
 */
function get_prefixed_table( string $table ): string {
	global $wpdb;

	return $wpdb->prefix . 'pak_custom_wc_payment_gateways_' . $table;
}
