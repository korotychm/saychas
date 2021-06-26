<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Application\Service\HtmlProviderService;
use Application\Model\Entity\UserData;
use Application\Model\Repository\UserRepository;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Authentication\AuthenticationService;
use Application\Resource\StringResource;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Http\Response;
use Laminas\Session\Container;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Where;

class AjaxController extends AbstractActionController
{

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

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository,
            CharacteristicRepositoryInterface $characteristicRepository, PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct, $entityManager, $config,
            HtmlProviderService $htmlProvider, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository, ProductImageRepositoryInterface $productImageRepository)
    {
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
    }

    public function addToBasketAction()
    {   
        $return = ["error" => true, "count" => 0];
        $post = $this->getRequest()->getPost();
        $return['productId']=$productId = $post->product;
        $addNum = 1;
      //exit("<pre>".print_r($post, true));
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if($userId ){
            $return['error']=false;    

           if($productId) { 
                $basketItem = $this->basketRepository->findFirstOrDefault(['user_id'=>$userId,'product_id'=>$productId, 'order_id'=>"0" ]);
                $basketItemTotal = (int)$basketItem->getTotal();
                $basketItem->setUserId($userId);
                $basketItem->setProductId($productId);
               // $basketItem->setTotal($addNum + $basketItemTotal);
                 $basketItem->setTotal(1);
                $this->basketRepository->persist($basketItem, ['user_id'=>$userId, 'product_id'=>$productId, 'order_id'=>0]);    
           }

            $where = new Where();
            $where->equalTo('user_id', $userId);
            $where->equalTo('order_id', 0);
            /** more conditions come here */
            $columns = ['product_id', 'order_id', 'total'];
            $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);
            foreach ($basket as $b) {
                if($pId=$b->productId){
                    $product = $this->productRepository->find(['id'=>$pId]);
                    $return['products'][]=[
                        "id" => $pId, 
                        "name" => $product->getTitle(), 
                        "count" => $b->total, 
                        'image'=> $this->productImageRepository->findFirstOrDefault(["product_id"=>$pId])->getHttpUrl(),
                       ]; 
                    $return['total']+=$b->total;
                    $return['count'] ++;
                }
            }
        }
        return new JsonModel($return);    
    //exit("<pre>".print_r($return, true)."</pre>");
        
    }

    public function userAuthAction()
    {
        //userNameInput userSmsCode userPass
        $password = $smsCode = "7777"; //костыль

        $return = ["error" => true, "message" => StringResource::ERROR_MESSAGE, "isUser" => false, "username" => ""];
        $post = $this->getRequest()->getPost();
        $return['phone'] = $this->phoneToNum($post->userPhone);
        $return['name'] = $post->userNameInput;
        $code = $post->userSmsCode;
        $container = new Container(StringResource::SESSION_NAMESPACE);

        if (!$return['phone']) {

            $return["message"] .= StringResource::ERROR_INPUT_PHONE_MESSAGE;
        } else {
            $user = $this->userRepository->findFirstOrDefault(["phone" => $return['phone']]);
            if ($user and $userId = $user->getId()) {
                $return['userId'] = $userId;
                $return["isUser"] = true;
                if ($post->userPass == $password) {

                    $container->userIdentity = $return['userId'];
                    $return["error"] = false;
                }
                $return["message"] = StringResource::ERROR_INPUT_PASSWORD_MESSAGE
                        . "($password)"
                ;
                $return["username"] = $user->getName();
            } else {
                if ($return['name'] and $code == $smsCode) {

                    $user = $this->userRepository->findFirstOrDefault(["id" => $container->userIdentity]);
                    $user->setName($return['name']);
                    $user->setPhone($return['phone']);
                    $this->userRepository->persist($user, ['id' => $user->getId()]);
                    $return["error"] = false;
                }
                $return["message"] = StringResource::ERROR_INPUT_NAME_SMS_MESSAGE;  //это телефонный номер  юзера
            }
        }

        $return['post'] = $post;

         return new JsonModel($return);
    }

    public function previewAction()
    {
        $this->layout()->setTemplate('layout/preview');
        $categories = $this->categoryRepository->findAllCategories();
        return new ViewModel([
            'menu' => $categories
        ]);
    }

    public function ajaxToWebAction()
    {
        $post = $this->getRequest()->getPost();
        $url = $this->config['parameters']['1c_request_links']['get_product'];
        $params = array('name' => 'value');
        $result = file_get_contents(
                $url,
                false,
                stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params)]
                ])
        );

        $return .= "<pre>";
        $return .= date("r") . "\n";
        // if($result) print_r(json_decode($result));
        $return .= "{$post->name} => {$post->value}";
        $return .= "</pre>";
        exit($return);

        return new JsonModel([]);
    }

    public function ajaxGetStoreAction()
    {

        $post = $this->getRequest()->getPost();
        $json = $post->value;
        try {
            $TMP = Json::decode($json);
        } catch (LaminasJsonRuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        $url = $this->config['parameters']['1c_request_links']['get_store'];
        $result = file_get_contents(
                $url,
                false,
                stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $post->value
            )
                ))
        );

        $r = print_r(json_decode($result, true), true);

        $response = new Response();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $response->getHeaders()->addHeaders([
            'HeaderField1' => 'header-field-value',
            'HeaderField2' => 'header-field-value2',
        ]);
        //$r = $this->htmlProvider->testHtml();
        $response->setContent(<<<EOS
        <html>
        <body>
                <pre>
            $r
                </pre>
        </body>
        </html>
        EOS);
        return $response;
    }

    public function ajaxGetLegalStoreAction()
    {
        $post = $this->getRequest()->getPost();
        $json = $post->value;
        try {
            $TMP = Json::decode($json);
        } catch (LaminasJsonRuntimeException $e) {
            exit($e->getMessage());
// return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }
        $ob = $TMP->data;
        if (!$ob->house)
            return (StringResource::USER_ADDREES_ERROR_MESSAGE);

        $container = new Container(StringResource::SESSION_NAMESPACE);
        $container->userAddress = $TMP->value;

        $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        $userData = new UserData();
        $userData->setAddress($container->userAddress);
        $userData->setGeodata($json);
//            $userData->setTimestamp(new \DateTime("now"));
        try {
            $user->setUserData([$userData]);
        } catch (InvalidQueryException $e) {
            print_r($e->getMessage());
        }

        $url = $this->config['parameters']['1c_request_links']['get_store'];
        $result = file_get_contents(
                $url,
                false,
                stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $json
            )
                ))
        );
        $legalStore = Json::decode($result, true);
        foreach ($legalStore as $store) {
            $sessionLegalStore[$store['store_id']] = $store['deliverySpeedInHours'];
        }

        $container->legalStore = $sessionLegalStore; //Json::decode($result, true);
        //exit(print_r($container->legalStore));
        exit("200");
    }

    public function ajaxSetUserAddressAction()
    {
        $json["userAddress"] = $this->htmlProvider->writeUserAddress();
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $json["legalStore"] = $container->legalStore;
        exit(Json::encode($json, JSON_UNESCAPED_UNICODE));
    }

    public function unsetFilterForCategoКyAction()
    {
        $post = $this->getRequest()->getPost();
        $category_id = $post->category_id;
        $container = new Container(StringResource::SESSION_NAMESPACE);
        unset($container->filtrForCategory[$category_id]);
    }

    /**
     * Return true if given product matches
     * the specified characteristics
     * 
     * @param HandbookRelatedProduct $product
     * @param array $characteristics
     * @return bool
     */
    private function matchProduct(/* HandbookRelatedProduct */ \Application\Model\Entity\Product $product, array $characteristics): bool
    {
        $flags = [];
        foreach ($characteristics as $key => $value) {
            $found = $this->productCharacteristicRepository->find(['characteristic_id' => $key, 'product_id' => $product->getId()]);
            if (null == $found) {
                $flags[$key] = false;
                continue;
            }
            $type = $found->getType();
            switch ($type) {
                case CharacteristicRepository::INTEGER_TYPE:
                    reset($value);
                    list($left, $right) = explode(';', current($value));
                    //list($left, $right) = explode(';', $value[0]);
                    $flags[$key] = !($found->getValue() < $left || $found->getValue() > $right);
                    break;
                case CharacteristicRepository::BOOL_TYPE:
                    $flags[$key] = ($found->getValue() == $value);
                    break;
                default:
                    $flags[$key] = in_array($found->getValue(), $value);
                    break;
            }
        }
        foreach ($flags as $f) {
            if (!$f) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return where clause to filter products by price and category
     * 
     * @param array $params
     * @return Where
     */
    private function getWhere($params): Where
    {
        $where = new Where();
        list($low, $high) = explode(';', $params['priceRange']);
        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
        $where->equalTo('category_id', $params['category_id']);

        return $where;
    }

    /**
     * Return filtered HandbookRelatedProduct filtered products
     * 
     * @param array $params
     * @return HandbookRelatedProduct[]
     */
    private function getProducts($params)
    {
        unset($params['offset']);
        unset($params['limit']);
        $params['where'] = $this->getWhere($params);
//        $products = $this->handBookRelatedProductRepository->findAll($params);
//        $orders=["","pr.title ASC", 'price ASC','price DESC',"pr.title DESC"];
//        $params['order']=$orders[$filtrForCategory[$category_id]['sortOrder']];
//        $params['filter'] = $filtred;
        $products = $this->productRepository->filterProductsByStores($params);

        $filteredProducts = [];
        foreach ($products as $product) {
            $matchResult = $this->matchProduct($product, $params['characteristics']);
            if ($matchResult) {
                $filteredProducts[] = $product;
            }
        }
        return $filteredProducts;
    }

    public function setFilterForCategoryAction()
    {

        $post = $this->getRequest()->getPost()->toArray();

        $products = $this->getProducts($post);

        return (new ViewModel(['products' => $products]))->setTerminal(true);

//        exit ("<pre>".print_r($post, true)."</pre>");


        /* foreach ($post as $key=>$value)
          //echo "$key=>$value <br>"; exit();
          $fltrArray[$key]=$value;

          $container = new Container(StringResource::SESSION_NAMESPACE);
          $filtrForCategory=$container->filtrForCategory;
          $filtrForCategory[$category_id]=$fltrArray;
          $container->filtrForCategory = $filtrForCategory;
          exit();
          //exit ("<pre>".print_r($container->filtrForCategory, true)."</pre>");
          //exit (print_r($container->filtrForCategory));/* */
    }

    public function ajaxAction($params = array('name' => 'value'))
    {
        $id = $this->params()->fromRoute('id', '');
        $post = $this->getRequest()->getPost();
        //$param=array(1,2,3,4,5);   

        if ($id == "toweb") {
            $url = "http://SRV02:8000/SC/hs/site/get_product";
            $params = array('name' => 'value');
            $result = file_get_contents(
                    $url,
                    false,
                    stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($params))
                    ))
            );
            $return .= "<pre>";
            $return .= date("r") . "\n";
            // if($result) print_r(json_decode($result));
            $return .= "{$post->name} => {$post->value}";
            $return .= "</pre>";
            exit($return);
        }
        if ($id == "getstore") {

            //.print_r($post,true));  
            $url = "http://SRV02:8000/SC/hs/site/get_product";

            $result = file_get_contents(
                    $url,
                    false,
                    stream_context_create(array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type: application/json',
                    'content' => $post->value
                )
                    ))
            );
            $return .= "<pre>";
            $return .= date("r") . "\n";
            if ($result)
                $return .= print_r(json_decode($result, true), true);
            $return .= "</pre>";
            exit($return);
        }
        if ($id == "getproviders") {

            $providers = $this->providerRepository->findAvailableProviders(['order' => 'id ASC', 'limit' => 100, 'offset' => 0/* , 'sequence' => [1,2,3,4,5] */]);
            //$providers = $this->providerRepository->findAll(['limit'=>100, 'offset' => 0, 'order' =>'id ASC']);
            //if (!$providers )    exit(date("r")."<h3>Объект provider не&nbsp;получен</h3> <a href=# rel='666' class=provider-list  >Запросить тестовые магазины </a> <hr/>"); 
            $return .= date("r");
            $return .= "<ul>";
            foreach ($providers as $row) {
                $return .= "<li><a href=# rel='{$row->getId()}' class=provider-list >{$row->getTitle()}</a></li>";
            }
            $return .= "</ul>";
            exit($return);
        }
        if ($id == "getshops") {
            $stores = $this->storeRepository->findStoresByProviderIdAndExtraCondition($post->provider, $param);
            $return .= date("r") . "<br>";
            $return .= "id постащика: {$post->provider}<hr>";
            if (!$stores)
                exit($return . "<h3>Объект store не&nbsp;получен</h3> <a href=# rel='2' class=shop-list  >Запросить тестовые товары </a>");
            $return .= "<ul>";
            foreach ($stores as $row)
                $return .= "<li><a href=#product rel='{$row->getId()}' class=shop-list title='{$row->getAddress()} \r\n  {$row->getGeox()} , {$row->getGeoy()} ' >"
                        . "{$row->getTitle()}</a>"
                        . "</li>";
            $return .= "</ul>";
            exit($return);
        }
        if ($id == "getproducts") {
            $products = $this->productRepository->findProductsByProviderIdAndExtraCondition($post->shop, $param);
            $str = StringResource::PRODUCT_FAILURE_MESSAGE;

            $return .= date("r") . "<br>";
            $return .= "id магазина: {$post->shop}<hr>";
            $return .= $this->htmlProvider->productCard($products)->card;
            exit($return);
        }

        header('HTTP/1.0 404 Not Found');
        exit();
    }

    public function banzaiiAction()
    {
        //$this->response->setStatusCode(404);

        return (new ViewModel(['banzaii' => 'zzappolzaii']))->setTerminal(true);
    }

    public function providerAction()
    {
        $id = $this->params()->fromRoute('id', '');
        $this->layout()->setTemplate('layout/mainpage');
        $categories = $this->categoryRepository->findAllCategories("", 0, $id);
        $providers = $this->providerRepository->findAll(['table' => 'provider', 'limit' => 100, 'order' => 'id ASC', 'offset' => 0]);
        return new ViewModel([
            "providers" => $providers,
            "catalog" => $categories,
        ]);
    }

    private function phoneToNum($destination_numbers)
    {
        $numbers = $sort_numbers = [];
        if (!is_array($destination_numbers)) {
            $destination_numbers = trim($destination_numbers);
            $dest_length = strlen($destination_numbers);
            $destination_numbers = str_replace(array(",", "\n"), ";", $destination_numbers);
            $sort_numbers = explode(';', $destination_numbers);
        } else {
            $sort_numbers = $destination_numbers;
        }

        foreach ($sort_numbers as $arInd) {
            $arInd = trim($arInd);
            $symbol = false;
            $spec_sym = array("+", "(", ")", " ", "-", "_");
            for ($i = 0; $i < strlen($arInd); $i++) {
                if (!is_numeric($arInd[$i]) && !in_array($arInd[$i], $spec_sym)) {
                    $symbol = true;
                }
            }
            if ($symbol) {
                $numbers[] = $arInd;
            } else {
                $arInd = str_replace($spec_sym, "", $arInd);

                if (strlen($arInd) < 10 || strlen($arInd) > 15) {
                    continue;
                } else {
                    if (strlen($arInd) == 10 && $arInd[0] == '9') {
                        $arInd = '7' . $arInd;
                    }
                    if (strlen($arInd) == 11 && $arInd[0] == '8') {
                        $arInd[0] = "7";
                    }
                    $numbers[] = $arInd;
                }
            }
        }
        return $numbers[0];
    }

}
