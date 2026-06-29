<?php

/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0');

define('COMPRESS_CSS',        true);
define('COMPRESS_SCRIPTS',    true);
define('CONCATENATE_SCRIPTS', true);
define('ENFORCE_GZIP',        true);

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles()
{

    wp_enqueue_style(
        'hello-elementor-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor-theme-style',
        ],
        HELLO_ELEMENTOR_CHILD_VERSION
    );
}
add_action('wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20);


/**
 * Core Enqueue Engine from Manifest or Vite Dev Server
 */
function dionnie_enqueue_from_manifest($entries)
{
    $hot_file = get_stylesheet_directory() . '/public/hot';
    $is_dev   = file_exists($hot_file);

    // --- DEVELOPMENT MODE (VITE HMR) ---
    if ($is_dev) {
        $dev_server = 'http://localhost:5173/';

        // Ensure Vite client is loaded first for HMR
        wp_enqueue_script_module('vite-client', $dev_server . '@vite/client');
        wp_enqueue_script_module('vite-reload', $dev_server . 'src/reload.js');

        foreach ($entries as $entry_key) {
            $file_url = $dev_server . $entry_key;
            $handle   = 'dionnie-' . sanitize_title($entry_key);

            // FIX 1: Allow .css, .scss, and .sass in dev mode
            if (preg_match('/\.(css|scss|sass)$/', $entry_key)) {
                // Vite allows loading CSS/SCSS via script module tag during development
                wp_enqueue_script_module($handle, $file_url);
            }
            // Handle JS/JSX/TS/TSX entry points in dev
            elseif (preg_match('/\.(js|jsx|ts|tsx)$/', $entry_key)) {
                wp_enqueue_script_module($handle, $file_url);
            }
        }
        return; // Bypass production manifest processing
    }

    // --- PRODUCTION MODE ---
    $manifest_path = get_stylesheet_directory() . '/public/build/manifest.json';
    if (! file_exists($manifest_path)) {
        return;
    }

    $manifest_data = json_decode(file_get_contents($manifest_path), true);
    if (json_last_error() !== JSON_ERROR_NONE || ! is_array($manifest_data)) {
        return;
    }

    $base_url = get_stylesheet_directory_uri() . '/public/build/';

    foreach ($entries as $entry_key) {
        if (! isset($manifest_data[$entry_key])) {
            continue;
        }

        $asset = $manifest_data[$entry_key];

        if (empty($asset['isEntry']) || ! isset($asset['file'])) {
            continue;
        }

        $file_url = $base_url . $asset['file'];
        $handle   = 'dionnie-' . sanitize_title($entry_key);

        // FIX 2: Allow .css, .scss, and .sass entry keys in production mode
        if (preg_match('/\.(css|scss|sass)$/', $entry_key)) {
            // Even if the key is .scss, Vite's manifest ['file'] will point to the compiled .css file
            wp_enqueue_style($handle, $file_url, array(), null);
        }
        // Handle JS entry points
        elseif (preg_match('/\.(js|jsx|ts|tsx)$/', $entry_key)) {
            wp_enqueue_script($handle, $file_url, array(), null, true);

            // Enqueue associated CSS for this JS entry
            if (! empty($asset['css']) && is_array($asset['css'])) {
                foreach ($asset['css'] as $index => $css_file) {
                    $css_handle = $handle . '-css-' . $index;
                    wp_enqueue_style($css_handle, $base_url . $css_file, array(), null);
                }
            }
        }
    }
}

/**
 * Enqueue frontend scripts and styles.
 */


function dionnie_enqueue_frontend_assets()
{
    dionnie_enqueue_from_manifest(array(
        'src/js/theme.js',
        'src/css/theme.scss',

    ));
}
add_action('wp_enqueue_scripts', 'dionnie_enqueue_frontend_assets');

/**
 * Enqueue block editor scripts and styles.
 */

/*
 function dionnie_enqueue_editor_assets() {
    dionnie_enqueue_from_manifest( array( 
        'src/js/editor.js', 
        'src/css/editor.css' 
    ) );
}
add_action( 'enqueue_block_editor_assets', 'dionnie_enqueue_editor_assets' );
*/


// Add Shortcode
function custom_mini_cart()
{
    echo '<a href="#" class="dropdown-back" data-toggle="dropdown"> ';
    echo '<i class="fa fa-shopping-cart" aria-hidden="true"></i>';
    echo '<div class="basket-item-count" style="display: inline;">';
    echo '<span class="cart-items-count count">';
    echo WC()->cart->get_cart_contents_count();
    echo '</span>';
    echo '</div>';
    echo '</a>';
    echo '<ul class="dropdown-menu dropdown-menu-mini-cart">';
    echo '<li>';
    echo '<div class="widget_shopping_cart_content">';
    woocommerce_mini_cart();
    echo '</div>';
    echo '</li>';
    echo '</ul>';
}
add_shortcode('custom_techno_mini_cart', 'custom_mini_cart');



function mytheme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'mytheme_add_woocommerce_support');


/**
 * Disable WordPress Core and WooCommerce Block Library CSS
 */
function custom_remove_block_library_css()
{
    // Remove WooCommerce Block Styles
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');

    // Remove WordPress Core Block Styles
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');

    // Optional: Remove Inline Global Styles / Theme JSON CSS if not using block themes
    wp_dequeue_style('global-styles');
    wp_dequeue_style('classic-theme-styles');
}
add_action('wp_enqueue_scripts', 'custom_remove_block_library_css', 999);


function disable_wp_emojis()
{
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');

    // Remove TinyMCE emojis
    add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');

    // Remove DNS prefetch
    add_filter('emoji_svg_url', '__return_false');
}
add_action('init', 'disable_wp_emojis');

function disable_emojis_tinymce($plugins)
{
    if (is_array($plugins)) {
        return array_diff($plugins, array('wpemoji'));
    } else {
        return array();
    }
}



/**
 * Generate meta descriptions without an SEO plugin.
 */
function my_custom_meta_description()
{

    // Prevent duplicates if another plugin/theme already outputs one.
    if (is_admin()) {
        return;
    }

    $description = '';

    // Homepage.
    if (is_front_page() || is_home()) {

        $description = get_bloginfo('description');

        // WooCommerce Product.
    } elseif (function_exists('is_product') && is_product()) {

        $product = wc_get_product(get_queried_object_id());

        if ($product && is_a($product, 'WC_Product')) {

            $description = $product->get_short_description();

            if (empty($description)) {
                $description = $product->get_description();
            }

            $description = $product->get_name() . ' - ' . $description;
        }

        // Posts & Pages.
    } elseif (is_singular()) {

        $post = get_post();

        if ($post) {

            if (! empty($post->post_excerpt)) {
                $description = $post->post_excerpt;
            } else {
                $description = $post->post_content;
            }
        }

        // WooCommerce Product Category.
    } elseif (function_exists('is_product_category') && is_product_category()) {

        $term = get_queried_object();

        if (! empty($term->description)) {
            $description = $term->description;
        } else {
            $description = sprintf(
                'Browse our %s products.',
                single_term_title('', false)
            );
        }

        // WooCommerce Product Tag.
    } elseif (function_exists('is_product_tag') && is_product_tag()) {

        $term = get_queried_object();

        if (! empty($term->description)) {
            $description = $term->description;
        } else {
            $description = sprintf(
                'Browse products tagged %s.',
                single_term_title('', false)
            );
        }

        // Blog Categories.
    } elseif (is_category()) {

        $description = category_description();

        if (empty($description)) {
            $description = sprintf(
                'Articles filed under %s.',
                single_cat_title('', false)
            );
        }

        // Blog Tags.
    } elseif (is_tag()) {

        $description = tag_description();

        if (empty($description)) {
            $description = sprintf(
                'Articles tagged %s.',
                single_tag_title('', false)
            );
        }

        // Generic Archives.
    } elseif (is_archive()) {

        $description = get_the_archive_description();

        if (empty($description)) {
            $description = get_bloginfo('description');
        }
    }

    // Final fallback.
    if (empty($description)) {
        $description = get_bloginfo('description');
    }

    // Cleanup.
    $description = wp_strip_all_tags($description);
    $description = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
    $description = preg_replace('/\s+/', ' ', $description);
    $description = trim($description);

    // Limit to ~160 characters.
    if (mb_strlen($description) > 160) {
        $description = mb_substr($description, 0, 157) . '...';
    }

    printf(
        '<meta name="description" content="%s" />' . "\n",
        esc_attr($description)
    );
}
add_action('wp_head', 'my_custom_meta_description', 5);



// Output minus button before quantity input
add_action('woocommerce_before_quantity_input_field', function () {
    echo '<button type="button" class="qty-set qty-minus">-</button>';
});

// Output plus button after quantity input
add_action('woocommerce_after_quantity_input_field', function () {
    echo '<button type="button" class="qty-set qty-plus">+</button>';
});
