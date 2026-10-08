<?php
/**
 * Front page template.
 *
 * @package MaxSchmeling
 */

get_header();

$about_page = get_page_by_path( 'about' );
$about_url  = $about_page ? get_permalink( $about_page ) : false;
$about_url  = $about_url ?: home_url( '/about/' );
?>
<div class="site-shell">
	<section class="hero" aria-labelledby="hero-title">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'Software · AI · Design · Hardware', 'maxschmeling' ); ?></p>
			<h1 id="hero-title"><?php echo wp_kses_post( __( 'I build useful things for the <em>web.</em>', 'maxschmeling' ) ); ?></h1>
			<p class="hero-intro">
				<?php esc_html_e( 'I’m Max Schmeling, a software developer and engineering lead in Kansas City. I write about building products, exploring new technology, and the ideas I pick up along the way.', 'maxschmeling' ); ?>
			</p>
			<div class="hero-actions">
				<a class="button" href="<?php echo esc_url( maxschmeling_blog_url() ); ?>"><?php esc_html_e( 'Read the blog', 'maxschmeling' ); ?></a>
				<a class="button button-secondary" href="<?php echo esc_url( $about_url ); ?>"><?php esc_html_e( 'More about me', 'maxschmeling' ); ?></a>
			</div>
		</div>

		<aside class="hero-card" aria-label="<?php esc_attr_e( 'A quick profile', 'maxschmeling' ); ?>">
			<div class="hero-card-bar" aria-hidden="true"><span></span><span></span><span></span></div>
			<div class="hero-card-content">
				<p><span class="prompt">max@web:~$</span> whoami</p>
				<p><span class="terminal-muted">role:</span> software developer</p>
				<p><span class="terminal-muted">focus:</span> useful, human tools</p>
				<p><span class="terminal-muted">curious_about:</span> AI + hardware</p>
				<p><span class="terminal-muted">based_in:</span> Kansas City</p>
			</div>
		</aside>
	</section>

	<section class="section" aria-labelledby="latest-writing">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Notes from the workbench', 'maxschmeling' ); ?></p>
				<h2 id="latest-writing"><?php esc_html_e( 'Latest writing', 'maxschmeling' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( maxschmeling_blog_url() ); ?>"><?php esc_html_e( 'View all posts →', 'maxschmeling' ); ?></a>
		</div>

		<?php
		$latest_posts = new WP_Query(
			array(
				'posts_per_page'      => 3,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			)
		);
		?>
		<?php if ( $latest_posts->have_posts() ) : ?>
			<div class="post-grid">
				<?php
				while ( $latest_posts->have_posts() ) :
					$latest_posts->the_post();
					get_template_part( 'template-parts/content', 'card' );
				endwhile;
				?>
			</div>
			<?php wp_reset_postdata(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</section>

	<section class="section" aria-labelledby="interests-title">
		<div class="section-heading">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'The recurring threads', 'maxschmeling' ); ?></p>
				<h2 id="interests-title"><?php esc_html_e( 'Things I think about', 'maxschmeling' ); ?></h2>
			</div>
		</div>
		<div class="interest-grid" aria-label="<?php esc_attr_e( 'Topics', 'maxschmeling' ); ?>">
			<span class="interest"><?php esc_html_e( 'Software', 'maxschmeling' ); ?></span>
			<span class="interest"><?php esc_html_e( 'AI', 'maxschmeling' ); ?></span>
			<span class="interest"><?php esc_html_e( 'Design', 'maxschmeling' ); ?></span>
			<span class="interest"><?php esc_html_e( 'Hardware', 'maxschmeling' ); ?></span>
			<span class="interest"><?php esc_html_e( 'WordPress', 'maxschmeling' ); ?></span>
			<span class="interest"><?php esc_html_e( 'Life', 'maxschmeling' ); ?></span>
		</div>
	</section>

	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<?php if ( trim( get_the_content() ) ) : ?>
				<section class="section entry-content" aria-label="<?php esc_attr_e( 'More about Max', 'maxschmeling' ); ?>">
					<?php the_content(); ?>
				</section>
			<?php endif; ?>
		<?php endwhile; ?>
	<?php endif; ?>
</div>
<?php
get_footer();
