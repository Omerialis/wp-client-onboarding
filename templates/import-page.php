<?php
/**
 * Template for the manual sections JSON import page.
 *
 * @package WpClientOnboarding
 */
defined('ABSPATH') || exit;

// Get import message from transient.
$import_message = get_transient('wcob_import_message');
delete_transient('wcob_import_message');
?>
<div class="wrap">
	<h1><?php esc_html_e('Import Manual Sections', 'wp-client-onboarding'); ?></h1>

	<?php if ($import_message) : ?>
		<div class="notice notice-<?php echo esc_attr($import_message['type']); ?> is-dismissible">
			<p><?php echo wp_kses_post($import_message['message']); ?></p>
		</div>
	<?php endif; ?>

	<div class="card">
		<h2><?php esc_html_e('Upload JSON File', 'wp-client-onboarding'); ?></h2>
		<p><?php esc_html_e('Upload a JSON file containing manual sections to import them into the system.', 'wp-client-onboarding'); ?></p>

		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data" id="wcob-import-form">
			<?php wp_nonce_field('wcob_import_action'); ?>
			<input type="hidden" name="action" value="wcob_import" />

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="wcob_import_file">
							<?php esc_html_e('JSON File', 'wp-client-onboarding'); ?>
						</label>
					</th>
					<td>
						<input type="file" id="wcob_import_file" name="wcob_import_file" accept=".json" required />
						<p class="description">
							<?php esc_html_e('Select a .json file to import.', 'wp-client-onboarding'); ?>
						</p>
						<div id="wcob-file-info" style="display:none; margin-top: 10px;">
							<strong><?php esc_html_e('Selected file:', 'wp-client-onboarding'); ?></strong>
							<span id="wcob-file-name"></span>
						</div>
					</td>
				</tr>
			</table>

			<?php submit_button(esc_html__('Import Sections', 'wp-client-onboarding'), 'primary'); ?>
		</form>
	</div>

	<div class="card">
		<h2><?php esc_html_e('JSON Format', 'wp-client-onboarding'); ?></h2>
		<p><?php esc_html_e('Your JSON file should be an array of objects with the following structure:', 'wp-client-onboarding'); ?></p>

		<pre style="background: #f5f5f5; padding: 15px; overflow-x: auto;"><code><?php
echo esc_html(
	json_encode(
		[
			[
				'title'   => 'Getting Started',
				'content' => '<p>Welcome to our manual...</p>',
				'order'   => 0,
			],
			[
				'title'   => 'Advanced Topics',
				'content' => '<p>Learn more about...</p>',
				'order'   => 1,
			],
		],
		JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
	)
);
?></code></pre>

		<h3><?php esc_html_e('Field Descriptions', 'wp-client-onboarding'); ?></h3>
		<ul>
			<li>
				<strong><?php esc_html_e('title', 'wp-client-onboarding'); ?></strong>
				<?php esc_html_e('(required, string) - Section title', 'wp-client-onboarding'); ?>
			</li>
			<li>
				<strong><?php esc_html_e('content', 'wp-client-onboarding'); ?></strong>
				<?php esc_html_e('(required, string) - Section content (HTML allowed)', 'wp-client-onboarding'); ?>
			</li>
			<li>
				<strong><?php esc_html_e('order', 'wp-client-onboarding'); ?></strong>
				<?php esc_html_e('(optional, integer) - Section display order', 'wp-client-onboarding'); ?>
			</li>
		</ul>
	</div>
</div>
