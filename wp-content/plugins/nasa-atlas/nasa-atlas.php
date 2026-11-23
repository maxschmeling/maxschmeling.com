<?php
/**
 * Plugin Name: NASA Atlas — Remote Data Blocks Demo
 * Description: An isolated editorial NASA image atlas powered by Remote Data Blocks. No site-wide theme or navigation changes.
 * Version: 1.0.1
 * Requires at least: 6.7
 * Requires PHP: 8.1
 * Requires Plugins: remote-data-blocks
 */
declare(strict_types=1);
namespace MaxSchmeling\NASAAtlas;
defined('ABSPATH') || exit;

const VERSION = '1.0.1';
const TEMPLATE = 'nasa-atlas-isolated.php';

require __DIR__ . '/queries.php';
require __DIR__ . '/content.php';

// The alpha declares this attribute client-side only. WordPress needs a server
// schema to pass it through providesContext to collection/fallback children.
// Scope the bridge to our two demo blocks; preserve the release artifact intact.
add_filter('register_block_type_args', static function (array $args, string $name): array {
    if (in_array($name, ['remote-data-blocks/nasa-image', 'remote-data-blocks/nasa-archive'], true)) {
        $args['attributes']['remoteData'] = ['type' => 'object'];
    }
    return $args;
}, 20, 2);

function is_atlas(): bool {
    return is_page() && get_page_template_slug() === TEMPLATE;
}

add_filter('theme_page_templates', static function (array $templates): array {
    $templates[TEMPLATE] = 'NASA Atlas — isolated canvas';
    return $templates;
});
add_filter('template_include', static function (string $template): string {
    return is_atlas() ? __DIR__ . '/template.php' : $template;
}, 99);
add_action('wp_enqueue_scripts', static function (): void {
    if (is_atlas()) {
        wp_enqueue_style('nasa-atlas', plugins_url('atlas.css', __FILE__), [], VERSION);
    }
}, 100);
add_filter('body_class', static function (array $classes): array {
    if (is_atlas()) { $classes[] = 'nasa-atlas'; }
    return $classes;
});
add_filter('wp_robots', static function (array $robots): array {
    if (is_atlas()) { $robots['noindex'] = true; }
    return $robots;
});
add_action('enqueue_block_assets', static function (): void {
    if (!is_admin()) { return; }
    $post = get_post();
    if ($post && get_page_template_slug($post) === TEMPLATE) {
        wp_enqueue_style('nasa-atlas-editor', plugins_url('editor.css', __FILE__), [], VERSION);
    }
});

// Public query values affect only deliberately opted-in NASA block instances.
add_filter('remote_data_blocks_query_input_variables', static function (array $inputs, array $enabled, string $block): array {
    if (!is_atlas() || is_admin()) { return $inputs; }
    if ($block === 'remote-data-blocks/nasa-image' && in_array('nasa_id_from_url', $enabled, true)) {
        $id = $_GET['nasa_id'] ?? '';
        if (is_string($id) && preg_match('/^[A-Za-z0-9_.-]{1,160}$/D', $id)) { $inputs['nasa_id'] = $id; }
    }
    if ($block === 'remote-data-blocks/nasa-archive' && in_array('search_from_url', $enabled, true)) {
        $q = $_GET['atlas_q'] ?? '';
        if (is_string($q) && trim($q) !== '') { $inputs['search'] = mb_substr(sanitize_text_field(wp_unslash($q)), 0, 100); }
    }
    return $inputs;
}, 10, 3);

// A real dynamic form, separate from RDB's editor-only record selector.
add_action('init', static function (): void {
    register_block_type('nasa-atlas/record-facts', [
        'api_version' => 3,
        'attributes' => ['archiveYear' => ['type' => 'integer', 'default' => 0], 'hasImage' => ['type' => 'boolean', 'default' => false]],
        'uses_context' => ['remote-data-blocks/remoteData'],
        'render_callback' => static function (array $attrs): string {
            return '<div class="atlas-record-facts"><span>Archive year <strong>' . esc_html((string) $attrs['archiveYear']) . '</strong></span><span>Image available <strong>' . ($attrs['hasImage'] ? 'Yes' : 'No') . '</strong></span></div>';
        },
    ]);
    register_block_type('nasa-atlas/search', [
        'api_version' => 3,
        'render_callback' => static function (): string {
            $q = isset($_GET['atlas_q']) && is_string($_GET['atlas_q']) ? mb_substr(sanitize_text_field(wp_unslash($_GET['atlas_q'])), 0, 100) : 'nebula';
            $html = '<form class="atlas-search" method="get" action="' . esc_url(home_url('/nasa-atlas/explore/')) . '"><label for="atlas-search">Search NASA’s image archive</label><div><input id="atlas-search" name="atlas_q" type="search" maxlength="100" value="' . esc_attr($q) . '" placeholder="Try Saturn, Earth, or the Moon"/><button type="submit">Explore <span aria-hidden="true">↗</span></button></div></form><nav class="atlas-topics" aria-label="Suggested searches">';
            foreach (['Earth', 'Moon', 'Mars', 'Webb', 'Saturn'] as $topic) {
                $html .= '<a href="' . esc_url(add_query_arg('atlas_q', $topic, home_url('/nasa-atlas/explore/'))) . '">' . esc_html($topic) . '</a>';
            }
            return $html . '</nav>';
        },
    ]);
    wp_register_script('nasa-atlas-editor', plugins_url('editor.js', __FILE__), ['wp-blocks', 'wp-element', 'wp-block-editor'], VERSION, true);
});
add_action('enqueue_block_editor_assets', static function (): void { wp_enqueue_script('nasa-atlas-editor'); });
add_filter('block_bindings_supported_attributes_nasa-atlas/record-facts', static fn(array $attrs): array => array_values(array_unique(array_merge($attrs, ['archiveYear', 'hasImage']))));

add_action('admin_menu', static function (): void {
    add_management_page('NASA Atlas', 'NASA Atlas', 'manage_options', 'nasa-atlas', static function (): void {
        echo '<div class="wrap"><h1>NASA Atlas</h1><p>Four isolated demo pages. Existing pages, theme, menus, and homepage are never updated.</p>';
        if (!function_exists('register_remote_data_block')) { echo '<p>Activate Remote Data Blocks first.</p></div>'; return; }
        if (isset($_POST['atlas_seed'])) {
            check_admin_referer('nasa_atlas_seed');
            try { $ids = seed_pages(); echo '<div class="notice notice-success"><p>Demo ready. Existing owned pages were preserved.</p></div>'; }
            catch (\Throwable $e) { echo '<div class="notice notice-error"><p>' . esc_html($e->getMessage()) . '</p></div>'; }
        }
        echo '<form method="post">'; wp_nonce_field('nasa_atlas_seed'); submit_button('Create missing NASA demo pages', 'primary', 'atlas_seed'); echo '</form>';
        foreach ((array) get_option('nasa_atlas_page_ids', []) as $id) {
            echo '<p><a href="' . esc_url(get_permalink($id)) . '">' . esc_html(get_the_title($id)) . '</a> · <a href="' . esc_url(get_edit_post_link($id)) . '">Edit page</a></p>';
        }
        echo '<p>Uninstall/rollback: unpublish the four recorded demo pages, deactivate NASA Atlas, then deactivate Remote Data Blocks only if no other content uses it.</p></div>';
    });
});
