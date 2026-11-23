<?php
declare(strict_types=1);
namespace MaxSchmeling\NASAAtlas;
defined('ABSPATH') || exit;

function plain(string $text): string {
    return trim(preg_replace('/\s+/u', ' ', html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? '');
}

function nasa_image(array $item, bool $large = false): string {
    $candidates = array_values(array_filter($item['links'] ?? [], static function ($link): bool {
        $url = $link['href'] ?? '';
        return ($link['render'] ?? '') === 'image' && wp_parse_url($url, PHP_URL_SCHEME) === 'https' && wp_parse_url($url, PHP_URL_HOST) === 'images-assets.nasa.gov' && ($link['rel'] ?? '') !== 'canonical' && (int) ($link['size'] ?? 0) < 1500000;
    }));
    usort($candidates, static function ($a, $b) use ($large): int {
        $rank = static fn($link) => abs((int) ($link['width'] ?? 640) - ($large ? 1920 : 1280));
        return $rank($a) <=> $rank($b);
    });
    return $candidates[0]['href'] ?? '';
}

function fields(): array {
    return [
        'nasa_id' => ['name' => 'NASA ID', 'type' => 'id', 'path' => '$.data[0].nasa_id'],
        'title' => ['name' => 'NASA title', 'type' => 'title', 'path' => '$.data[0].title', 'format' => __NAMESPACE__ . '\\plain'],
        'description' => ['name' => 'Description', 'type' => 'string', 'generate' => static fn($d) => plain($d['data'][0]['description_508'] ?? $d['data'][0]['description'] ?? '')],
        'excerpt' => ['name' => 'Short description', 'type' => 'string', 'generate' => static fn($d) => wp_trim_words(plain($d['data'][0]['description_508'] ?? $d['data'][0]['description'] ?? ''), 30, '…')],
        'image_url' => ['name' => 'NASA image', 'type' => 'image_url', 'generate' => static fn($d) => nasa_image($d)],
        'hero_image_url' => ['name' => 'Large NASA image', 'type' => 'image_url', 'generate' => static fn($d) => nasa_image($d, true)],
        'image_alt' => ['name' => 'Image description', 'type' => 'image_alt', 'generate' => static fn($d) => wp_trim_words(plain($d['data'][0]['description_508'] ?? $d['data'][0]['title'] ?? 'NASA archive image'), 35, '')],
        'credit' => ['name' => 'Image credit', 'type' => 'string', 'generate' => static fn($d) => plain($d['data'][0]['photographer'] ?? $d['data'][0]['secondary_creator'] ?? $d['data'][0]['center'] ?? 'NASA')],
        'date_label' => ['name' => 'Archive record date', 'type' => 'string', 'generate' => static fn($d) => 'Archive date / ' . substr($d['data'][0]['date_created'] ?? '', 0, 10)],
        'archive_year' => ['name' => 'Archive year (integer)', 'type' => 'integer', 'generate' => static fn($d) => (int) substr($d['data'][0]['date_created'] ?? '0', 0, 4)],
        'has_image' => ['name' => 'Image present (boolean)', 'type' => 'boolean', 'generate' => static fn($d) => nasa_image($d) !== ''],
        'detail_url' => ['name' => 'Atlas image page', 'type' => 'button_url', 'generate' => static fn($d) => add_query_arg('nasa_id', $d['data'][0]['nasa_id'] ?? '', home_url('/nasa-atlas/image/'))],
        'source_url' => ['name' => 'Original NASA record', 'type' => 'button_url', 'generate' => static fn($d) => 'https://images.nasa.gov/details/' . rawurlencode($d['data'][0]['nasa_id'] ?? '')],
        'button_label' => ['name' => 'Image link label', 'type' => 'button_text', 'generate' => static fn($d) => 'View image ↗'],
    ];
}

function query_config(bool $single = false): array {
    $config = [
        'display_name' => $single ? 'NASA image by ID' : 'NASA image archive',
        'data_source' => ['display_name' => 'NASA Image and Video Library', 'endpoint' => 'https://images-api.nasa.gov/search'],
        'cache_ttl' => 3600,
        'endpoint' => static function (array $inputs) use ($single): string {
            $args = ['media_type' => 'image'];
            if ($single) {
                $args['nasa_id'] = (string) ($inputs['nasa_id'] ?? 'carina_nebula');
                $args['page_size'] = 1;
            } else {
                $args['q'] = mb_substr(trim((string) ($inputs['search'] ?? 'nebula')), 0, 100) ?: 'nebula';
                $args['page'] = max(1, min(100, (int) ($inputs['page'] ?? 1)));
                $args['page_size'] = max(1, min(24, (int) ($inputs['limit'] ?? 9)));
            }
            return add_query_arg($args, 'https://images-api.nasa.gov/search');
        },
        'input_schema' => $single ? ['nasa_id' => ['name' => 'NASA ID', 'type' => 'id']] : [
            'search' => ['name' => 'Search NASA images', 'type' => 'ui:search_input', 'default_value' => 'nebula'],
            'page' => ['name' => 'Page', 'type' => 'ui:pagination_page', 'default_value' => 1],
            'limit' => ['name' => 'Images per page', 'type' => 'ui:pagination_per_page', 'default_value' => 9],
        ],
        'output_schema' => ['is_collection' => !$single, 'path' => $single ? '$.collection.items[0]' : '$.collection.items[*]', 'type' => fields()],
    ];
    if (!$single) { $config['pagination_schema'] = ['total_items' => ['name' => 'NASA images found', 'type' => 'integer', 'path' => '$.collection.metadata.total_hits']]; }
    return $config;
}

add_action('init', static function (): void {
    if (!function_exists('register_remote_data_block')) { return; }
    // Alpha keys selectors by query class, so list and search need distinct classes.
    if (!class_exists(__NAMESPACE__ . '\\NASAImageSearchQuery')) {
        class NASAImageSearchQuery extends \RemoteDataBlocks\Config\Query\HttpQuery {}
        class NASAImageListQuery extends \RemoteDataBlocks\Config\Query\HttpQuery {}
    }
    // Record selectors need a compact preview, not every long description,
    // generated URL and typed demo field as a separate DataViews column.
    $search = query_config();
    $search['output_schema']['type'] = array_intersect_key(fields(), array_flip(['nasa_id', 'title', 'image_url', 'credit', 'date_label']));
    $list = $search;
    $list['input_schema']['search'] = ['name' => 'Topic', 'type' => 'ui:input', 'default_value' => 'James Webb nebula'];
    $list['input_schema']['limit']['default_value'] = 12;
    $image = register_remote_data_block([
        'name' => 'nasa-image', 'title' => 'NASA Image', 'icon' => 'camera',
        'instructions' => 'Search the NASA archive or enter a NASA ID. Choose a layout, then edit ordinary WordPress blocks. Each bound field updates together.',
        'render_query' => ['query' => query_config(true)],
        'selection_queries' => [
            ['query' => NASAImageListQuery::from_array($list), 'type' => 'list', 'display_name' => 'Browse Webb images'],
            ['query' => NASAImageSearchQuery::from_array($search), 'type' => 'search', 'display_name' => 'Search NASA archive'],
        ],
        'overrides' => [['name' => 'nasa_id_from_url', 'display_name' => 'NASA ID from this page URL', 'help_text' => 'Only on Atlas pages. Other image blocks stay pinned unless opted in.']],
        'patterns' => [
            ['title' => 'NASA editorial image story', 'html' => image_pattern('story')],
            ['title' => 'NASA cinematic image plate', 'html' => image_pattern('hero')],
            ['title' => 'NASA compact archive card', 'html' => image_pattern('card')],
        ],
    ]);
    $archive = register_remote_data_block([
        'name' => 'nasa-archive', 'title' => 'NASA Archive', 'icon' => 'images-alt2',
        'render_query' => ['query' => query_config()],
        'overrides' => [['name' => 'search_from_url', 'display_name' => 'Search from this page URL', 'help_text' => 'Uses the Atlas search form; saved editor search remains the default.']],
        'patterns' => [['title' => 'NASA archive card grid', 'role' => 'inner_blocks', 'html' => image_pattern('card', 'remote-data-blocks/nasa-archive')]],
    ]);
    foreach ([$image, $archive] as $result) { if (is_wp_error($result)) { error_log('NASA Atlas registration: ' . $result->get_error_message()); } }
}, 20);
