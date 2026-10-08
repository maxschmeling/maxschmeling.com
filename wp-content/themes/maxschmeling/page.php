<?php
/**
 * Page template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell single-page">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<p class="eyebrow"><?php esc_html_e( 'Max Schmeling', 'maxschmeling' ); ?></p>
				<?php the_title( '<h1>', '</h1>' ); ?>
				<?php if ( has_excerpt() ) : ?>
					<p class="entry-dek"><?php echo esc_html( get_the_excerpt() ); ?></p>
				<?php endif; ?>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="featured-image"><?php the_post_thumbnail( 'full' ); ?></figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
				<?php wp_link_pages(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>
<?php
get_footer();
