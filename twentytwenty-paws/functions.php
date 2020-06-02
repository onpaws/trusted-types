<?php
//Paws customization saved here to prevent some things twentytwenty does that I don't want, e.g. naked <script> tags
//Suppress wpemoji (AFAIK is cosmetic)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

// Introduce my trustedTypes Script
function paws_custom_scripts() {
    wp_enqueue_script( 'custom-js', get_stylesheet_directory_uri() . '/trustedTypes.js', array(), '', false );
}
add_action( 'wp_enqueue_scripts', 'paws_custom_scripts' );

// TODO" figure out why this add_filter breaks editing
// Wire in the nonce genereated on the server (see .htaccess)
add_filter( 'script_loader_tag', 'add_nonce_to_script', 10, 3 );
function add_nonce_to_script( $tag, $handle, $src ) {
    global $my_nonce;
	$my_nonce = $_SERVER['UNIQUE_ID'];
    return '<script type="text/javascript" src="' . esc_url( $src ) . '" nonce="' . esc_attr( $my_nonce ) . '"></script>';
}

# Twentytwenty wants to inject more naked <script> tags, one for No JS and one for IE11. Suppress.
# https://github.com/WordPress/WordPress/blob/86b20d565562d5565f7fbe4392adfe75e258d31e/wp-content/themes/twentytwenty/inc/template-tags.php#L616
# https://github.com/WordPress/WordPress/blob/86b20d565562d5565f7fbe4392adfe75e258d31e/wp-content/themes/twentytwenty/functions.php#L234
add_action( 'init', 'remove_actions_parent_theme');
function remove_actions_parent_theme() {
	remove_action( 'wp_head', 'twentytwenty_no_js_class' );
	remove_action( 'wp_print_footer_scripts', 'twentytwenty_skip_link_focus_fix' );
};

# TwentyTwenty remove inline styles
# via https://wordpress.stackexchange.com/questions/353117/wordpress-twentytwenty-theme-inline-css-overriding-css-body
add_action( 'wp_enqueue_scripts', function() {
        $styles = wp_styles();
        $styles->add_data( 'twentytwenty-style', 'after', array() );
}, 20 );
