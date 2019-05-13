<?php

add_action( 'wp_enqueue_scripts', function() {
    wp_deregister_script('wp-embed');
}, 100 );
add_action( 'init', function() {
    remove_action( 'wp_head', 'wp_oembed_add_host_js' ); 
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );        
    remove_action( 'rest_api_init', 'wp_oembed_register_route' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
    add_filter( 'embed_oembed_discover', '__return_false' );            
});