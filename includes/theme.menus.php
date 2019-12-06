<?php

Class MenuHandler extends Singleton 
{

    public function __construct()
    {
        register_nav_menus( 
            array(
                'main'	=>	__('Main Menu')
            )
        );
    }

    public static function getMenuItems( $location = 'main' )
    {
        $locations = get_nav_menu_locations();
        $object = wp_get_nav_menu_object( $locations[$location] );
        $items   = wp_get_nav_menu_items($object->name);
        $result  = array();
        $parents = array();
		$active = null;

        if(!empty($items)){
            _wp_menu_item_classes_by_context($items);
        }

        if (is_array($items)) {
            foreach ($items as $itm) {
                $parents[$itm->ID] = $itm->menu_item_parent;
            }
            foreach ($items as $itm) {
                $tmp = array(
                    'id' => $itm->ID,
                    'parent' => $itm->menu_item_parent,
                    'object_id' => $itm->object_id,
                    'title' => $itm->title ? $itm->title : $itm->post_title,
                    'url' => $itm->url ? $itm->url : 'javascript:void(0)',
                    'target' => $itm->target ? $itm->target : '',
                    'classes' => is_array($itm->classes) ? implode(' ', $itm->classes) : '',
                    'active' => false,
                    'type' => $itm->object
                );
                
                if ($itm->menu_item_parent == 0) {
                    $children = isset($result[$itm->ID]) ? $result[$itm->ID] : array();
                    $result[$itm->ID] = array_merge($tmp, $children);
                    if(stristr($tmp['classes'],'current-menu-item') !== false){
                        $result[$itm->ID]['active'] = true;
                    }

                } else {
                    if ($parents[$itm->menu_item_parent] == 0) {
                        $result[$itm->menu_item_parent]['children'][$itm->ID] = $tmp;
                        if(stristr($tmp['classes'],'current-menu-item') !== false){
                            $result[$itm->menu_item_parent]['children'][$itm->ID]['active'] = true;
                            $result[$itm->menu_item_parent]['active'] = true;
                        }
                    } else {
                        $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['children'][$itm->ID] = $tmp;
                        if(stristr($tmp['classes'],'current-menu-item') !== false){
                            $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['children'][$itm->ID]['active'] = true;
                            $result[$parents[$itm->menu_item_parent]]['children'][$itm->menu_item_parent]['active'] = true;
                            $result[$parents[$itm->menu_item_parent]]['active'] = true;
                        }
                    }
                }
            }
        }

        return apply_filters( 'menu_items', $result, $location, $active ); 
    }
}

MenuHandler::getInstance();