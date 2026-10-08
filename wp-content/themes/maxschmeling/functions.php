<?php
/**
 * Theme setup and helpers.
 *
 * @package MaxSchmeling
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Configure theme features.
 */
function maxschmeling_setup(): void {
	load_theme_textdomain( 'maxschmeling', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	add_theme_support(
		'html5',
		array(
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
			'search-form',
		)
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 320,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	register_nav_menus(
		array(
			'primary' => __( 'Primary menu', 'maxschmeling' ),
			'footer'  => __( 'Footer menu', 'maxschmeling' ),
		)
	);
}
add_action( 'after_setup_theme', 'maxschmeling_setup' );

/**
 * Load public assets.
 */
function maxschmeling_assets(): void {
	$theme = wp_get_theme();

	wp_enqueue_style( 'maxschmeling-style', get_stylesheet_uri(), array(), $theme->get( 'Version' ) );
	wp_enqueue_script(
		'maxschmeling-navigation',
		get_template_directory_uri() . '/assets/js/navigation.js',
		array(),
		$theme->get( 'Version' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'maxschmeling_assets' );

/**
 * Provide a branded favicon until a Site Icon is set in WordPress.
 *
 * WordPress takes over automatically as soon as an icon is configured in
 * Settings > General, so this never overrides an administrator's choice.
 */
function maxschmeling_fallback_site_icon(): void {
	if ( has_site_icon() ) {
		return;
	}

	$images_url = get_template_directory_uri() . '/assets/images/';
	?>
	<link rel="icon" href="<?php echo esc_url( $images_url . 'site-icon.svg' ); ?>" type="image/svg+xml">
	<link rel="icon" href="<?php echo esc_url( $images_url . 'site-icon-32.png' ); ?>" sizes="32x32">
	<link rel="icon" href="<?php echo esc_url( $images_url . 'site-icon-192.png' ); ?>" sizes="192x192">
	<link rel="apple-touch-icon" href="<?php echo esc_url( $images_url . 'site-icon-180.png' ); ?>">
	<meta name="msapplication-TileImage" content="<?php echo esc_url( $images_url . 'site-icon-512.png' ); ?>">
	<?php
}
add_action( 'wp_head', 'maxschmeling_fallback_site_icon', 99 );

/**
 * Use a compact excerpt on cards.
 *
 * @param int $length Default excerpt length.
 */
function maxschmeling_excerpt_length( int $length ): int {
	if ( is_admin() ) {
		return $length;
	}

	return 24;
}
add_filter( 'excerpt_length', 'maxschmeling_excerpt_length' );

/**
 * Replace the default excerpt suffix.
 */
function maxschmeling_excerpt_more(): string {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'maxschmeling_excerpt_more' );

/**
 * Remove redundant labels such as "Category:" from archive titles.
 *
 * @param string $title Generated archive title.
 */
function maxschmeling_archive_title( string $title ): string {
	if ( is_category() ) {
		return single_cat_title( '', false );
	}

	if ( is_tag() ) {
		return single_tag_title( '', false );
	}

	if ( is_author() ) {
		return get_the_author();
	}

	return $title;
}
add_filter( 'get_the_archive_title', 'maxschmeling_archive_title' );

/**
 * Get the URL for the posts index.
 */
function maxschmeling_blog_url(): string {
	$page_for_posts = (int) get_option( 'page_for_posts' );

	if ( $page_for_posts ) {
		$permalink = get_permalink( $page_for_posts );

		if ( $permalink ) {
			return $permalink;
		}
	}

	return home_url( '/blog/' );
}

/**
 * Print a compact post byline.
 */
function maxschmeling_post_meta(): void {
	printf(
		'<span>%1$s</span><span aria-hidden="true"> · </span><span>%2$s</span>',
		esc_html( get_the_date() ),
		esc_html(
			sprintf(
				/* translators: %s: estimated reading time. */
				__( '%s min read', 'maxschmeling' ),
				maxschmeling_reading_time()
			)
		)
	);
}

/**
 * Estimate reading time for the current post.
 */
function maxschmeling_reading_time(): int {
	$content    = wp_strip_all_tags( (string) get_post_field( 'post_content', get_the_ID() ) );
	$word_count = str_word_count( $content );

	return max( 1, (int) ceil( $word_count / 220 ) );
}

/**
 * Fallback navigation when no menu has been assigned.
 */
function maxschmeling_menu_fallback(): void {
	?>
	<ul>
		<li><a href="<?php echo esc_url( maxschmeling_blog_url() ); ?>"><?php esc_html_e( 'Blog', 'maxschmeling' ); ?></a></li>
		<?php if ( get_page_by_path( 'projects' ) ) : ?>
			<li><a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'Projects', 'maxschmeling' ); ?></a></li>
		<?php endif; ?>
		<?php if ( get_page_by_path( 'about' ) ) : ?>
			<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'About', 'maxschmeling' ); ?></a></li>
		<?php endif; ?>
	</ul>
	<?php
}
