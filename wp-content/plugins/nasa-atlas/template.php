<?php
namespace MaxSchmeling\NASAAtlas;
defined('ABSPATH') || exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class(); ?>><?php wp_body_open(); ?>
<a class="atlas-skip" href="#atlas-main">Skip to content</a>
<header class="atlas-masthead">
  <a class="atlas-wordmark" href="<?php echo esc_url(home_url('/nasa-atlas/')); ?>"><span class="atlas-orbit" aria-hidden="true">◌</span> NASA <span>/ ATLAS</span></a>
  <nav aria-label="Atlas navigation"><a href="<?php echo esc_url(home_url('/nasa-atlas/')); ?>">Overview</a><a href="<?php echo esc_url(home_url('/nasa-atlas/explore/')); ?>">Explore ↗</a><a href="<?php echo esc_url(home_url('/nasa-atlas/editor-guide/')); ?>">Behind the Atlas</a></nav>
</header>
<main id="atlas-main"><?php while (have_posts()) { the_post(); the_content(); } ?></main>
<footer class="atlas-footer"><div class="atlas-footer-title">A different perspective<br>changes everything.</div><div><p>NASA ATLAS / A REMOTE DATA BLOCKS DEMO</p><p>Imagery and record data from NASA’s Image and Video Library.<br>Credits belong to the creators named with each image.<br>An independent demonstration. Not affiliated with NASA.</p><a href="<?php echo esc_url(home_url('/')); ?>">← Back to Max Schmeling</a></div><?php if (current_user_can('edit_post', get_the_ID())) { ?><a class="atlas-edit" href="<?php echo esc_url(get_edit_post_link()); ?>">Edit this page ↗</a><?php } ?></footer>
<?php wp_footer(); ?></body></html>
