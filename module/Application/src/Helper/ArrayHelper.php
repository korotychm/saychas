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
        if (isset($elements[$parentId])) {
            foreach ($elements[$parentId] as $element) {
                //exit (print_r($element['id']));
                $LegalTree = self::LegalTree($elements, $element['id'], $categoriesHasProduct);
                if (!empty($categoriesHasProduct[$element['id']]) || $LegalTree) {
                    $children = self::filterTree($elements, $element['id'], $categoriesHasProduct);
                    $element['children'] = $children;
                    $return[] = $element;
                }
            }
        }
        return $return;
    }

    /**
     *
     * @param array $elements
     * @param string $parentId
     * @param array $categoriesHasProduct
     * @param bulean $return
     * @return boolean
     */
    private function LegalTree(array $elements, $parentId, array $categoriesHasProduct, $return = false)
    {
//        if ($return) {
//            return true;
//        }
        if (!$return and!empty($elements[$parentId])) {
            foreach ($elements[$parentId] as $element) {
                if (!empty($categoriesHasProduct[$element['id']])) {
                    return true;
                }
                $return = self::LegalTree($elements, $element['id'], $categoriesHasProduct, $return);
            }
        }
        return $return;
    }

    /**
     * Build tree out of a flat array
     * 
     * @param array $elements
     * @param string $parentId
     * @return array
     */
    public static function buildTree(array $elements, $parentId = '0', $parentKey = 'parent_id', $key = 'id')
    {
        $branch = [];
        if (!empty($elements[$parentId])) {
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
    
    /**
     * Build tree out of a flat array
     * 
     * @param array $elements
     * @param mixed $parentId
     * @return array
     */
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
     * @param array $allParentsId
     * @param string $nodeKey
     * @param string $parentKey
     * @return array
     */
    public static function getParents($node, $hierarchy, $allParentsId = [], $nodeKey = 'id', $parentKey = 'parent_id', $childrenKey = 'children')
    {
        if ($node[$parentKey] != 0) {
            $parentNode = self::searchTree($node[$parentKey], $hierarchy, $nodeKey, $childrenKey);
            $allParentsId[] = $parentNode[$nodeKey];
            $result = self::getParents($parentNode, $hierarchy, $allParentsId, $nodeKey, $parentKey, $childrenKey);

            if ($result !== false) {
                return $result;
            }
        }

        return $allParentsId;
    }

    /**
     * 
     * @param array $products
     * @param string $key
     * @return array
     */
    public static function extractId($products, $key = "product_id")
    {
        foreach ($products as $p) {
            $filtredProducts[] = $p[$key];
        }
     
        return $filtredProducts ?? [];
    }
    
    /**
     * 
     * @param string $strHeaders
     * @return array
     */
    public function parseCookies($strHeaders)
    {
        $result = array();
        $aPairs = explode(';', $strHeaders);
        foreach ($aPairs as $pair) {
            $aKeyValues = explode('=', trim($pair), 2);
            if (count($aKeyValues) == 2) {
                switch ($aKeyValues[0]) {
                    case 'path':
                    case 'domain':
                        $aTmp[trim($aKeyValues[0])] = urldecode(trim($aKeyValues[1]));
                        break;
                    case 'expires':
                        $aTmp[trim($aKeyValues[0])] = strtotime(urldecode(trim($aKeyValues[1])));
                        break;
                    default:
                        $aTmp['name'] = trim($aKeyValues[0]);
                        $aTmp['value'] = trim($aKeyValues[1]);
                        $cookies[$aKeyValues[0]] = trim($aKeyValues[1]);
                        break;
                }
            }
            $result[] = $aTmp;
        }
        return ["cookies"=> $cookies,  "fullparams"=>$result ] ;
    }

}
