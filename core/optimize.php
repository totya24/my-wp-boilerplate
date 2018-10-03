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
        add_filter('the_generator', function() { return ''; });

        # Theme supports - https://www.daddydesign.com/wordpress/how-to-add-features-in-wordpress-using-add_theme_support-function/
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );
        add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
        
        # Allow upload svg files
        add_filter('upload_mimes', function ($mimes) {
            $mimes['svg'] = 'image/svg+xml';
            return $mimes;
        });

        # Remove and add js
        if ( !is_admin() ) {
            add_filter( 'wp_enqueue_scripts', array( $this, 'handleJavaScript'), 999 );
        }

        # Sanitize uploaded image name
        add_action('wp_handle_upload_prefilter', array( $this, 'uploadFilter') );

        if ( isset($_GET['debug']) && $_GET['debug'] == 1 ) {
			show_admin_bar( true );
		} else {
			show_admin_bar( false );
        }
        
        if($this->themeOptions['disableXMLRPC']){
            $this->disableXMLRPC();
        }
        if($this->themeOptions['disableEmojis']){
            $this->disableEmojis();
        }
        if($this->themeOptions['disableComments']){
            $this->disableComments();
        }
        if($this->themeOptions['disableFeeds']){
            $this->disableFeeds();
        }
        if($this->themeOptions['disableRestApi']){
            $this->disableRestApi();
        }
        if($this->themeOptions['disableEmbed']){
            $this->disableEmbed();
        }
        if($this->themeOptions['slowHeartBeat']){
            $this->slowHeartbeat();
        }
        if($this->themeOptions['hidePosts']){
            $this->hidePosts();
        }


        $this->handleAdminAssets();
    }

    public function hidePosts()
    {
        add_action( 'admin_menu', function() {
            remove_menu_page('edit.php');
        } );
    }

    public function handleAdminAssets()
    {
        if($this->themeOptions['adminAssets']['editorStyle']){
            $editorStyle = $this->themeOptions['adminAssets']['editorStyle'];
            add_action( 'init', function() use ($editorStyle)
            {
                add_editor_style( $editorStyle );
            });
        }

        if($this->themeOptions['adminAssets']['js'] || $this->themeOptions['adminAssets']['css'])
        {
            add_action( 'admin_enqueue_scripts', array($this, 'loadAdminAssets') );
        }
    }

    public function loadAdminAssets(){
        wp_register_style( 'custom_wp_admin_css', $this->themeOptions['adminAssets']['css'], false, '1.0.0' );
        wp_enqueue_style( 'custom_wp_admin_css' );

        wp_register_script( 'custom_wp_admin_js', $this->themeOptions['adminAssets']['js'], false, '1.0.0' );
        wp_enqueue_script( 'custom_wp_admin_js' );
    }

    public function handleJavaScript()
    {
        if($this->themeOptions['disableJquery']){
            wp_dequeue_script('jquery');
            wp_deregister_script('jquery');
        }

        if($this->themeOptions['addScriptJs']){
            $scriptLastModified = filemtime(get_template_directory() . '/assets/js/scripts.min.js');
            wp_enqueue_script( 'scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), $scriptLastModified, true );
        }
    }
    
    public function uploadFilter( $file )
    {
		$path = pathinfo($file['name']);
		$new_filename = preg_replace('/.' . $path['extension'] . '$/', '', $file['name']);
		$file['name'] = sanitize_title($new_filename) . '.' . strtolower($path['extension']);

		return $file;
    }

    private function disableXMLRPC()
    {
        add_filter( 'xmlrpc_enabled', '__return_false' );
        remove_action( 'wp_head', 'rsd_link' ); 
        remove_action( 'wp_head', 'wlwmanifest_link' ); 
        remove_action( 'wp_head', 'wp_shortlink_wp_head' );
        remove_action( 'wp_head', 'wp_resource_hints', 2 );
    }

    public function disableEmojis()
    {
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    }

    private function disableRestApi()
    {
        // Remove the references to the JSON api
        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
        remove_action( 'rest_api_init', 'wp_oembed_register_route' );
        add_filter( 'embed_oembed_discover', '__return_false' );
        remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
        remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
        remove_action( 'wp_head', 'wp_oembed_add_host_js' );
        remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );
        // Disable the API completely
        add_filter('json_enabled', '__return_false');
        add_filter('json_jsonp_enabled', '__return_false');
        add_filter('rest_enabled', '__return_false');
        add_filter('rest_jsonp_enabled', '__return_false');      
    } 

    private function disableEmbed()
    {
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
        
    } 

    private function disableFeeds()
    {        
        remove_action( 'wp_head', 'feed_links_extra', 3 ); 
        remove_action( 'wp_head', 'feed_links', 2 );   
        add_action( 'do_feed', array($this, 'disableFeedsHook'), 1 );
        add_action( 'do_feed_rdf', array($this, 'disableFeedsHook'), 1 );
        add_action( 'do_feed_rss', array($this, 'disableFeedsHook'), 1 );
        add_action( 'do_feed_rss2', array($this, 'disableFeedsHook'), 1 );
        add_action( 'do_feed_atom', array($this, 'disableFeedsHook'), 1 );        
    }  
    
    private function disableComments()
    {
        if( is_admin() ) {
            update_option( 'default_comment_status', 'closed' ); 
        }
        
        add_filter( 'comments_open', '__return_false', 20, 2 );
        add_filter( 'pings_open', '__return_false', 20, 2 );
        
        add_action( 'admin_init', function() {
            
            $post_types     = get_post_types();
            
            foreach($post_types as $post_type) {
                if (post_type_supports($post_type, 'comments') ) {
                    remove_post_type_support($post_type, 'comments');
                    remove_post_type_support($post_type, 'trackbacks');
                }
            }
            
        }); 
        
        add_action( 'admin_menu', function() {
            remove_menu_page('edit-comments.php');
        } );
        
        add_action( 'wp_before_admin_bar_render', function() {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('comments');              
        } );              
        
    }

    private function slowHeartbeat()
    {
         add_filter( 'heartbeat_settings', function($settings) {
            $settings['interval'] = 60; 
            return $settings;
        } );
    }  

}

$optimize = new Optimize();