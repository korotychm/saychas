<?php
// src/Model/Entity/Entity.php

namespace Application\Model\Entity;

/**
 * Base Entity class
 */
class Entity
{
    /**
     * Camelizes strings; converts strings like my_string to MyString
     * 
     * @param type $string
     * @return type
     */
    private function camelize($string)
    {
        $words = explode('_', $string);

        // make a strings first character uppercase
        $words = array_map('ucfirst', $words);

        // join array elements with ''
        $string = implode('', $words);
        
        return $string;
    }
    
    /**
     * Magic __get method that calls a getter for specified property
     * 
     * @param string $name
     * @return type
     */
    public function __get($name)
    {
        if (isset($this->$name)) {
            $name = 'get'.$this->camelize($name);
            return $this->$name();
        }
        return null;
    }
    
    /**
     * Magic __set method that is not used right now
     */
    /**
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
    */

}
