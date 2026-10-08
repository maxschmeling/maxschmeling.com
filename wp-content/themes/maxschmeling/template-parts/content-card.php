<?php
/**
 * Post card.
 *
 * @package MaxSchmeling
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="post-card-image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
		</a>
	<?php endif; ?>
	<div class="post-card-body">
		<div class="post-meta"><?php maxschmeling_post_meta(); ?></div>
		<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '">', '</a></h3>' ); ?>
		<div class="post-card-excerpt"><?php the_excerpt(); ?></div>
		<a class="post-card-link" href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read article →', 'maxschmeling' ); ?></a>
	</div>
</article>
