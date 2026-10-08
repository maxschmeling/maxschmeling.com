<?php
/**
 * Not found template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell page-layout">
	<section class="error-404">
		<p class="eyebrow">404</p>
		<h1><?php esc_html_e( 'This route goes nowhere.', 'maxschmeling' ); ?></h1>
		<p><?php esc_html_e( 'The page may have moved, or it never existed in the first place.', 'maxschmeling' ); ?></p>
		<div class="hero-actions">
			<a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Head home', 'maxschmeling' ); ?></a>
		</div>
	</section>
</div>
<?php
get_footer();
