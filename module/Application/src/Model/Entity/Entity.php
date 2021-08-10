<?php

// src/Model/Entity/Entity.php

namespace Application\Model\Entity;

use Exception;

/**
 * Base Entity class
 */
class Entity
{

    /**
     * Get primary key
     *
     * @return string
     */
    public function primaryKey()
    {
        return '';
    }

    /**
     * Camelizes strings; converts strings like my_string to MyString
     *
     * @param string $string
     * @return string
     */
    private function camelize($string)
    {
        //$words = explode('_', $string);
        // make a strings first character uppercase
        $words = array_map('ucfirst', explode('_', $string));

        // join array elements with ''
        return implode('', $words);
    }

    /**
     * Magic __get method that calls a getter for specified property
     *
     * @param string $name
     * @return type
     */
    public function __get($name)
    {
        $name = 'get' . $this->camelize($name);
        if (!method_exists($this, $name)) {
            throw new Exception('Method ' . $name . ' does not exist');
        }
        return $this->$name();
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
