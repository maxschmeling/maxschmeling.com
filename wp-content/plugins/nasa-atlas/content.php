<?php
declare(strict_types=1);
namespace MaxSchmeling\NASAAtlas;
defined('ABSPATH') || exit;

function markup(string $name, array $attrs, string $html = '', bool $self = false): string {
    return '<!-- wp:' . $name . ($attrs ? ' ' . serialize_block_attributes($attrs) : '') . ($self ? ' /-->' : ' -->' . $html . '<!-- /wp:' . $name . ' -->');
}
function group(string $class, string $content): string {
    return markup('group', ['className' => $class, 'layout' => ['type' => 'default']], '<div class="wp-block-group ' . esc_attr($class) . '">' . $content . '</div>');
}
function text(string $content, string $class = '', int $level = 0): string {
    $attrs = $class ? ['className' => $class] : [];
    if ($level) { $attrs['level'] = $level; }
    $tag = $level ? 'h' . $level : 'p';
    $classes = trim(($level ? 'wp-block-heading ' : '') . $class);
    return markup($level ? 'heading' : 'paragraph', $attrs, '<' . $tag . ($classes ? ' class="' . esc_attr($classes) . '"' : '') . '>' . $content . '</' . $tag . '>');
}
function bound(string $name, string $field, string $class, string $block, int $level = 0): string {
    $attrs = ['metadata' => ['bindings' => ['content' => ['source' => 'remote-data/binding', 'args' => ['field' => $field, 'block' => $block]]]]];
    if ($class) { $attrs['className'] = $class; }
    if ($level) { $attrs['level'] = $level; }
    $tag = $level ? 'h' . $level : 'p';
    $classes = trim(($level ? 'wp-block-heading ' : '') . $class);
    return markup($name, $attrs, '<' . $tag . ($classes ? ' class="' . esc_attr($classes) . '"' : '') . '></' . $tag . '>');
}
function image_block(string $block, bool $large = false): string {
    $bind = static fn($field) => ['source' => 'remote-data/binding', 'args' => ['field' => $field, 'block' => $block]];
    return markup('image', ['sizeSlug' => 'large', 'linkDestination' => 'none', 'metadata' => ['bindings' => ['url' => $bind($large ? 'hero_image_url' : 'image_url'), 'alt' => $bind('image_alt')]]], '<figure class="wp-block-image size-large"><img src="" alt=""/></figure>');
}
function button(string $label, string $url, string $class = '', ?array $bindings = null): string {
    $attrs = $class ? ['className' => $class] : [];
    if ($bindings) { $attrs['metadata']['bindings'] = $bindings; }
    $html = '<div class="wp-block-button' . ($class ? ' ' . esc_attr($class) : '') . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url($url) . '">' . esc_html($label) . '</a></div>';
    return markup('buttons', [], '<div class="wp-block-buttons">' . markup('button', $attrs, $html) . '</div>');
}
function remote_button(string $block, string $field = 'detail_url', string $label = 'View image ↗'): string {
    $binding = ['url' => ['source' => 'remote-data/binding', 'args' => ['field' => $field, 'block' => $block]]];
    if ($field === 'detail_url') { $binding['text'] = ['source' => 'remote-data/binding', 'args' => ['field' => 'button_label', 'block' => $block]]; }
    return button($label, '', 'atlas-link', $binding);
}
function image_pattern(string $kind, string $block = 'remote-data-blocks/nasa-image'): string {
    $meta = bound('paragraph', 'credit', 'atlas-credit', $block) . bound('paragraph', 'date_label', 'atlas-date', $block);
    if ($kind === 'hero') {
        return group('atlas-hero-plate', image_block($block, true) . group('atlas-hero-caption', bound('heading', 'title', 'atlas-record-title', $block, 2) . $meta . remote_button($block)));
    }
    if ($kind === 'card') {
        return group('atlas-card', image_block($block) . group('atlas-card-body', bound('paragraph', 'credit', 'atlas-credit', $block) . bound('heading', 'title', '', $block, 3) . bound('paragraph', 'excerpt', 'atlas-excerpt', $block) . remote_button($block)));
    }
    $facts = markup('nasa-atlas/record-facts', ['metadata' => ['bindings' => ['archiveYear' => ['source' => 'remote-data/binding', 'args' => ['field' => 'archive_year', 'block' => $block]], 'hasImage' => ['source' => 'remote-data/binding', 'args' => ['field' => 'has_image', 'block' => $block]]]]], '', true);
    return group('atlas-story', image_block($block, true) . group('atlas-story-copy', bound('paragraph', 'date_label', 'atlas-date', $block) . bound('heading', 'title', '', $block, 2) . $facts . bound('paragraph', 'description', 'atlas-description', $block) . $meta . remote_button($block, 'source_url', 'Original NASA record ↗')));
}

function remote(string $name, array $inputs, string $pattern, string $class = '', array $overrides = []): string {
    $block = 'remote-data-blocks/' . $name;
    $config = \RemoteDataBlocks\Editor\BlockManagement\ConfigStore::get_block_configuration($block);
    if (!$config) { throw new \RuntimeException('NASA block did not register: ' . $block); }
    $response = $config['queries']['display']->execute($inputs);
    if (is_wp_error($response)) { throw new \RuntimeException($response->get_error_message()); }
    $data = ['blockName' => $block, 'queryKey' => 'display', 'queryInputs' => [$inputs], 'enabledOverrides' => $overrides, 'results' => $response['results'] ?? [], 'metadata' => $response['metadata'] ?? [], 'resultId' => wp_generate_uuid4()];
    $inner = markup('remote-data-blocks/template', [], $pattern);
    if ($name === 'nasa-archive') { $inner .= markup('remote-data-blocks/pagination', [], '', true); }
    $inner .= markup('remote-data-blocks/no-results', ['mode' => 'empty'], text('No images matched this search. Try a broader subject, or choose a topic above.', 'atlas-empty'));
    $inner .= markup('remote-data-blocks/no-results', ['mode' => 'error'], text('The NASA archive is temporarily unavailable. Please try again shortly.', 'atlas-empty'));
    return markup($block, ['remoteData' => $data, 'className' => $class], '<div class="wp-block-' . str_replace('/', '-', $block) . ' rdb-container' . ($class ? ' ' . esc_attr($class) : '') . '">' . $inner . '</div>');
}
function single(string $id, string $kind, string $class = '', array $overrides = []): string {
    return remote('nasa-image', ['nasa_id' => $id], image_pattern($kind), $class, $overrides);
}

function page_contents(): array {
    $base = home_url('/nasa-atlas/');
    $landing = group('atlas-hero', group('atlas-hero-type', text('A FIELD GUIDE TO THE EXTRAORDINARY', 'atlas-eyebrow') . text('A universe<br>worth exploring.', 'atlas-display', 1) . text('Real images. Remarkable stories.<br>A new perspective on our place in it all.', 'atlas-deck') . button('Enter the archive ↗', $base . 'explore/')) . single('carina_nebula', 'hero', 'atlas-hero-image'));
    $landing .= group('atlas-intro', text('01 / A WIDER PERSPECTIVE', 'atlas-eyebrow') . text('From our home planet<br>to the edge of what we can see.', '', 2) . text('An invitation to look closer. Wander through NASA’s extraordinary image archive, from the world beneath our feet to the places that stretch our imagination.', 'atlas-deck'));
    $landing .= group('atlas-chapter', group('atlas-chapter-heading', text('02 / OUR PALE BLUE HOME', 'atlas-eyebrow') . text('Everything we know.<br>Seen differently.', '', 2)) . single('PIA18033', 'story', 'atlas-feature'));
    $landing .= group('atlas-chapter atlas-chapter-dark', group('atlas-chapter-heading', text('03 / INTO THE UNKNOWN', 'atlas-eyebrow') . text('Beyond the familiar.', '', 2)) . single('PIA17172', 'story', 'atlas-feature atlas-feature-reverse'));
    $landing .= group('atlas-archive-section', text('04 / FOLLOW YOUR CURIOSITY', 'atlas-eyebrow') . text('There is always more<br>to discover.', '', 2) . remote('nasa-archive', ['search' => 'nebula', 'page' => 1, 'limit' => 3], image_pattern('card', 'remote-data-blocks/nasa-archive'), 'atlas-grid atlas-teaser') . button('Explore the full archive ↗', $base . 'explore/'));
    $landing .= group('atlas-outro', text('The next discovery<br>starts with a closer look.', '', 2) . button('Find your perspective ↗', $base . 'explore/'));

    $explore = group('atlas-page-intro', text('NASA / IMAGE ARCHIVE', 'atlas-eyebrow') . text('Look closer.', 'atlas-display', 1) . text('From the familiar to the far away. Search NASA’s image library and see where your curiosity takes you.', 'atlas-deck') . markup('nasa-atlas/search', [], '', true));
    $explore .= remote('nasa-archive', ['search' => 'nebula', 'page' => 1, 'limit' => 9], image_pattern('card', 'remote-data-blocks/nasa-archive'), 'atlas-grid atlas-search-grid', ['search_from_url']);

    $detail = group('atlas-detail-intro', text('<a href="' . esc_url($base . 'explore/') . '">← Back to the archive</a>', 'atlas-eyebrow') . text('A closer look.', 'atlas-display', 1));
    $detail .= single('carina_nebula', 'story', 'atlas-detail', ['nasa_id_from_url']);
    $detail .= group('atlas-comparison', text('ANOTHER PERSPECTIVE', 'atlas-eyebrow') . text('A world of our own.', '', 2) . text('One image changes with your journey through the archive. This one stays close to home.', 'atlas-deck') . single('PIA18033', 'hero', 'atlas-pinned'));

    $guide = group('atlas-page-intro', text('BEHIND THE ATLAS', 'atlas-eyebrow') . text('Remote data.<br>Human storytelling.', 'atlas-display', 1) . text('A five-minute tour of Remote Data Blocks 1.9.0-alpha.1. These are ordinary WordPress pages: you control the composition; NASA supplies the image records.', 'atlas-deck'));
    $steps = [
        ['Start with the image.', 'Edit the Atlas page and select a NASA Image block. Open “Change data”, search “Saturn” or “Cosmic Cliffs”, then select a record. Titles, images, descriptions, credits, and links update together. Browse Webb images or enter a NASA ID manually to try the other selectors.'],
        ['Keep the layout. Change the source.', 'Inside the NASA block, select an Image, Heading, Paragraph, or Button. In the Remote Data panel, inspect its binding. Change a field, Apply it, or Cancel to keep the original. Image URL and alt text are separate bindings.'],
        ['Try compatible and typed fields.', 'Select a bound button and bind its text to NASA title. The alpha suggests semantic matches while also offering compatible scalar fields. In an editorial image story, select Record facts: Archive year is an integer and Image present is a boolean. The bindings preserve those types instead of turning everything into strings.'],
        ['Tell your own story.', 'Edit the unbound headline, introduction, colors, or spacing as ordinary blocks. Insert a NASA Image block and try the Editorial story, Cinematic plate, and Compact card patterns. Add an inline remote field to a sentence from the rich-text toolbar.'],
        ['Explore a real collection.', 'The Explore page repeats one core-block card template across a NASA query. Search “Saturn”, follow Next, and open an image. Search and pagination are real queries, not a static grid.'],
        ['One template. Many perspectives.', 'The image page opts into the NASA ID URL override. Its pinned Earth block does not. Open two different images: the main record changes, but the comparison stays fixed. Overrides are opt-in per block.'],
    ];
    foreach ($steps as $i => [$heading, $copy]) { $guide .= group('atlas-guide-step', text(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT), 'atlas-step-number') . text($heading, '', 2) . text($copy)); }
    $inline = '<remote-data-blocks-inline-field data-query="' . esc_attr(wp_json_encode(['remoteData' => ['blockName' => 'remote-data-blocks/nasa-image', 'queryInputs' => [['nasa_id' => 'carina_nebula']]], 'selectedField' => 'credit', 'type' => 'field'])) . '">NASA ESA CSA STScI</remote-data-blocks-inline-field>';
    $guide .= group('atlas-guide-step', text('What powers this demo', '', 2) . text('NASA Image and Video Library → typed RDB HTTP queries → core block bindings → editable WordPress pages. One-hour metadata caching. Search/list/manual record selection, reusable patterns, collection templates, native pagination, empty/error states, inline-binding authoring, and per-instance URL overrides.') . text('Inline binding in ordinary prose: the Cosmic Cliffs image is credited to ' . $inline . '. This credit is a remote field, not a manually maintained sentence.') . text('The demo canvas, CSS, and robots noindex apply only to these four pages. Existing content, navigation, and theme remain unchanged. This is an independent demonstration, not an official NASA website. Images retain their record-specific credits.') . button('Open the Atlas ↗', $base));
    return ['atlas' => $landing, 'explore' => $explore, 'image' => $detail, 'editor-guide' => $guide];
}

function seed_pages(): array {
    $ids = (array) get_option('nasa_atlas_page_ids', []);
    $paths = ['atlas' => 'nasa-atlas', 'explore' => 'nasa-atlas/explore', 'image' => 'nasa-atlas/image', 'editor-guide' => 'nasa-atlas/editor-guide'];
    foreach ($paths as $key => $path) {
        $post = get_page_by_path($path);
        if ($post && get_post_meta($post->ID, '_nasa_atlas_owned', true) !== 'yes') { throw new \RuntimeException('Refusing to overwrite an existing page: ' . $path); }
    }
    // Prepare all remote data before the first page write; failed NASA requests leave the site unchanged.
    $content = page_contents();
    $titles = ['atlas' => 'NASA Atlas', 'explore' => 'Explore the NASA Archive', 'image' => 'NASA Image — A Closer Look', 'editor-guide' => 'NASA Atlas — Editor Guide'];
    foreach ($paths as $key => $path) {
        $existing = get_page_by_path($path);
        if ($existing) { $ids[$key] = $existing->ID; continue; }
        $id = wp_insert_post(wp_slash(['post_type' => 'page', 'post_status' => 'publish', 'post_title' => $titles[$key], 'post_name' => $key === 'atlas' ? 'nasa-atlas' : $key, 'post_parent' => $key === 'atlas' ? 0 : ($ids['atlas'] ?? 0), 'post_content' => $content[$key], 'comment_status' => 'closed', 'ping_status' => 'closed']), true);
        if (is_wp_error($id)) { throw new \RuntimeException($id->get_error_message()); }
        update_post_meta($id, '_nasa_atlas_owned', 'yes');
        update_post_meta($id, '_wp_page_template', TEMPLATE);
        $ids[$key] = $id;
        update_option('nasa_atlas_page_ids', $ids, false);
    }
    return $ids;
}
