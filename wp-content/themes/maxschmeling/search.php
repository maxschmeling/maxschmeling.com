<?php
/**
 * Search results template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell archive-layout">
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Search', 'maxschmeling' ); ?></p>
		<h1>
			<?php
			printf(
				/* translators: %s: search query. */
				esc_html__( 'Results for “%s”', 'maxschmeling' ),
				esc_html( get_search_query() )
			);
			?>
		</h1>
		<?php get_search_form(); ?>
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
