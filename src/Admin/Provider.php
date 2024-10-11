<?php

namespace panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin;

use Kucrut\Vite;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\GatewayDeleteAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Actions\GatewaySaveAction;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Admin\Services\Notifier;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Constants;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Core;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Configurator;
use panastasiadist\PAK_Custom_WC_Payment_Gateways\Services\Controller;

/**
 * Provides the necessary functionality for a settings page in the administration area.
 */
class Provider {
	/**
	 * @var Configurator A Configurator instance used to retrieve and store the plugin's settings.
	 */
	private Configurator $configurator;

	/**
	 * @var string $capability Capability required by the user to access the plugin's settings page.
	 */
	private string $capability = 'manage_options';

	/**
	 * @var string The absolute file system path to the main plugin file.
	 */
	private string $main_plugin_file_path = '';

	/**
	 * @var Notifier A Notifier instance used to handle admin-side notices.
	 */
	private Notifier $notifier;

	/**
	 * @var bool $load_settings Whether configuration page-specific stuff should be loaded.
	 */
	private bool $load_settings = false;

	/**
	 * @var bool $load_notices Whether notice-specific stuff should be loaded.
	 */
	private bool $load_notices = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->configurator          = Core::instance()->get_service( Configurator::class );
		$this->main_plugin_file_path = Core::instance()->get_main_plugin_file_path();

		$controller = Core::instance()->get_service( Controller::class );

		add_action( 'init', function () use ( $controller ) {
			if ( ! current_user_can( $this->capability ) ) {
				return;
			}

			$this->notifier = new Notifier();

			add_action( 'admin_menu', fn() => $this->render_settings_page() );
			add_filter( 'plugin_action_links_' . plugin_basename( $this->main_plugin_file_path ), fn( array $links ) => $this->add_plugin_action_links( $links ) );
			add_filter( 'plugin_row_meta', fn( array $links, string $file ) => $this->add_plugin_meta_links( $links, $file ), 10, 2 );

			$controller->register( GatewaySaveAction::class );
			$controller->register( GatewayDeleteAction::class );

			$this->load_settings = true;
			$this->load_notices  = $this->notifier->has_applicable_notices();

			add_action( 'admin_enqueue_scripts', fn( string $hook_suffix ) => $this->enqueue_scripts( $hook_suffix ) );
		} );
	}

	/**
	 * Adds plugin-specific links in the meta area of the plugin's row in the plugins management page.
	 *
	 * @param array $links
	 * @param string $file
	 *
	 * @return array
	 */
	private function add_plugin_meta_links( array $links, string $file ): array {
		if ( plugin_basename( $this->main_plugin_file_path ) !== $file ) {
			return $links;
		}

		return array_merge( $links, [
			'feedback' => '<a href="' . esc_url( Constants::URL_FEEDBACK ) . '" target="_blank">' . esc_html__( 'Feedback / Review', 'pak-custom-wc-payment-gateways' ) . '</a>',
			'github'   => '<a href="' . esc_url( Constants::URL_GITHUB ) . '" target="_blank">' . esc_html__( 'GitHub', 'pak-custom-wc-payment-gateways' ) . '</a>',
		] );
	}

	/**
	 * Adds plugin-specific links in the actions area of the plugin's row in the plugins management page.
	 *
	 * @param array $links
	 *
	 * @return array
	 */
	private function add_plugin_action_links( array $links ): array {
		return array_merge( $links, [
			'<a href="' . get_admin_url( null, 'options-general.php?page=pak-custom-wc-payment-gateways' ) . '">' . esc_html__( 'Settings', 'pak-custom-wc-payment-gateways' ) . '</a>',
		] );
	}

	/**
	 * Handles a request from the settings page to create or update a gateway.
	 *
	 * @param string $hook_suffix A string corresponding to the page being requested.
	 *
	 * @return void
	 */
	private function enqueue_scripts( string $hook_suffix ) {
		if ( ! ( $this->load_notices || $this->load_settings ) ) {
			return;
		}

		$object = [
			'url_ajax' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce(),
		];

		/* phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion */
		wp_enqueue_style( 'font-inter', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap' );

		wp_enqueue_script(
			'pak-custom-wc-payment-gateways',
			plugins_url( 'admin/common.js', $this->main_plugin_file_path ),
			array( 'jquery' ),
			filemtime( plugin_dir_path( $this->main_plugin_file_path ) . 'admin/common.js' ),
			true
		);

		if ( $this->load_notices ) {
			wp_enqueue_script(
				'pak-custom-wc-payment-gateways-notices',
				plugins_url( 'admin/notices.js', $this->main_plugin_file_path ),
				array( 'pak-custom-wc-payment-gateways' ),
				filemtime( plugin_dir_path( $this->main_plugin_file_path ) . 'admin/notices.js' ),
				true
			);

			wp_enqueue_style(
				'pak-custom-wc-payment-gateways-notices',
				plugins_url( 'admin/notices.css', $this->main_plugin_file_path ),
				array(),
				filemtime( plugin_dir_path( $this->main_plugin_file_path ) . 'admin/notices.css' ),
			);
		}

		// Return if it's not our settings page
		if ( $this->load_settings && 'settings_page_pak-custom-wc-payment-gateways' === $hook_suffix ) {
			Vite\enqueue_asset(
				__DIR__ . '/../../admin/settings/dist',
				'src/main.ts',
				[ 'handle' => 'pak-custom-wc-payment-gateways-settings', 'in-footer' => true ]
			);

			$object = array_merge_recursive( $object, [
				'action_gateway_delete' => GatewayDeleteAction::get_id(),
				'action_gateway_save'   => GatewaySaveAction::get_id(),
				'gateway_id_prefix'     => $this->configurator::get_gateway_id_prefix(),
				'gateways'              => $this->configurator->get_configuration()->to_array()['gateways'],
				'texts'                 => require_once __DIR__ . '/texts_settings.php',
				'url_author'            => Constants::URL_AUTHOR,
				'url_contribution'      => Constants::URL_GITHUB,
				'url_feedback'          => Constants::URL_FEEDBACK,
				'url_help'              => Constants::URL_HELP,
			] );
		}

		wp_localize_script( 'pak-custom-wc-payment-gateways', 'pak_custom_wc_payment_gateways', $object );
	}

	/**
	 * Add the plugin's settings page to the WordPress Settings menu.
	 *
	 * @return void
	 */
	private function render_settings_page() {
		add_options_page(
			'PAK Custom Payment Gateways for WooCommerce',
			'PAK Custom Payment Gateways for WooCommerce',
			$this->capability,
			'pak-custom-wc-payment-gateways',
			function () {
				echo '<div class="app"></div>';
			}
		);
	}
}
