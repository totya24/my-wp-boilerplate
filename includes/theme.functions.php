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

    public static function sendMail($to, $subject, $content = '', $template = 'general'){
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $message = twig_render('mail/'.$template.'.twig', false);
        $result = wp_mail($to, $subject, $message, $headers);
        return $result;
    }
}

ThemeFunctions::getInstance();