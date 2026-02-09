<?php
/**
 * Asset enqueuing for the manual admin page.
 *
 * @package WpClientOnboarding
 */
declare(strict_types=1);

namespace WpClientOnboarding;

defined('ABSPATH') || exit;

/**
 * Assets class - handles enqueuing CSS and JS for the manual page.
 */
class Assets {

	/**
	 * Constructor - set up hooks.
	 */
	public function __construct() {
		add_action('admin_enqueue_scripts', [$this, 'enqueue_manual_assets']);
	}

	/**
	 * Enqueue CSS and JS for the manual admin page.
	 *
	 * @param string $hook_suffix The current admin page hook.
	 * @return void
	 */
	public function enqueue_manual_assets(string $hook_suffix): void {
		// Only enqueue on the manual page.
		if ('toplevel_page_wcob-manual' !== $hook_suffix) {
			return;
		}

		// Enqueue CSS.
		wp_enqueue_style(
			'wcob-manual',
			WCOB_PLUGIN_URL . 'assets/css/manual.css',
			[],
			WCOB_VERSION
		);

		// Enqueue JavaScript.
		wp_enqueue_script(
			'wcob-manual',
			WCOB_PLUGIN_URL . 'assets/js/manual.js',
			[],
			WCOB_VERSION,
			true
		);

		// Localize script with data if needed.
		wp_localize_script(
			'wcob-manual',
			'wcobManual',
			[
				'ajaxUrl' => admin_url('admin-ajax.php'),
			]
		);
	}
}
