<?php
/**
 * Admin page registration and rendering for the manual.
 *
 * @package WpClientOnboarding
 */
declare(strict_types=1);

namespace WpClientOnboarding;

defined('ABSPATH') || exit;

/**
 * AdminPage class - handles admin menu and page rendering.
 */
class AdminPage {

	/**
	 * Constructor - set up hooks.
	 */
	public function __construct() {
		add_action('admin_menu', [$this, 'register_menu']);
	}

	/**
	 * Register the admin menu and submenus.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		// Register the main menu page.
		add_menu_page(
			__( 'User Manual', 'wp-client-onboarding' ),
			__( 'User Manual', 'wp-client-onboarding' ),
			'read',
			'wcob-manual',
			[$this, 'render_page'],
			'dashicons-book-alt',
			30
		);

		// Rename the first submenu item (auto-created by WP) to avoid duplication.
		add_submenu_page(
			'wcob-manual',
			__( 'User Manual', 'wp-client-onboarding' ),
			__( 'View', 'wp-client-onboarding' ),
			'read',
			'wcob-manual',
			[$this, 'render_page']
		);

		// CPT submenus are auto-registered via show_in_menu => 'wcob-manual' in CPT.php.

		// Register import submenu page.
		add_submenu_page(
			'wcob-manual',
			__( 'Import Sections', 'wp-client-onboarding' ),
			__( 'Import', 'wp-client-onboarding' ),
			'manage_wcob_manual',
			'wcob-import',
			[$this, 'render_import_page']
		);
	}

	/**
	 * Render the manual page.
	 *
	 * Displays either the list of sections or a single section based on the ?section= query param.
	 *
	 * @return void
	 */
	public function render_page(): void {
		// Check if a specific section is requested.
		$section_id = isset($_GET['section']) ? intval($_GET['section']) : 0;

		if ($section_id > 0) {
			// Validate the section exists and is the correct post type.
			$section = get_post($section_id);
			if ($section && 'wcob_manual_section' === $section->post_type) {
				// Render the single section template.
				$this->render_section($section_id);
			} else {
				// Section not found, redirect to list.
				$this->render_list();
			}
		} else {
			// No section specified, render the list.
			$this->render_list();
		}
	}

	/**
	 * Render the manual list template.
	 *
	 * @return void
	 */
	private function render_list(): void {
		$template_file = WCOB_PLUGIN_DIR . 'templates/manual-list.php';
		if (file_exists($template_file)) {
			include $template_file;
		}
	}

	/**
	 * Render the manual section template.
	 *
	 * @param int $section_id The section post ID.
	 * @return void
	 */
	private function render_section(int $section_id): void {
		$template_file = WCOB_PLUGIN_DIR . 'templates/manual-section.php';
		if (file_exists($template_file)) {
			include $template_file;
		}
	}

	/**
	 * Render the import page template.
	 *
	 * @return void
	 */
	public function render_import_page(): void {
		$template_file = WCOB_PLUGIN_DIR . 'templates/import-page.php';
		if (file_exists($template_file)) {
			include $template_file;
		}
	}
}
