<?php
// src/Model/Repository/CharacteristicRepository.php

namespace Application\Helper;
/**
 * Description of ArrayHelper
 *
 * @author alex
 */
class ArrayHelper {
    /**
     * Groups array by subarray values
     * 
     * @param array $arr
     * @param Callable|string $criteria
     * @return array
     */
    public static function groupBy($arr, $criteria): array
    {
        return array_reduce($arr, function($accumulator, $item) use ($criteria) {
            $key = (is_callable($criteria)) ? $criteria($item) : $item[$criteria];
            if (!array_key_exists($key, $accumulator)) {
                $accumulator[$key] = [];
            }
            array_push($accumulator[$key], $item);
            return $accumulator;
        }, []);
    }
}
