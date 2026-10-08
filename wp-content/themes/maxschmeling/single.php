<?php
/**
 * Single post template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell single-post">
	<?php while ( have_posts() ) : the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<div class="post-meta"><?php maxschmeling_post_meta(); ?></div>
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

			<footer class="entry-footer">
				<?php the_category( ' · ' ); ?>
				<?php the_tags( '<span aria-hidden="true"> · </span>', ' · ' ); ?>
			</footer>
		</article>

		<?php
		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous', 'maxschmeling' ) . '</span><span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next', 'maxschmeling' ) . '</span><span class="nav-title">%title</span>',
			)
		);
		?>

		<?php if ( comments_open() || get_comments_number() ) : ?>
			<?php comments_template(); ?>
		<?php endif; ?>
	<?php endwhile; ?>
</div>
<?php
get_footer();
