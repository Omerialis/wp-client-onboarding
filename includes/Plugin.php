<?php
/**
 * Main Plugin orchestrator class.
 *
 * @package WpClientOnboarding
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

namespace WpClientOnboarding;

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
		// Instantiate Capabilities to register capabilities handling.
		new Capabilities();
	}
}
