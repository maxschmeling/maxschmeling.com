<?php
/**
 * Site footer.
 *
 * @package MaxSchmeling
 */
?>
</main>
<footer class="site-footer">
	<div class="site-shell footer-inner">
		<p class="footer-copy">
			&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>.
			<?php esc_html_e( 'Made with WordPress and curiosity.', 'maxschmeling' ); ?>
		</p>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-navigation" aria-label="<?php esc_attr_e( 'Footer navigation', 'maxschmeling' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'depth'          => 1,
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
