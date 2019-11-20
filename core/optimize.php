<?php

Class Optimize
{
    public $themeOptions = null;

    public function __construct()
    {
        global $themeOptions;
        $this->themeOptions = $themeOptions;

        # Remove wp version from the head
        remove_action( 'wp_head', 'wp_generator' ); 
        add_filter( 'the_generator', '__return_empty_string' );

        # Theme supports - https://www.daddydesign.com/wordpress/how-to-add-features-in-wordpress-using-add_theme_support-function/
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );

        # Remove and add js
        if ( !is_admin() ) {
            add_filter( 'wp_enqueue_scripts', array( $this, 'handleJavaScript'), 999 );
        }

        if ( isset($_GET['debug']) && $_GET['debug'] == 1 ) {
            show_admin_bar( true );
        } else {
            show_admin_bar( false );
        }
        $this->handleAdminAssets();
    }

    public function handleAdminAssets()
    {
        if($this->themeOptions['adminAssets']['editorStyle']) {
            $editorStyle = $this->themeOptions['adminAssets']['editorStyle'];
            add_action( 'init', function() use ($editorStyle)
            {
                add_editor_style( $editorStyle );
            });
        }

        if($this->themeOptions['adminAssets']['js'] || $this->themeOptions['adminAssets']['css']) {
            add_action( 'admin_enqueue_scripts', array($this, 'loadAdminAssets') );
        }
    }

    public function loadAdminAssets()
    {
        wp_register_style( 'custom_wp_admin_css', $this->themeOptions['adminAssets']['css'], false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );

        wp_register_script( 'custom_wp_admin_js', $this->themeOptions['adminAssets']['js'], false, '1.0.0' );
        wp_enqueue_script( 'custom_wp_admin_js' );
    }

    public function handleJavaScript()
    {
        if($this->themeOptions['disableJquery']) {
            wp_dequeue_script('jquery');
            wp_deregister_script('jquery');
        }

        if($this->themeOptions['addScriptJs']) {
            $scriptLastModified = filemtime(get_template_directory() . '/assets/js/scripts.min.js');
            wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), $scriptLastModified, true );
        }
    }
}

$optimize = new Optimize();