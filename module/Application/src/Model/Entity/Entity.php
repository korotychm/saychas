<?php
// src/Model/Entity/Entity.php

namespace Application\Model\Entity;

class Entity
{
    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }
    
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}
