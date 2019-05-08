<?php

Class PiklistMods extends Singleton 
{

    private $isPiklistActive = false;

    public function __construct()
    {
        global $themeOptions;
        if($themeOptions['usePiklist'] != true) return false;

        $this->piklistCheck();

            
            add_filter('piklist_part_data', array($this, 'customCommentBlock'), 10, 2);
            add_filter('piklist_part_process_callback', array($this, 'showOnlyFrontpage'), 10, 2);
    }

    public function piklistCheck()
    {
        if(is_admin()){
            //$this->isPiklistActive =  is_plugin_active('piklist');
        }
    }
    
    public function customCommentBlock( $data, $folder )
    {
        if($folder != 'meta-boxes') {
            return $data;
        }
        
        $data['frontpage'] = 'Frontpage';
        return $data;
    }
    
    public function showOnlyFrontpage( $part, $type )
    {
        global $post;
        
        if($type != 'meta-boxes') {
            return $part;
        }
        
        if ($part['data']['frontpage']) {
            $homepageId = get_option( 'page_on_front' );
            if ($post->ID != $homepageId) {
                $part['data']['role'] = 'no-role';
            }
        }
        return $part;
    }
}

PiklistMods::getInstance();