<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
//use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
//use Application\Model\Entity\Basket;
//use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Setting;
//use Application\Model\Entity\Delivery;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Application\Service\HtmlProviderService;
use Application\Service\CommonHelperFunctionsService;
//use Application\Model\Entity\User;
//use Application\Model\Entity\UserData;
//use Application\Model\Entity\UserPaycard;
//use Application\Model\Entity\ProductFavorites;
//use Application\Model\Entity\ProductHistory;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\Entity\StockBalance;
//use Application\Model\Entity\Provider;
use Application\Model\Repository\UserRepository;
//use Application\Adapter\Auth\UserAuthAdapter;
//use RuntimeException;
use Laminas\Authentication\AuthenticationService;
//use Application\Resource\Resource;
use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Http\Response;
//use Laminas\Session\Container; // as SessionContainer;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Where;
//use Throwable;
use Application\Helper\ArrayHelper;

//use Application\Helper\StringHelper;

class ProductCardsController extends AbstractActionController {

    private $testRepository;
    private $categoryRepository;
    private $providerRepository;
    private $storeRepository;
    private $productRepository;
    private $filteredProductRepository;
    private $brandRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $productCharacteristicRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $userRepository;
    private $authService;
    private $basketRepository;
    private $productImageRepository;
    private $commonHelperFuncions;

    //private $sessionContainer;

    public function __construct(
            TestRepositoryInterface $testRepository,
            CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository,
            StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository,
            FilteredProductRepositoryInterface $filteredProductRepository,
            BrandRepositoryInterface $brandRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository,
            StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager,
            $config,
            HtmlProviderService $htmlProvider,
            UserRepository $userRepository,
            AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository,
            BasketRepositoryInterface $basketRepository,
            ProductImageRepositoryInterface $productImageRepository,
            //SessionContainer $sessionContainer ,
            CommonHelperFunctionsService $commonHelperFuncions) {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        $this->brandRepository = $brandRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->basketRepository = $basketRepository;
        $this->productImageRepository = $productImageRepository;
        $this->commonHelperFuncions = $commonHelperFuncions;
//        $this->sessionContainer = $sessionContainer;
//        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(ProductCharacteristic::class);
        $this->entityManager->initRepository(StockBalance::class);
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
        $products = $this->getProductsCategories($param);
        return new JsonModel($products);
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
        $products = $this->getProductsBrand(['brand_id' => $post->brandId, 'category_id' => $post->categoryId]);
        return new JsonModel($products);
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
        $products = $this->getProductsStore(['store_id' => $post->storeId, 'category_id' => $post->categoryId]);
        return new JsonModel($products);
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
        $products = $this->getProductsProvider(['provider_id' => $post->providerId, 'category_id' => $post->categoryId]);
        return new JsonModel($products);
    }

    /**
     * return JSON product cards
     *
     * @return Json model
     */
    public function getProductsCatalogAction() {
        $post = $this->getRequest()->getPost()->toArray();
        $products = $this->getProductsCatalog($post);
        return new JsonModel(["products" => $products]);
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
        $groupChars = [0];
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
                $groupChars[] = $key;
            }
            $legalProducts = array_intersect($legalProducts, $this->getFiltredProductsId($filterWhere));
        }
        $subWhere = new Where();
        $productsFiltred = $this->getFiltredProductsId($subWhere->in('characteristic_id', $groupChars));
        $productsFiltredDefault =  empty($productsFiltred) ? [0] : $productsFiltred; 
        $nest = $where->nest();
        $nest->in('product_id', $legalProducts)->or->notIn('product_id', $productsFiltredDefault)->unnest();
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
        return ArrayHelper::extractProdictsId($products);
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
        $products = ArrayHelper::extractProdictsId($storeProducts);
        $where = new Where();
        $where->in('product_id', $products);
        if (!empty($params['category_id'])) {
            $where->equalTo('category_id', $params['category_id']);
        }
        return $where;
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
    private function getProducts($params) {
        $products = $this->handBookRelatedProductRepository->findAll($params);
        $filteredProducts = $this->commonHelperFuncions->getProductCardArray($products, $this->identity());
        return $filteredProducts;
    }

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
