<?php
/**
 * About page template.
 *
 * @package MaxSchmeling
 */

get_header();
?>
<div class="site-shell about-page">
	<section class="about-hero" aria-labelledby="about-title">
		<div>
			<p class="eyebrow"><?php esc_html_e( 'A little context', 'maxschmeling' ); ?></p>
			<h1 id="about-title"><?php esc_html_e( 'I make software—and chase the ideas behind it.', 'maxschmeling' ); ?></h1>
		</div>
		<p class="about-lede">
			<?php esc_html_e( 'I’m Max Schmeling, a software developer and engineering leader based in Kansas City. I care about building useful products, helping teams do their best work, and understanding how technology changes the way we create.', 'maxschmeling' ); ?>
		</p>
	</section>

	<div class="about-grid">
		<div class="about-copy entry-content">
			<section aria-labelledby="work-heading">
				<h2 id="work-heading"><?php esc_html_e( 'The work', 'maxschmeling' ); ?></h2>
				<p><?php esc_html_e( 'I lead software teams at Automattic and spend much of my time around WordPress, publishing workflows, real-time collaboration, and tools for people who make things on the web. I’m most interested in the point where solid engineering, thoughtful design, and real human needs meet.', 'maxschmeling' ); ?></p>
				<p><?php esc_html_e( 'I’ve worked across product development, platform engineering, and open-source software. This site is where I document what I’m learning: technical decisions, experiments, talks, unfinished ideas, and the occasional strong opinion earned the hard way.', 'maxschmeling' ); ?></p>
			</section>

			<section aria-labelledby="building-heading">
				<h2 id="building-heading"><?php esc_html_e( 'Always building', 'maxschmeling' ); ?></h2>
				<p><?php esc_html_e( 'Side projects are how I explore. Clipisode is my long-running experiment in collaborative video creation. BlendedCal helps families make sense of overlapping schedules. GradeWatch turns school data into something more useful. Some projects become products; others are simply a good excuse to learn a new system.', 'maxschmeling' ); ?></p>
				<p><?php esc_html_e( 'Lately, that curiosity keeps pulling me toward AI, connected hardware, developer tools, and the new kinds of interfaces they make possible.', 'maxschmeling' ); ?></p>
			</section>

			<section aria-labelledby="away-heading">
				<h2 id="away-heading"><?php esc_html_e( 'Away from the keyboard', 'maxschmeling' ); ?></h2>
				<p><?php esc_html_e( 'I live in Kansas City with my wife and our three kids. Life outside work involves a lot of youth sports, travel planning, family logistics, and tinkering with whatever piece of technology has caught my attention that week.', 'maxschmeling' ); ?></p>
				<p><?php esc_html_e( 'That mix is intentional. This is a technical site, mostly—but it’s also a record of the projects, places, people, and experiences that matter to me.', 'maxschmeling' ); ?></p>
			</section>
		</div>

		<aside class="about-aside" aria-label="<?php esc_attr_e( 'At a glance', 'maxschmeling' ); ?>">
			<p class="about-aside-label"><?php esc_html_e( 'At a glance', 'maxschmeling' ); ?></p>
			<dl>
				<div><dt><?php esc_html_e( 'Based in', 'maxschmeling' ); ?></dt><dd><?php esc_html_e( 'Kansas City', 'maxschmeling' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Working on', 'maxschmeling' ); ?></dt><dd><?php esc_html_e( 'WordPress & publishing tools', 'maxschmeling' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Exploring', 'maxschmeling' ); ?></dt><dd><?php esc_html_e( 'AI, design & hardware', 'maxschmeling' ); ?></dd></div>
				<div><dt><?php esc_html_e( 'Also', 'maxschmeling' ); ?></dt><dd><?php esc_html_e( 'Husband, dad & serial tinkerer', 'maxschmeling' ); ?></dd></div>
			</dl>
			<div class="about-links">
				<a href="<?php echo esc_url( maxschmeling_blog_url() ); ?>"><?php esc_html_e( 'Read the blog →', 'maxschmeling' ); ?></a>
				<?php if ( get_page_by_path( 'projects' ) ) : ?>
					<a href="<?php echo esc_url( home_url( '/projects/' ) ); ?>"><?php esc_html_e( 'See my projects →', 'maxschmeling' ); ?></a>
				<?php endif; ?>
			</div>
		</aside>
	</div>
</div>
<?php
get_footer();
