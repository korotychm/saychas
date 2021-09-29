<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\Entity\Setting;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Service\CommonHelperFunctionsService;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\Entity\StockBalance;
use Application\Model\Entity\ProductHistory;
use Laminas\Authentication\AuthenticationService;
use Laminas\Json\Json;
use Laminas\Db\Sql\Where;
use Application\Helper\ArrayHelper;
use Application\Resource\Resource;

//use Application\Helper\StringHelper;

class ProductCardsController extends AbstractActionController {

    private $categoryRepository;
    private $characteristicRepository;
    private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    private $authService;
    private $commonHelperFuncions;

    public function __construct(
      CategoryRepositoryInterface $categoryRepository, CharacteristicRepositoryInterface $characteristicRepository, HandbookRelatedProductRepositoryInterface $handBookProduct, $entityManager,$config, AuthenticationService $authService,CommonHelperFunctionsService $commonHelperFuncions ) 
      {
        $this->categoryRepository = $categoryRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->authService = $authService;
        $this->commonHelperFuncions = $commonHelperFuncions;
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(ProductCharacteristic::class);
        $this->entityManager->initRepository(StockBalance::class);
         $this->entityManager->initRepository(ProductHistory::class);
      }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductCategoriesAction() {
        $post = $this->getRequest()->getPost();
        $categoryId = $post->categoryId;
        if (empty($params = Setting::find(['id' => 'main_menu']))) {
            return new JsonModel([]);
        }
        $categories = Json::decode($params->getValue(), Json::TYPE_ARRAY);
        $category = $categories[$categoryId]["categories"];
        foreach ($category as $item) {
            $param[] = $item["id"];
        }
        return new JsonModel(["products" =>$this->getProductsCategories($param)]);
    }
    
    
    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsTopAction() {
        return new JsonModel(["products" => $this->getProductsTop()]);
    }

    
    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsBrandAction() {
        $post = $this->getRequest()->getPost();
        if (empty($post->brandId)) {
            return new JsonModel([]);
        }
        return new JsonModel(["products" =>$this->getProductsBrand(['brand_id' => $post->brandId, 'category_id' => $post->categoryId])]);
    }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsStoreAction() {
        $post = $this->getRequest()->getPost();
        if (empty($post->storeId)) {
            return new JsonModel([]);
        }
        return new JsonModel(["products" =>$this->getProductsStore(['store_id' => $post->storeId, 'category_id' => $post->categoryId])]);
        //return new JsonModel($post);
    }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsProviderAction() {
        $post = $this->getRequest()->getPost();
        if (empty($post->providerId)) {
            return new JsonModel([]);
        }
        return new JsonModel(["products" => $this->getProductsProvider(['provider_id' => $post->providerId, 'category_id' => $post->categoryId])]);
    }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsCatalogAction() {
        $post = $this->getRequest()->getPost()->toArray();
        return new JsonModel(["products" => $this->getProductsCatalog($post)]);
    }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsSaleAction() {
        return new JsonModel(["products" => $this->getProductsSale()]);
    }
    
    
    /**
     * Return where clause to filter products by price and category
     *
     * @param array $params
     * @return Where
     */
    private function getWhereCatalog($params): Where {
        $category_id = $params['category_id'];
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $where = new Where();
        list($low, $high) = explode(';', $params['priceRange']);
        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
        $where->in('category_id', $categoryTree);
        $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];
        return (!empty($characteristics)) ? $this->filterWhere($characteristics, $where) : $where;
    }

    /**
     * Return where clause filter products by selected characteristics
     *
     * @param array $characteristics, object Where
     * @return object Where
     */
    private function filterWhere($characteristics, $where): Where {
        $inChars = array_keys($characteristics);
        $legalProducts = $this->getFiltredProductsId(['characteristic_id' => $inChars]);
        //$groupChars = [0];
        while (list($key, $value) = each($characteristics)) {
            if (empty($value) or empty($found = ProductCharacteristic::find(['characteristic_id' => $key]))) {
                continue;
            }
            $filterWhere = new Where();
            $type = $found->getType();
            $filterWhere->equalTo('characteristic_id', $found->getCharacteristicId($key));
            if ($type == CharacteristicRepository::INTEGER_TYPE) {
                //reset($value);
                list($min, $max) = explode(';', current($value));
                $filterWhere->between('value', $min * 1, $max * 1);
            } elseif ($type == CharacteristicRepository::BOOL_TYPE) {
                $filterWhere->equalTo('value', $value);
            } else {
                $filterWhere->in('value', $value);
                //$groupChars[] = $key;
            }
            $groupChars[] = $key;
            $legalProducts = array_intersect($legalProducts, $this->getFiltredProductsId($filterWhere));
        }
        $subWhere = new Where();
        $productsFiltred = $this->getFiltredProductsId($subWhere->in('characteristic_id', $groupChars));
        //$productsFiltredDefault =  empty($productsFiltred) ? [0] : $productsFiltred; 
        $nest = $where->nest();
        $nest->in('product_id', $legalProducts)->or->notIn('product_id', $productsFiltred)->unnest();
        return $where;
    }

    /**
     * Return  filtered products id
     *
     * @param object Where
     * @return array
     */
    private function getFiltredProductsId($where) {
        $products = ProductCharacteristic::findAll(["where" => $where, "columns" => ['product_id'], "group" => "product_id"])->toArray();
        return ArrayHelper::extractId($products);
    }

    /**
     * Return where clause for query
     *
     * @param array $params
     * @return Where
     */
    private function getWhereCategories($params): Where {
        $where = new Where();
        $where->in('category_id', $params);
        return $where;
    }

    /**
     * Return where clause for query
     *
     * @param array $params
     * @return Where
     */
    private function getWhereBrand($params): Where {
        $where = new Where();
        $where->equalTo('brand_id', $params['brand_id']);
        if (!empty($params['category_id'])) {
            $where->equalTo('category_id', $params['category_id']);
        }
        return $where;
    }

    /**
     * Return where clause for query
     *
     * @param array $params
     * @return Where
     */
    private function getWhereProvider($params): Where {
        $where = new Where();
        $where->equalTo('product.provider_id', $params['provider_id']);
        //exit ($params['provider_id']);
        if (!empty($params['category_id'])) {
            $where->equalTo('category_id', $params['category_id']);
        }
        return $where;
    }

    /**
     * Return where clause for query
     *
     * @param array $params
     * @return Where
     */
    private function getWhereStore($params): Where {
        $storeProducts = StockBalance::findAll(["where" => ['store_id' => $params['store_id']], 'columns' => ['product_id'], "group" => "product_id"])->toArray();
        $products = ArrayHelper::extractId($storeProducts);
        $where = new Where();
        $where->in('product_id', $products);
        if (!empty($params['category_id'])) {
            $where->equalTo('category_id', $params['category_id']);
        }
        return $where;
    }
    
    /**
     * Return where clause for query
     *
     * @return Where
     */
    private function getWhereTop ($limit = Resource::SQL_LIMIT_PRODUCTCARD_IN_SLIDER)
    {
        $count_columns = new \Laminas\Db\Sql\Expression("count(`product_id`) as `count`, `product_id` as product_id");
        $products = ProductHistory::findAll(['columns' => [$count_columns], 'group' => ['product_id'], 'having' => ['count > 1'], 'group' => ['product_id'],'limit' => $limit,])->toArray();
        $productsId = ArrayHelper::extractId($products);
        //exit (print_r($productsId));
        $where = new Where();
        $where->in("id" , $productsId);                
        return $where; 
         //SELECT `product_id`, COUNT(`product_id`) AS `count` FROM 'product_history` GROUP BY `product_id` HAVING  `count` > order BY `count` DESC LIMIT 0, 40
     }
     /**
     * Return where clause for query
     *
     * @return Where
     */
    private function getWhereSale ()
    {
        $where = new Where();
        $where->greaterThan("discount" , 0);                
        return $where; 
         //SELECT `product_id`, COUNT(`product_id`) AS `count` FROM 'product_history` GROUP BY `product_id` HAVING  `count` > order BY `count` DESC LIMIT 0, 40
     }
    
     /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsCatalog($params) {
        $this->prepareCharacteristics($params['characteristics']);
        if (empty($params['priceRange'])) {
            $params['priceRange'] = '0;' . PHP_INT_MAX;
        }
        unset($params['offset'], $params['limit']);
        $params['where'] = $this->getWhereCatalog($params);
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsCategories($params) {
        $params['where'] = $this->getWhereCategories($params);
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsBrand($params) {
        $params['where'] = $this->getWhereBrand($params);
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsStore($params) {
        $params['where'] = $this->getWhereStore($params);
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsProvider($params) {
        $params['where'] = $this->getWhereProvider($params);
        return $this->getProducts($params);
    }
    
    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsTop() {
        $params['where'] = $this->getWhereTop();
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProductsSale() {
        $params['where'] = $this->getWhereSale();
        $params['order'] = ['discount desc'];
        $params['limit'] = Resource::SQL_LIMIT_PRODUCTCARD_IN_SLIDER;
        return $this->getProducts($params);
    }

    /**
     * Return filtered HandbookRelatedProduct
     *
     * @param array $params
     * @return HandbookRelatedProduct[]
     *
     */
    private function getProducts($params) {
        $products = $this->handBookRelatedProductRepository->findAll($params);
        $filteredProducts = $this->commonHelperFuncions->getProductCardArray($products, $this->identity());
        return $filteredProducts;
    }
    
    /**
     * return productId array from product_history
     * 
     * @param int  Limit for SQL query
     * @return array
     */

    

    private function prepareCharacteristics(&$characteristics) {
        if (!$characteristics) {
            return;
        }
        foreach ($characteristics as $key => &$value) {
            if ($value) {
                foreach ($value as &$v) {
                    if (empty($v)) {
                        $v = '0;' . PHP_INT_MAX;
                    }
                }
            }
        }
    }

}
