<?php
/**
 * JSON import handler for manual sections.
 *
 * @package WpClientOnboarding
 */
declare(strict_types=1);

namespace WpClientOnboarding;

defined('ABSPATH') || exit;

/**
 * Importer class - handles JSON file import for manual sections.
 */
class Importer {

	/**
	 * Constructor - set up hooks.
	 */
	public function __construct() {
		add_action('admin_post_wcob_import', [$this, 'handle_import']);
	}

	/**
	 * Handle JSON import from uploaded file.
	 *
	 * Verifies nonce, checks permissions, validates JSON structure,
	 * and creates posts from the imported data.
	 *
	 * @return void
	 */
	public function handle_import(): void {
		// Verify nonce.
		if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'wcob_import_action')) {
			wp_die(esc_html__('Nonce verification failed.', 'wp-client-onboarding'));
		}

		// Check user capabilities.
		if (!current_user_can('manage_wcob_manual')) {
			wp_die(esc_html__('Insufficient permissions.', 'wp-client-onboarding'));
		}

		// Check if file was uploaded.
		if (!isset($_FILES['wcob_import_file']) || empty($_FILES['wcob_import_file']['tmp_name'])) {
			$this->set_import_message('error', esc_html__('No file uploaded.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		$file = $_FILES['wcob_import_file'];

		// Validate file extension is .json.
		$filename = isset($file['name']) ? sanitize_file_name(wp_unslash($file['name'])) : '';
		if (empty($filename) || !preg_match('/\.json$/i', $filename)) {
			$this->set_import_message('error', esc_html__('File must have a .json extension.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		// Validate MIME type.
		$mime_type = isset($file['type']) ? sanitize_text_field(wp_unslash($file['type'])) : '';
		$allowed_mimes = ['application/json', 'text/plain'];
		if (!in_array($mime_type, $allowed_mimes, true)) {
			$this->set_import_message('error', esc_html__('Invalid file type. Please upload a JSON file.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		// Read file contents.
		$tmp_file = isset($file['tmp_name']) ? sanitize_text_field(wp_unslash($file['tmp_name'])) : '';
		if (!is_readable($tmp_file)) {
			$this->set_import_message('error', esc_html__('Unable to read uploaded file.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		$file_contents = file_get_contents($tmp_file);
		if (false === $file_contents) {
			$this->set_import_message('error', esc_html__('Failed to read file contents.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		// Decode JSON.
		try {
			$data = json_decode($file_contents, true, 512, JSON_THROW_ON_ERROR);
		} catch (\JsonException $e) {
			$this->set_import_message('error', esc_html__('Invalid JSON format: ', 'wp-client-onboarding') . esc_html($e->getMessage()));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		if (!is_array($data)) {
			$this->set_import_message('error', esc_html__('JSON must contain an array of objects.', 'wp-client-onboarding'));
			wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
			exit;
		}

		// Validate and import entries.
		$success_count = 0;
		$error_messages = [];

		foreach ($data as $index => $entry) {
			// Validate entry is an object.
			if (!is_array($entry)) {
				$error_messages[] = sprintf(
					/* translators: %d is the entry index */
					esc_html__('Entry %d is not an object.', 'wp-client-onboarding'),
					$index + 1
				);
				continue;
			}

			// Validate required fields.
			if (empty($entry['title']) || !is_string($entry['title'])) {
				$error_messages[] = sprintf(
					/* translators: %d is the entry index */
					esc_html__('Entry %d missing required field "title".', 'wp-client-onboarding'),
					$index + 1
				);
				continue;
			}

			if (empty($entry['content']) || !is_string($entry['content'])) {
				$error_messages[] = sprintf(
					/* translators: %d is the entry index */
					esc_html__('Entry %d missing required field "content".', 'wp-client-onboarding'),
					$index + 1
				);
				continue;
			}

			// Validate optional order field.
			$order = 0;
			if (isset($entry['order'])) {
				if (!is_int($entry['order'])) {
					$error_messages[] = sprintf(
						/* translators: %d is the entry index */
						esc_html__('Entry %d has invalid "order" field (must be integer).', 'wp-client-onboarding'),
						$index + 1
					);
					continue;
				}
				$order = $entry['order'];
			}

			// Create the post.
			$post_id = wp_insert_post([
				'post_type'    => 'wcob_manual_section',
				'post_title'   => wp_kses_post($entry['title']),
				'post_content' => wp_kses_post($entry['content']),
				'post_status'  => 'publish',
				'menu_order'   => $order,
			], true);

			if (is_wp_error($post_id)) {
				$error_messages[] = sprintf(
					/* translators: %d is the entry index, %s is the error message */
					esc_html__('Entry %d failed to import: %s', 'wp-client-onboarding'),
					$index + 1,
					esc_html($post_id->get_error_message())
				);
				continue;
			}

			$success_count++;
		}

		// Set message with results.
		if ($success_count > 0) {
			$message = sprintf(
				/* translators: %d is the number of imported entries */
				esc_html__('Successfully imported %d section(s).', 'wp-client-onboarding'),
				$success_count
			);

			if (!empty($error_messages)) {
				$message .= ' ' . esc_html__('Some entries had errors:', 'wp-client-onboarding') . ' ' . implode(' ', $error_messages);
			}

			$this->set_import_message('success', $message);
		} else {
			$message = esc_html__('No entries were imported.', 'wp-client-onboarding');
			if (!empty($error_messages)) {
				$message .= ' ' . esc_html__('Errors:', 'wp-client-onboarding') . ' ' . implode(' ', $error_messages);
			}

			$this->set_import_message('error', $message);
		}

		wp_safe_redirect(admin_url('admin.php?page=wcob-import'));
		exit;
	}

	/**
	 * Set import message in transient for display after redirect.
	 *
	 * @param string $type The message type ('success' or 'error').
	 * @param string $message The message text.
	 * @return void
	 */
	private function set_import_message(string $type, string $message): void {
		set_transient('wcob_import_message', [
			'type'    => $type,
			'message' => $message,
		], 30);
	}
}
