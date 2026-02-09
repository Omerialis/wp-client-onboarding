<?php
/**
 * Custom Post Type registration for wcob_manual_section.
 *
 * @package WpClientOnboarding
 */
declare(strict_types=1);

namespace WpClientOnboarding;

defined('ABSPATH') || exit;

/**
 * CPT class - handles custom post type registration and admin functionality.
 */
class CPT {

	/**
	 * Constructor - set up hooks.
	 */
	public function __construct() {
		add_action('init', [$this, 'register_cpt']);
		add_action('admin_init', [$this, 'register_admin_features']);
		add_action('wp_ajax_wcob_reorder_sections', [$this, 'handle_reorder_ajax']);
	}

	/**
	 * Register the wcob_manual_section custom post type.
	 *
	 * @return void
	 */
	public function register_cpt(): void {
		$args = [
			'label'              => __( 'Manual Sections', 'wp-client-onboarding' ),
			'description'        => __( 'Client manual sections', 'wp-client-onboarding' ),
			'public'             => false,
			'show_ui'            => true,
			'show_in_menu'       => 'wcob-manual',
			'show_in_rest'       => false,
			'supports'           => ['title', 'editor', 'page-attributes'],
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'map_meta_cap'       => true,
			'labels'             => [
				'name'                  => __( 'Manual Sections', 'wp-client-onboarding' ),
				'singular_name'         => __( 'Manual Section', 'wp-client-onboarding' ),
				'all_items'             => __( 'All Sections', 'wp-client-onboarding' ),
				'add_new'               => __( 'Add New Section', 'wp-client-onboarding' ),
				'add_new_item'          => __( 'Add New Manual Section', 'wp-client-onboarding' ),
				'edit_item'             => __( 'Edit Section', 'wp-client-onboarding' ),
				'new_item'              => __( 'New Section', 'wp-client-onboarding' ),
				'view_item'             => __( 'View Section', 'wp-client-onboarding' ),
				'search_items'          => __( 'Search Sections', 'wp-client-onboarding' ),
				'not_found'             => __( 'No sections found', 'wp-client-onboarding' ),
				'not_found_in_trash'    => __( 'No sections found in Trash', 'wp-client-onboarding' ),
			],
		];

		register_post_type('wcob_manual_section', $args);
	}

	/**
	 * Register admin features (columns, AJAX handling).
	 *
	 * @return void
	 */
	public function register_admin_features(): void {
		// Add filters for admin columns.
		add_filter('manage_wcob_manual_section_posts_columns', [$this, 'filter_columns']);
		add_filter('manage_sortable_columns', [$this, 'get_sortable_columns']);
		add_filter('manage_wcob_manual_section_posts_custom_column', [$this, 'render_column'], 10, 2);

		// Enqueue script and style for admin if on the CPT list page.
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);

		// Handle capability mapping.
		add_filter('map_meta_cap', [$this, 'map_meta_cap'], 10, 4);
	}

	/**
	 * Filter the columns displayed in the CPT admin list.
	 *
	 * @param array<string, string> $columns The columns array.
	 * @return array<string, string> Modified columns array.
	 */
	public function filter_columns(array $columns): array {
		$new_columns = [];

		foreach ($columns as $key => $value) {
			if ('title' === $key) {
				$new_columns['title'] = $value;
			}
		}

		$new_columns['menu_order'] = __( 'Order', 'wp-client-onboarding' );
		$new_columns['date']        = $columns['date'] ?? 'Date';

		return $new_columns;
	}

	/**
	 * Make the Order column sortable.
	 *
	 * @param array<string, string> $columns The sortable columns array.
	 * @return array<string, string> Modified sortable columns array.
	 */
	public function get_sortable_columns(array $columns): array {
		$columns['menu_order'] = 'menu_order';
		return $columns;
	}

	/**
	 * Render custom column content.
	 *
	 * @param string $column  The column key.
	 * @param int    $post_id The post ID.
	 * @return void
	 */
	public function render_column(string $column, int $post_id): void {
		if ('menu_order' === $column) {
			$menu_order = get_post_field('menu_order', $post_id);
			echo isset($menu_order) ? intval($menu_order) : 0;
		}
	}

	/**
	 * Enqueue admin assets (JavaScript for drag & drop reordering).
	 *
	 * @param string $hook_suffix The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_assets(string $hook_suffix): void {
		// Only enqueue on the CPT list page.
		if ('edit.php' !== $hook_suffix) {
			return;
		}

		global $post_type;
		if ('wcob_manual_section' !== $post_type) {
			return;
		}

		// Register and enqueue the admin order script.
		wp_register_script(
			'wcob-admin-order',
			WCOB_PLUGIN_URL . 'assets/js/admin-order.js',
			[],
			WCOB_VERSION,
			true
		);

		wp_enqueue_script('wcob-admin-order');

		// Localize script with AJAX data.
		wp_localize_script(
			'wcob-admin-order',
			'wcobAdminOrder',
			[
				'ajaxUrl' => admin_url('admin-ajax.php'),
				'nonce'   => wp_create_nonce('wcob_reorder_sections'),
			]
		);
	}

	/**
	 * Handle AJAX request to reorder sections.
	 *
	 * @return void
	 */
	public function handle_reorder_ajax(): void {
		// Verify nonce.
		if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'wcob_reorder_sections')) {
			wp_send_json_error(['message' => 'Nonce verification failed'], 403);
		}

		// Check capability.
		if (!current_user_can('manage_wcob_manual')) {
			wp_send_json_error(['message' => 'Insufficient permissions'], 403);
		}

		// Get the order data.
		if (!isset($_POST['order']) || !is_array($_POST['order'])) {
			wp_send_json_error(['message' => 'Invalid order data'], 400);
		}

		$order = array_map('intval', wp_unslash($_POST['order']));

		// Update menu_order for each post.
		foreach ($order as $position => $post_id) {
			wp_update_post([
				'ID'         => $post_id,
				'menu_order' => $position,
			]);
		}

		wp_send_json_success(['message' => 'Sections reordered successfully']);
	}

	/**
	 * Map meta capabilities for the custom post type.
	 *
	 * @param array<string>  $caps    The required capabilities.
	 * @param string         $cap     The capability being checked.
	 * @param int            $user_id The user ID.
	 * @param array<mixed>   $args    Additional arguments.
	 * @return array<string> The modified capabilities.
	 */
	public function map_meta_cap(array $caps, string $cap, int $user_id, array $args): array {
		// Map edit/delete/read capabilities for wcob_manual_section to manage_wcob_manual.
		if (in_array($cap, ['edit_wcob_manual_section', 'delete_wcob_manual_section', 'read_wcob_manual_section'], true)) {
			$caps = ['manage_wcob_manual'];
		}

		return $caps;
	}
}
