<?php

// src/Helper/ArrayHelper.php

namespace Application\Helper;

/**
 * Description of ArrayHelper
 *
 * @author alex
 */
class ArrayHelper
{

    /**
     * Group array by subarray values
     *
     * @param array $arr
     * @param Callable|string $criteria
     * @return array
     */
    public static function groupBy($arr, $criteria): array
    {
        return array_reduce($arr, function ($accumulator, $item) use ($criteria) {
            $key = (is_callable($criteria)) ? $criteria($item) : $item[$criteria];
            if (!array_key_exists($key, $accumulator)) {
                $accumulator[$key] = [];
            }
            array_push($accumulator[$key], $item);
            return $accumulator;
        }, []);
    }

    /**
     * filter tree out of a flat array
     * @author plusweb
     * @param array $elements
     * @param type $parentId
     * @param array keys $categoriesHasProduct
     * @return array
     */
    public static function filterTree(array $elements, $parentId = 0, $categoriesHasProduct = [])
    {
        $return = [];
        if ($elements[$parentId]) {
            foreach ($elements[$parentId] as $element) {
                //exit (print_r($element['id']));
                $isHasProduct = self::isHasProduct($elements, $element['id'], $categoriesHasProduct);
                if (!empty($categoriesHasProduct[$element['id']]) or $isHasProduct) {
                    $children = self::filterTree($elements, $element['id'], $categoriesHasProduct);
                    $element['children'] = $children;
                    $return[] = $element;
                }
            }
        }
        return $return;
    }

    private function isHasProduct(array $elements, $parentId, array $categoriesHasProduct, $return = false)
    {
//        if ($return) {
//            return true;
//        }
        if (!$return and !empty($elements[$parentId])) {
            foreach ($elements[$parentId] as $element) {
                if (!empty($categoriesHasProduct[$element['id']])) {
                   return true;
                }
            $return = self::isHasProduct($elements, $element['id'], $categoriesHasProduct, $return);
            }
        }
        return $return;
    }
    /**
     * Build tree out of a flat array
     * @author Kraskov
     * @param array $elements
     * @param mixed $parentId
     * @return array
     */
    public static function buildTree(array $elements, $parentId = 0, $parentKey = 'parent_id', $key = 'id')
    {
        $branch = [];
        if (!empty($elements[$parentId])){
            foreach ($elements[$parentId] as $element) {
                    $children = self::buildTree($elements, $element[$key], $parentKey, $key);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
               }
        }
        return $branch;
    }
    
    public static function buildTreeAlex(array $elements, $parentId = 0, $parentKey = 'parent_id', $key = 'id')
    {
        $branch = [];
      
            foreach ($elements as $element) {
                if ($element[$parentKey] == $parentId) {
                    $children = self::buildTree($elements, $element[$key], $parentKey, $key);
                    if ($children) {
                        $element['children'] = $children;
                    }
                    $branch[] = $element;
                
                }
        }
        return $branch;
    }
    
    /**
     * Search tree haystack for given needle
     *
     * @param int $needle
     * @param array $haystack
     * @param string $nodeKey
     * @param string $childrenKey
     * @return boolean
     */
    public static function searchTree($needle, $haystack, $nodeKey = 'id', $childrenKey = 'children')
    {
        foreach ($haystack as $node) {
            if ($node[$nodeKey] == $needle) {
                return $node;
            } elseif (isset($node[$childrenKey])) {
                $result = self::searchTree($needle, $node[$childrenKey], $nodeKey, $childrenKey);
                if ($result !== false) {
                    return $result;
                }
            }
        }

        return false;
    }

    /**
     * Get parents for given node in the hierarchy
     *
     * @param array $node
     * @param array $hierarchy
     * @param array $allParentIds
     * @param string $nodeKey
     * @param string $parentKey
     * @return array
     */
    public static function getParents($node, $hierarchy, $allParentIds = [], $nodeKey = 'id', $parentKey = 'parent_id', $childrenKey = 'children')
    {
        if ($node[$parentKey] != 0) {
            $parentNode = self::searchTree($node[$parentKey], $hierarchy, $nodeKey, $childrenKey);
            $allParentIds[] = $parentNode[$nodeKey];
            $result = self::getParents($parentNode, $hierarchy, $allParentIds, $nodeKey, $parentKey, $childrenKey);

            if ($result !== false) {
                return $result;
            }
        }

        return $allParentIds;
    }
    
    public static function extractProdictsId ($products)
    {
        $filtredProducts = [];
        foreach ($products as $p) {
            $filtredProducts[] = $p["product_id"];
        }
        return $filtredProducts;
    }


}
