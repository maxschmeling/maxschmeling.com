<?php
/**
 * Empty content state.
 *
 * @package MaxSchmeling
 */
?>
<section class="empty-state">
	<h2><?php esc_html_e( 'Nothing here yet.', 'maxschmeling' ); ?></h2>
	<?php if ( is_search() ) : ?>
		<p><?php esc_html_e( 'Try another search term or browse the latest posts.', 'maxschmeling' ); ?></p>
		<?php get_search_form(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'The next idea is probably still on the workbench.', 'maxschmeling' ); ?></p>
	<?php endif; ?>
</section>
