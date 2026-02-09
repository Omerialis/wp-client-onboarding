<?php
/**
 * Template for displaying a single manual section.
 *
 * @package WpClientOnboarding
 */
defined('ABSPATH') || exit;

// Get the section post.
$section = get_post($section_id);

// Verify the post exists and is the correct type.
if (!$section || 'wcob_manual_section' !== $section->post_type) {
	wp_die(esc_html__('Section not found.', 'wp-client-onboarding'));
}

// Get the back-to-list URL.
$back_url = admin_url('admin.php?page=wcob-manual');
?>
<div class="wrap">
	<div class="wcob-manual-section-header">
		<a href="<?php echo esc_url($back_url); ?>" class="wcob-manual-back-link">
			&larr; <?php esc_html_e( 'Back to manual', 'wp-client-onboarding' ); ?>
		</a>
	</div>

	<h1><?php echo esc_html($section->post_title); ?></h1>

	<div class="wcob-manual-section-content">
		<?php
		// Content is admin-managed (CPT), not user input.
		// apply_filters('the_content') handles oEmbed (YouTube iframes, etc.).
		// wp_kses_post would strip iframes, so we use the filtered output directly.
		$content = apply_filters('the_content', $section->post_content);
		echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
</div>
