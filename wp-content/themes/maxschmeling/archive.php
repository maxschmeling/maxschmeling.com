<?php
/**
 * Archive template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell archive-layout">
	<header class="archive-header">
		<p class="eyebrow"><?php esc_html_e( 'Browse the archive', 'maxschmeling' ); ?></p>
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
		<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
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
