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

    public static function paginator( $range = 4 )
    {
        global $paged, $wp_query;
    
        if ( !$max_page ) {
            $max_page = $wp_query->max_num_pages;
        }
    
        $data = array();

        if ( $max_page > 1 ) {
            if ( !$paged ) $paged = 1;
    
            if($paged > 1){
                $data['prev'] = get_pagenum_link($paged - 1);
            }
   
            if ( $max_page > $range + 1 ) {
                if ( $paged >= $range ){
                    $data['links'][] = array(
                        'active' => false,
                        'url' => get_pagenum_link(1),
                        'title' => '1'
                    );
                }

                if ( $paged >= ($range + 1) ) {
                    $data['links'][] = array(
                        'active' => false,
                        'title' => '&hellip;'
                    );
                }
            }
    
            $i_start = 1;
            $i_end = $max_page;

            if ( $max_page > $range ) {
                if ( $paged < $range ) {
                    $i_start = 1;
                    $i_end = $range + 1;
                } elseif ( $paged >= ($max_page - ceil(($range/2))) ) {
                    $i_start = $max_page - $range;
                    $i_end = $max_page;
                } elseif ( $paged >= $range && $paged < ($max_page - ceil(($range/2))) ) {
                    $i_start = $paged - ceil($range/2);
                    $i_end = $paged + ceil(($range/2));
                }
            }
            
            for ( $i = $i_start; $i <= $i_end; $i++ ) {
                $data['links'][] = array(
                    'active' => ( $i == $paged ),
                    'url' => get_pagenum_link($i),
                    'title' => $i
                );
            }

            if ( $max_page > $range + 1 ) {
                if ( $paged <= $max_page - ($range - 1) ) {
                    $data['links'][] = array(
                        'active' => false,
                        'title' => '&hellip;'
                    );
                    $data['links'][] = array(
                        'active' => false,
                        'url' => get_pagenum_link($max_page),
                        'title' => $max_page
                    );  
                }
            }
    
            if($paged < $max_page){
                $data['next'] = get_pagenum_link($paged-1);
            }
        }
        $content = ob_get_contents();

        return twig_render('components/pagination.twig', $data, false);
    }
}

ThemeFunctions::getInstance();