<?php
/**
 * Site header.
 *
 * @package MaxSchmeling
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'maxschmeling' ); ?></a>
<header class="site-header">
	<div class="site-shell header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-title" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					Max<span class="site-title-mark">/</span>Schmeling
				</a>
			<?php endif; ?>
		</div>

		<button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primary-navigation">
			<?php esc_html_e( 'Menu', 'maxschmeling' ); ?>
		</button>

		<nav id="primary-navigation" class="primary-navigation" aria-label="<?php esc_attr_e( 'Primary navigation', 'maxschmeling' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'menu_id'        => 'primary-menu',
					'container'      => false,
					'fallback_cb'    => 'maxschmeling_menu_fallback',
				)
			);
			?>
		</nav>
	</div>
</header>
<main id="primary" class="site-main">
