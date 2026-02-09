<?php
/**
 * Main Plugin orchestrator class.
 *
 * @package WpClientOnboarding
 */
declare(strict_types=1);

namespace WpClientOnboarding;

defined('ABSPATH') || exit;

/**
 * Plugin class - main orchestrator.
 */
class Plugin {

	/**
	 * Run the plugin.
	 *
	 * Initializes all plugin components by instantiating them.
	 *
	 * @return void
	 */
	public function run(): void {
		// Load plugin text domain for i18n.
		add_action( 'init', [ $this, 'load_textdomain' ] );

		// Instantiate Capabilities to register capabilities handling.
		new Capabilities();

		// Instantiate CPT to register custom post type.
		new CPT();

		// Instantiate AdminPage to register admin menu and pages.
		new AdminPage();

		// Instantiate Assets to enqueue styles and scripts.
		new Assets();

		// Instantiate Importer to handle JSON import.
		new Importer();
	}

	/**
	 * Load the plugin text domain for translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'wp-client-onboarding',
			false,
			dirname( plugin_basename( WCOB_PLUGIN_FILE ) ) . '/languages'
		);
	}
}
