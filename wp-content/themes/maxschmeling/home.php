<?php
/**
 * Posts index template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell archive-layout">
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Ideas, experiments, and field notes', 'maxschmeling' ); ?></p>
		<h1><?php single_post_title(); ?></h1>
		<p class="archive-description"><?php esc_html_e( 'Writing about software, AI, design, development, hardware, travel, and whatever else is worth sharing.', 'maxschmeling' ); ?></p>
	</header>

	<?php if ( have_posts() ) : ?>
		<div class="archive-posts">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/content', 'card' ); ?>
			<?php endwhile; ?>
		</div>
		<?php the_posts_pagination( array( 'mid_size' => 1 ) ); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
