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

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_CHILD_VERSION', '2.0.0' );

/**
 * Load child theme scripts & styles.
 *
 * @return void
 */
function hello_elementor_child_scripts_styles() {

	wp_enqueue_style(
		'hello-elementor-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		[
			'hello-elementor-theme-style',
		],
		HELLO_ELEMENTOR_CHILD_VERSION
	);

}
add_action( 'wp_enqueue_scripts', 'hello_elementor_child_scripts_styles', 20 );


/**
 * Core Enqueue Engine from Manifest or Vite Dev Server
 */
function dionnie_enqueue_from_manifest( $entries ) {
    $hot_file = get_stylesheet_directory() . '/public/hot';
    $is_dev   = file_exists( $hot_file );


   //wp_enqueue_style(  'dionnie-reset', get_stylesheet_directory_uri() . '/src/css/reset.css' );
   // wp_enqueue_style(  'dionnie-theme', get_stylesheet_directory_uri() . '/src/css/theme.css' );

    // --- DEVELOPMENT MODE (VITE HMR) ---
    if ( $is_dev ) {
        $dev_server = 'http://localhost:5173/';
        
        // Ensure Vite client is loaded first for HMR
        wp_enqueue_script_module( 'vite-client', $dev_server . '@vite/client' );
		wp_enqueue_script_module( 'vite-reload', $dev_server . 'src/reload.js' );

		

        foreach ( $entries as $entry_key ) {
            $file_url = $dev_server . $entry_key;
            $handle   = 'dionnie-' . sanitize_title( $entry_key );

            // Handle CSS entry points in dev
            if ( preg_match( '/\.css$/', $entry_key ) ) {
                // Vite allows loading CSS via script module tag during development
                wp_enqueue_script_module( $handle, $file_url );
            }
            // Handle JS/JSX/TS/TSX entry points in dev
            elseif ( preg_match( '/\.(js|jsx|ts|tsx)$/', $entry_key ) ) {
                wp_enqueue_script_module( $handle, $file_url );
            }
        }
        return; // Bypass production manifest processing
    }

    // --- PRODUCTION MODE ---
    $manifest_path = get_stylesheet_directory() . '/public/build/manifest.json';
    if ( ! file_exists( $manifest_path ) ) {
        return;
    }

    $manifest_data = json_decode( file_get_contents( $manifest_path ), true );
    if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $manifest_data ) ) {
        return;
    }

    $base_url = get_stylesheet_directory_uri() . '/public/build/';

    foreach ( $entries as $entry_key ) {
        if ( ! isset( $manifest_data[ $entry_key ] ) ) {
            continue;
        }

        $asset = $manifest_data[ $entry_key ];

        if ( empty( $asset['isEntry'] ) || ! isset( $asset['file'] ) ) {
            continue;
        }

        $file_url = $base_url . $asset['file'];
        $handle   = 'dionnie-' . sanitize_title( $entry_key );

        // Handle CSS entry points
        if ( preg_match( '/\.css$/', $entry_key ) ) {
            wp_enqueue_style( $handle, $file_url, array(), null );
        }
        // Handle JS entry points
        elseif ( preg_match( '/\.(js|jsx|ts|tsx)$/', $entry_key ) ) {
            wp_enqueue_script( $handle, $file_url, array(), null, true );

            // Enqueue associated CSS for this JS entry
            if ( ! empty( $asset['css'] ) && is_array( $asset['css'] ) ) {
                foreach ( $asset['css'] as $index => $css_file ) {
                    $css_handle = $handle . '-css-' . $index;
                    wp_enqueue_style( $css_handle, $base_url . $css_file, array(), null );
                }
            }
        }
    }
}

/**
 * Enqueue frontend scripts and styles.
 */


function dionnie_enqueue_frontend_assets() {
    dionnie_enqueue_from_manifest( array( 
        'src/js/theme.js', 
        'src/css/theme.css',
    
    ) );
}
add_action( 'wp_enqueue_scripts', 'dionnie_enqueue_frontend_assets' );

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
function custom_mini_cart() { 
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
add_shortcode( 'custom_techno_mini_cart', 'custom_mini_cart' );



function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );


/**
 * Disable WordPress Core and WooCommerce Block Library CSS
 */
function custom_remove_block_library_css() {
    // Remove WooCommerce Block Styles
    wp_dequeue_style( 'wc-blocks-style' );
    wp_dequeue_style( 'wc-blocks-vendors-style' );
    
    // Remove WordPress Core Block Styles
    wp_dequeue_style( 'wp-block-library' );
    wp_dequeue_style( 'wp-block-library-theme' );
    
    // Optional: Remove Inline Global Styles / Theme JSON CSS if not using block themes
    wp_dequeue_style( 'global-styles' );
    wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'custom_remove_block_library_css', 999 );
