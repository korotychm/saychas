<?php

// src/Model/Repository/HandbookRelatedProductRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Sql\Select;
use Laminas\Db\Sql\Join;
use Application\Model\Entity\HandbookRelatedProduct;
//use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;

class HandbookRelatedProductRepository extends Repository implements HandbookRelatedProductRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product";

    /**
     * @var HandbookRelatedProduct
     */
    protected HandbookRelatedProduct $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param HandbookRelatedProduct $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            HandbookRelatedProduct $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Find min and max price value by category
     * 
     * Example usage: $categoryId = '000000002'
     * $categoryTree may be obtained like the following
     * $categoryTree = $this->categoryRepository->findCategoryTree($categoryId, [$categoryId]);
     * 
     * @param array $categoryTree
     * @return array
     */
    public function findMinMaxPriceValueByCategory(array $categoryTree) : array
    {
        $products = $this->findAll(['where' => ['category_id' => $categoryTree]])->toArray();
        
        usort($products, function($a, $b){
            return $a['price']->getPrice() < $b['price']->getPrice();
        });
     
        reset($products);
        return ['maxprice' => current($products)['price']->getPrice(), 'minprice' => end($products)['price']->getPrice()];
    }
    
//    public function findFilteredProducts($params)
//    {
////        $params = [
////            'category_id' => ['000000006'],
////            'offset' => 0,
////            'limit' => 1,
////            'priceRange' => '5399100, 5399100',
////        ];
////        ['where' => $where, 'limit' => 1, 'offset' => 0 ]
//        
////        $result = $this->db->query("call get_products_by_characteristics()")->execute();
////        return $result;
//        return $this->findAll($params);
//    }

    /**
     * Find all entities in the repository
     * Overrides parent findAll
     * 
     * @param array $params
     * @return Entity[]
     */
    public function findAll($params)
    {
        $join = new Join();
        $join->join(['pri' => 'price'], "{$this->tableName}.id = pri.product_id", ['price'], Select::JOIN_LEFT);
        $params['joins'] = $join;
        return parent::findAll($params);
    }
    
    /**
     * Adds given handbookRelatedProduct into it's repository
     *
     * @param json $content
     */
    public function replace($content)
    {
        return ['result' => false, 'description' => '', 'statusCode' => 405];
    }

}
