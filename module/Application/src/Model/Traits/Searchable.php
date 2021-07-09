<?php

// src/Model/Traits/Searchable.php

namespace Application\Model\Traits;

/**
 * Description of Searchable
 *
 * @author alex
 */
trait Searchable
{

    public static function findFirstOrDefault($params)
    {
        return self::$repository->findFirstOrDefault($params);
    }

    public static function findAll($params)
    {
        return self::$repository->findAll($params);
    }

    public static function find($params)
    {
        return self::$repository->find($params);
    }

    public function persist($params)
    {
        return self::$repository->persist($this, $params);
    }

}
