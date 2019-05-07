<?php

Class ThemeFunctions extends Singleton 
{
    public function __construct()
    {
        // FOO
    }

    public static function getTitle( $default = null )
    {
        $title = get_the_title();
        
        if(is_archive() && !is_admin()){
            $objType = get_queried_object()->name;
            $cptObj = get_post_type_object( $objType );
            $title = $cptObj->label;
        }
        
        return $title;
    }
}

ThemeFunctions::getInstance();