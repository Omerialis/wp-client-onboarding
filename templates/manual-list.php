<?php
/**
 * Template for displaying the manual sections list.
 *
 * @package WpClientOnboarding
 */
defined('ABSPATH') || exit;

// Query all manual sections ordered by menu_order.
$args = [
	'post_type'      => 'wcob_manual_section',
	'posts_per_page' => -1,
	'orderby'        => 'menu_order',
	'order'          => 'ASC',
];

$sections = new WP_Query($args);
?>
<div class="wrap">
	<h1><?php esc_html_e('Manuel', 'wp-client-onboarding'); ?></h1>

	<?php if ($sections->have_posts()) : ?>
		<div class="wcob-manual-list">
			<?php
			while ($sections->have_posts()) {
				$sections->the_post();
				$section_id   = get_the_ID();
				$section_url  = add_query_arg('section', $section_id, admin_url('admin.php?page=wcob-manual'));
				$excerpt      = has_excerpt() ? get_the_excerpt() : wp_trim_words(get_the_content(), 20);
				?>
				<div class="wcob-manual-section-item">
					<h2>
						<a href="<?php echo esc_url($section_url); ?>">
							<?php the_title(); ?>
						</a>
					</h2>
					<?php if (!empty($excerpt)) : ?>
						<p class="wcob-manual-excerpt">
							<?php echo esc_html($excerpt); ?>
						</p>
					<?php endif; ?>
				</div>
				<?php
			}
			wp_reset_postdata();
			?>
		</div>
	<?php else : ?>
		<p><?php esc_html_e('No sections found.', 'wp-client-onboarding'); ?></p>
	<?php endif; ?>
</div>
