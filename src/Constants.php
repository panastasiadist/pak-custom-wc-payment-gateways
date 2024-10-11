<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways;

class Constants {
	/**
	 * Prefix to use for plugin-specific filter names.
	 */
	public const HOOK_NAMESPACE = 'pak_custom_wc_payment_gateways';

	public const URL_AUTHOR = 'https://anastasiadis.me/';

	public const URL_FEEDBACK = 'https://wordpress.org/support/plugin/pak-custom-wc-payment-gateways/reviews/?filter=5#new-post';

	public const URL_GITHUB = 'https://github.com/panastasiadist/pak-custom-wc-payment-gateways/';

	public const URL_HELP = 'https://github.com/panastasiadist/pak-custom-wc-payment-gateways/blob/main/README.md';

	/**
	 * The option_name used for saving the plugin's configuration in the _options WordPress table.
	 */
	public const WP_OPTIONS_KEY_CONFIG = 'pak_custom_wc_payment_gateways_config';

	/**
	 * The option_name used for saving the plugin's installed version in the _options WordPress table.
	 */
	public const WP_OPTIONS_KEY_VERSION = 'pak_custom_wc_payment_gateways_version';

	/**
	 * The name of a constant to be defined in wp-config.php, to override the default plugin's parameters of operation.
	 */
	public const SYSTEM_PARAMETERS_CONSTANT = 'PAK_CUSTOM_WC_PAYMENT_GATEWAYS';
}
