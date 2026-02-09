<?php
/**
 * Capabilities management for WP Client Onboarding.
 *
 * @package WpClientOnboarding
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

namespace WpClientOnboarding;

/**
 * Capabilities class - manages custom capabilities.
 */
class Capabilities {

	/**
	 * Activate the plugin and grant capabilities.
	 *
	 * @return void
	 */
	public static function activate(): void {
		$admin_role = get_role('administrator');

		if ($admin_role instanceof \WP_Role) {
			$admin_role->add_cap('manage_wcob_manual');
		}
	}

	/**
	 * Deactivate the plugin and remove capabilities.
	 *
	 * @return void
	 */
	public static function deactivate(): void {
		$admin_role = get_role('administrator');

		if ($admin_role instanceof \WP_Role) {
			$admin_role->remove_cap('manage_wcob_manual');
		}
	}
}
