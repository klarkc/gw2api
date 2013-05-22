<?php

/**
 * Utility library
 * 
 * Simple utility library with static methods to quickly get some tests up
 * and running.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */

/**
 * Utility library class
 * 
 * A very simple utility class with static methods to make development
 * iterations pretty simple, do some array magic etc. May be removed after
 * stable version.
 * 
 * @package Core
 * @author  Oliver Schwarz <oliver.schwarz@gmail.com>
 */
class Gw2ApiUtil
{

    /**
     * Fetch resource by ID
     * 
     * All static resources look the same in v1, you always have an array of
     * objects, containing an ID and a name. This static method fetches the
     * name of a resource object by its ID.
     * 
     * @param integer $id   ID of resource
     * @param array   $data Resource data, array of objects
     * 
     * @return mixed Name of resource or false (if not found)
     */
    public static function getResourceById($id, array $data)
    {
        foreach ($data as $item) {
            if ($item->id == $id) {
                return $item->name;
            }
        }
        return false;
    }
    
}