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
use Application\Model\Entity\Basket;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
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
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Model\Repository\UserRepository;
//use Application\Adapter\Auth\UserAuthAdapter;
use RuntimeException;
use Laminas\Authentication\AuthenticationService;
use Application\Resource\StringResource;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Http\Response;
use Laminas\Session\Container; // as SessionContainer;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Laminas\Db\Sql\Where;
//use Throwable;
//use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;

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
    private $commonHelperFuncions;

    //private $sessionContainer;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository, BrandRepositoryInterface $brandRepository,
            CharacteristicRepositoryInterface $characteristicRepository, PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct, $entityManager, $config,
            HtmlProviderService $htmlProvider, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository, ProductImageRepositoryInterface $productImageRepository/* ,
              SessionContainer $sessionContainer */, CommonHelperFunctionsService $commonHelperFuncions)
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
        $this->commonHelperFuncions = $commonHelperFuncions;
//        $this->sessionContainer = $sessionContainer;
        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Delivery::class);
    }

    public function delFromBasketAction()
    {
        $return = ["error" => true, "count" => 0];
        $post = $this->getRequest()->getPost();
        $return['productId'] = $productId = $post->productId;

        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $basketItem = Basket::remove(['where' => ['user_id' => $userId, 'product_id' => $productId]]);
        return new JsonModel($return);
    }

    public function getJsonCategoryFiltersAction()
    {

        $post = $this->getRequest()->getPost();
        $return['category_id'] = $category_id = $post->categoryId;
        if (!$category_id or empty($matherCategories = $this->categoryRepository->findAllMatherCategories($category_id))) {
            return new JsonModel($return);
        }
        //$return['print_r'] = $matherCategories;
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $return["rangeprice"] = $this->handBookRelatedProductRepository->findMinMaxPriceValueByCategory($categoryTree);
        $filters = $this->productCharacteristicRepository->getCategoryFilter($matherCategories);
        $return["filters"] = $this->htmlProvider->getCategoryFilterJson($filters, $category_id);
        return new JsonModel($return);
    }

    public function getUserOrderListAction()
    {
        $return["result"] = true;
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $orders = ClientOrder::findAll(['user_id' => $userId]);
        if (!empty($orders)) {
            $return["order_list"] = $this->htmlProvider->orderList($orders);
        }
        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->notEqualTo('order_id', 0);
        $columns = ['product_id'];
        $userBasketHistory = Basket::findAll(['where' => $where, 'columns' => $columns]);
        if (empty($userBasketHistory)) {
            return new JsonModel(["result" => false]);
        }
        foreach ($userBasketHistory as $basketItem) {
            $product_id = $basketItem->getProductId();
            try {
                $product = $this->handBookRelatedProductRepository->find(['id' => $product_id]);
                // $product = null;
                $return["productsMap"][$product_id]["image"] = $product->receiveProductImages()->current()->getHttpUrl();
                $return["productsMap"][$product_id]["title"] = $product->getTitle();
            } catch (\Throwable $ex) {
                return new JsonModel(["result" => false, 'error' => $ex->getMessage()]);
            }
        }

        return new JsonModel($return);
    }

    public function checkOrderStatusAction()
    {
        $post = $this->getRequest()->getPost();
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
    }

    public function ajaxUserDeleteAddressAction()
    {
        //$return["error"] = true;
        /* $this->getResponse()->setStatusCode(403);
          return; */
        $post = $this->getRequest()->getPost();
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);
        $userPhone = (empty($user)) ? false : $user->getPhone();
        $userPhone = StringHelper::phoneFromNum($user->getPhone());
        if ($userPhone) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $return['reload'] = $post->reload;
        $return['dataId'] = $post->dataId;
        $remove = UserData::remove(['where' => ['user_id' => $userId, 'id' => $return['dataId']]]);
        $return['removeresult'] = $remove->count();

        return new JsonModel($return);
    }

//
    public function ajaxUserSetDefaultAddressAction()
    {
        //$return["error"] = true;
        $post = $this->getRequest()->getPost();
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $user = User::find(['id' => $userId]);
        //$userPhone = (empty($user))?false:$user->getPhone();
        if (!$return['userId']) {
            $this->getResponse()->setStatusCode(403);
            return;
        }
        $userPhone = StringHelper::phoneFromNum($user->getPhone());
        $return['reload'] = true; // $post->reload;
        $return['dataId'] = $post->dataId;
        //return new JsonModel($return);
        // $return['dataId'] = 16;
        // $userId = 50;

        $userData = UserData::findAll(['where' => ['user_id' => $userId, 'id' => $return['dataId']]])->current();
        if (null == $userData) {
            $return['error'] = "adress not found";
            return new JsonModel($return);
        }
        //$userData = UserData::findAll(['where' => ['user_id' => 50, 'id' => 15 ] ])->current();

        $userGeoData = $userData->getGeodata();

        //$userData->setId($return['dataId']);
        $userData->setTime(time());
        $userData->persist(['user_id' => $userId, 'id' => $return['dataId']]);

        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userGeoData);
        //$return['removeresult']  = $remove->count();
        return new JsonModel($return);
    }

    public function ajaxBasketChangedAction()
    {
        //exit("banzaiii!!!");
        //$userId = $this->id
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $userId = $container->userIdentity;
        $whatHappened = $container->whatHappened;
        if (!empty($whatHappened['products'])) {
            $return = ["result" => true, "products" => $whatHappened['products'], "stores" => $whatHappened['stores']];
            $return ['updated'] = $this->htmlProvider->basketWhatHappenedUpdate($userId, $whatHappened['products']);
        } else {
            $return = ["result" => false];
        }
        unset($container->whatHappened);
        return new JsonModel($return);
    }

    public function basketCheckBeforeSendAction()
    {

        $userId = $this->identity();
        $user = User::find(['id' => $userId]);
        $userPhone = (empty($user)) ? false : $user->getPhone();
        if (empty($userPhone)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/",]);
        }
        $userData = UserData::findAll(['where' => ['user_id' => $userId], 'order' => 'timestamp DESC'])->current();
        if (empty($userData)) {
            return new JsonModel(["result" => false, "reload" => true, "reloadUrl" => "/"]);
        }
        $userGeoData = $userData->getGeodata();
        //return new JsonModel([$userId, $userGeoData]);

        $return['updatelegalstore'] = $this->commonHelperFuncions->updateLegalStores($userGeoData);
        $post = $this->getRequest()->getPost();
        $param['basketUserId'] = $post->userIdentity;
        $param['userId'] = $userId;
        $param['postedProducts'] = $post->products;

        $where = new Where();
        $where->equalTo('user_id', $userId);
        $where->equalTo('order_id', 0);
        $columns = ['product_id', 'total', 'price'];
        $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);

        if (!empty($basket)) {
            $return = $this->htmlProvider->basketCheckBeforeSendService($param, $basket);
            return new JsonModel($return);
        }
        return new JsonModel(['result' => false, "reload" => true, "reloadUrl" => "/"]);
    }

    public function addToBasketAction()
    {
        $return = ["error" => true, "count" => 0];
        $return['total'] = $return['count'] = 0;
        $post = $this->getRequest()->getPost();
        $return['productId'] = $productId = $post->product;
        $addNum = 1;
        //exit("<pre>".print_r($post, true));
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        if ($userId) {
            $return['error'] = false;
            if ($productId) {

                // $basketItem = $this->basketRepository->findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
                $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
                $basketItemTotal = (int) $basketItem->getTotal();

                $basketItem->setUserId($userId);
                $basketItem->setProductId($productId);
                $productadd = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();
                $productaddPrice = (int) $productadd->getPrice();
                $basketItem->setPrice($productaddPrice);
                //$basketItemTotal = 0;
                $basketItem->setTotal($basketItemTotal + 1);
                $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
                //$this->basketRepository->persist($basketItem, ['user_id' => $userId, 'product_id' => $productId, 'order_id' => 0]);
            }

            $where = new Where();
            $where->equalTo('user_id', $userId);
            $where->equalTo('order_id', 0);

            /** more conditions come here */
            $columns = ['product_id', 'order_id', 'total'];
//            $basket = $this->basketRepository->findAll(['where' => $where, 'columns' => $columns]);
            $basket = Basket::findAll(['where' => $where, 'columns' => $columns]);
            foreach ($basket as $b) {
                if ($pId = $b->productId) {
                    $product = $this->productRepository->find(['id' => $pId]);
                    $return['products'][] = [
                        "id" => $pId,
                        "name" => $product->getTitle(),
                        "count" => $b->total,
                        'image' => $this->productImageRepository->findFirstOrDefault(["product_id" => $pId])->getHttpUrl(),
                    ];
                    $return['total'] += $b->total;
                    $return['count']++;
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
        $return['phone'] = StringHelper::phoneToNum($post->userPhone); // $this->phoneToNum($post->userPhone);
        $return['name'] = $post->userNameInput;
        $code = $post->userSmsCode;
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
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

    public function calculateBasketItemAction()
    {
        $post = $this->getRequest()->getPost();
        if (!$userId = $this->identity())
            return new JsonModel(["error" => true, "errorMessage" => "user not found"]);
        if (!$return['productId'] = $productId = $post->product)
            return new JsonModel(["error" => true, "errorMessage" => "product not found"]);

        $product = $this->handBookRelatedProductRepository->findAll(['where' => ['id' => $productId]])->current();

        if (null == $product
                or!$productPrice = (int) $product->getPrice()
                or!$productCount = (int) $post->count
                or $productCount < 1
        ) {
            return new JsonModel(["error" => true, "errorMessage" => "product price error"]);
        }

        $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
        $basketItem->setTotal($productCount);
        $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);

        $return['totalNum'] = (int) $productPrice * $productCount;
        $return['totalFomated'] = number_format($return['totalNum'] / 100, 0, ',', '&nbsp;');

        return new JsonModel($return);
    }

    public function basketOrderMergeAction()
    {
        $param = [
            "hourPrice" => 29900, //цена доставки за час
            "mergePrice" => 5000, //цена доставки за три часа
            "mergePriceFirst" => 24900, //цена доставки за первый махгазин  при объеденении заказа
            "mergecount" => 4, //количество объеденямых магазинов
        ];
        $userId = $this->identity();
        if (!$userId) {
            header('HTTP/1.0 401 Unauthorized');
            exit();
        }
        $post = $this->getRequest()->getPost();
        $timepoint = $post->timepoint;
        $selectedtimepoint[0][$timepoint[0]] = " selected ";
        $selectedtimepoint[1][$timepoint[1]] = " selected ";
        $return = $this->htmlProvider->basketMergeData($post, $param);
        $view = new ViewModel([
            'ordermerge' => $post->ordermerge,
            'timeClose' => $return['timeClose'],
            'countStors' => $return["count"],
            'hourPrice' => $return["hourPrice"],
            'hour3Price' => $return["hour3Price"],
            'select1hour' => $return["select1hour"],
            'select3hour' => $return["select3hour"],
            'selectedtimepoint' => $selectedtimepoint,
            'timepointtext1' => $post->timepointtext1,
            'timepointtext3' => $post->timepointtext3,
            'printr' => "<pre>" . print_r($post, true) . "</pre>",
        ]);
        $view->setTemplate('application/common/basket-order-merge');
        return $view->setTerminal(true);
    }

    public function basketPayCardInfoAction()
    {
        $cardinfo = "4276 5555 **** <span class='red'>1234&darr;</span>";
        $post = $this->getRequest()->getPost();
        $paycard = $post->paycard;
        $view = new ViewModel([
            'paycard' => $paycard,
            'cardinfo' => $cardinfo,
        ]);
        $view->setTemplate('application/common/basket-pay-card');
        return $view->setTerminal(true);
    }

    public function basketPayInfoAction()
    {
        //sleep(2);
        $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        $basketUser['phone'] = $user->getPhone();
        /**/    //$basketUser['phoneformated'] = "+".sprintf("%s (%s) %s-%s-%s",substr($basketUser['phone'], 0, 1),substr($basketUser['phone'], 1, 3),substr($basketUser['phone'], 4, 3),substr($basketUser['phone'], 7, 2),substr($basketUser['phone'], 9));
        $basketUser['name'] = $user->getName();
        $userData = $user->getUserData();
        if ($userData->count() > 0)
            $basketUser['address'] = $userData->current()->getAddress();

        /**/

        $param = [
            "hourPrice" => 29900, //цена доставки за час
            "mergePrice" => 5000, //цена доставки за три часа
            "mergePriceFirst" => 24900, //цена доставки за первый магазин  при объеденении заказа
            "mergecount" => 4, //количество объеденямых магазинов
        ];
        $post = $this->getRequest()->getPost();
        $row = $this->htmlProvider->basketPayInfoData($post, $param);

        $timeDelevery = (!$post->ordermerge) ? $post->timepointtext1 : $post->timepointtext3;
        $row['payEnable'] = ($row['total'] > 0 and ($row['countSelfdelevery'] or ($row['countDelevery'] /* and $timeDelevery */))) ? true : false;

        //basketPayInfoData($post);
        //$row["payEnable"]=true;
        //exit (print_r($row));
        $view = new ViewModel([
            //$row
            "payEnable" => $row["payEnable"],
            "textDelevery" => $row["textDelevery"],
            'priceDelevery' => $param['hourPrice'],
            'ordermerge' => $post->ordermerge,
            'priceDeleveryMerge' => $param['mergePrice'],
            'priceDeleveryMergeFirst' => $param['mergePriceFirst'],
            'countDelevery' => $row["countDelevery"],
            'countDeleveryText' => $row["countDeleveryText"],
            'priceDelevery' => $row['priceDelevery'],
            'addressDelevery' => StringHelper::cutAddressCity($basketUser['address']),
            'priceSelfdelevery' => 0,
            'basketpricetotalall' => $row['basketpricetotalall'],
            'post' => $row['post'],
            'productcount' => $row['count'],
            'producttotal' => $row['total'],
            'countSelfdelevery' => $row['countSelfdelevery'],
            'storeAdress' => $row["storeAdress"],
            "cardinfo" => "4276 5555 **** <span class='red'>1234&darr;</span>",
            'paycard' => $post->paycard,
            'timeDelevery' => $timeDelevery,
                /**/
        ]);
        $view->setTemplate('application/common/basket-payinfo');
        return $view->setTerminal(true);
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
                stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => $post->value
            ]
                ])
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
        // exit ( print_r($post));
        if (!$json = $post->value)
            return new JsonModel(NULL);

        try {
            $TMP = Json::decode($json);
        } catch (LaminasJsonRuntimeException $e) {
            return new JsonModel(["result" => false, "error" => $e->getMessage()]);
        }
        $ob = $TMP->data;
        if (!$ob->house) {

            return new JsonModel(["result" => false, "error" => StringResource::USER_ADDREES_ERROR_MESSAGE]);
        }
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        /* $container = new Container(StringResource::SESSION_NAMESPACE);
          $url = $this->config['parameters']['1c_request_links']['get_store'];
          $result = file_get_contents(
          $url,
          false,
          stream_context_create(['http' => ['method' => 'POST','header' => 'Content-type: application/json','content' => $json]])
          );

          if (!$result) return new JsonModel(["result"=>false, "error"=>"1C не отвечает "]);
          $legalStore = Json::decode($result, true);
          foreach ($legalStore as $store) {
          $sessionLegalStore[$store['store_id']] = $store['delivery_speed_in_hours'];
          if($store['time_until_closing']) $store['time_until_closing']+=time();
          $sessionLegalStoreArray[$store['store_id']] = $store ;
          }
          $container->legalStore = $sessionLegalStore; //Json::decode($result, true);
          $container->legalStoreArray = $sessionLegalStoreArray;
          //exit (print_r($sessionLegalStoreArray));//Json::decode($result, true); */
        // $return = $this->getLegalStore($json);

        $return = $this->commonHelperFuncions->updateLegalStores($json);
        return new JsonModel($return);
    }

    /* private function getLegalStore ($json)
      {


      $url = $this->config['parameters']['1c_request_links']['get_store'];
      $result = file_get_contents(
      $url,
      false,
      stream_context_create(['http' => ['method' => 'POST','header' => 'Content-type: application/json','content' => $json]])
      );
      if (!$result) {
      return ["result"=>false, "error"=>"1C не отвечает "];
      }

      $legalStore = Json::decode($result, true);

      foreach ($legalStore as $store) {
      $sessionLegalStore[$store['store_id']] = $store['delivery_speed_in_hours'];
      if($store['time_until_closing']) $store['time_until_closing']+=time();
      $sessionLegalStoreArray[$store['store_id']] = $store ;
      }
      $container = new Container(StringResource::SESSION_NAMESPACE);
      $container->legalStore = $sessionLegalStore; //Json::decode($result, true);
      $container->legalStoreArray = $sessionLegalStoreArray;

      return ["result"=>true, "message"=>"Магазины получены"];
      } */

    public function ajaxAddUserAddressAction()
    {
        $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        $post = $this->getRequest()->getPost();
        $return["user"] = $userId;
        if ($userId and $post->address and $post->dadata) {
            $return["error"] = "Успешно ";
            $return["ok"] = "присвоен адрес: {$post->address}";
            $userData = new UserData();
            $userData->setUserId($userId);
            $userData->setAddress($post->address);
            $userData->setGeodata($post->dadata);
//            $ts = ( new \DateTime("now") )->format('Y-m-d h:i:s');
//            $userData->setTimestamp( $ts );
            $userData->setTime(time());
            try {
                $user->setUserData([$userData]);
            } catch (InvalidQueryException $e) {
                $return['error'] = $e->getMessage();
            }/**/
        } else
            $return["error"] = "Ошибка";

        return new JsonModel($return);
    }

    public function ajaxSetUserAddressAction()
    {
        $userId = $this->identity();
        $user = $this->userRepository->find(['id' => $userId]);
        $return["userAddress"] = $this->htmlProvider->writeUserAddress($user);
        $return["legalStore"] = $container->legalStore;
        return new JsonModel($return);
    }

    public function unsetFilterForCategoКyAction()
    {
        $post = $this->getRequest()->getPost();
        $category_id = $post->category_id;
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
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
    private function matchProduct(HandbookRelatedProduct /* \Application\Model\Entity\Product */ $product, array $characteristics): bool
    {
//        $markerObject = $product->receiveMarkerObject();
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
        $category_id = $params['category_id'];
        $categoryTree = $this->categoryRepository->findCategoryTree($category_id, [$category_id]);
        $where = new Where();
        list($low, $high) = explode(';', $params['priceRange']);
        $where->lessThanOrEqualTo('price', $high)->greaterThanOrEqualTo('price', $low);
//        $where->equalTo('category_id', $params['category_id']);
//        $categories = $this->categoryRepository->findAll(['id' => '000000006']);
//        foreach($categories as $c) {
//            echo '<pre>';
//            print_r($c);
//            echo '</pre>';
//        }
//        exit;
        $where->in('category_id', $categoryTree);

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
        $this->prepareCharacteristics($params['characteristics']);
        if (empty($params['priceRange'])) {
            $params['priceRange'] = '0;' . PHP_INT_MAX;
        }
        unset($params['offset']);
        unset($params['limit']);
        $params['where'] = $this->getWhere($params);
        //$params['order'] = ['price ASC'];
        $products = $this->handBookRelatedProductRepository->findAll($params);

        $filteredProducts = [];
        foreach ($products as $product) {
            $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];
            $matchResult = $this->matchProduct($product, /* $params['characteristics'] */ $characteristics);
            if ($matchResult && !isset($filteredProducts[$product->getId()])) {
                $filteredProducts[$product->getId()] = $product;
            }
        }
        return $filteredProducts;
    }

    private function getProductCards($params)
    {
        $this->prepareCharacteristics($params['characteristics']);
        if (empty($params['priceRange'])) {
            $params['priceRange'] = '0;' . PHP_INT_MAX;
        }
        unset($params['offset']);
        unset($params['limit']);
        $params['where'] = $this->getWhere($params);
        //$params['order'] = ['price ASC'];
        $products = $this->handBookRelatedProductRepository->findAll($params);

        $filteredProducts = [];
        foreach ($products as $product) {
            $characteristics = null == $params['characteristics'] ? [] : $params['characteristics'];
            $matchResult = $this->matchProduct($product, /* $params['characteristics'] */ $characteristics);
            if ($matchResult && !isset($filteredProducts[$product->getId()])) {

                /*$charNew = $product->getParamVariableList();
                $characteristicsArray = [];
                if (!empty($charNew)) {
                    $characteristicsArray = Json::decode($charNew, Json::TYPE_ARRAY);
                }*/
                $filteredProducts[$product->getId()] = [
                    "reserve" => $product->receiveRest(),
                    "price" => $product->getPrice(),
                    "oldprice" => $product->getOldPrice(),
                    "discount" => $product->getDiscount(),
                    "image" => $product->receiveFirstImageObject()->getHttpUrl(),
                    //"chars" => $characteristicsArray,
                ];
            }
        }
        return $filteredProducts;
    }

    private function prepareCharacteristics(&$characteristics)
    {
        if (!$characteristics) {
            return;
        }
        foreach ($characteristics as $key => &$value) {
            foreach ($value as &$v) {
                if (empty($v)) {
                    $v = '0;' . PHP_INT_MAX;
                }
            }
        }
    }

//    private function getProducts1($params)
//    {
//        $where = new Where();
//        $where->greaterThan('rest', 0);
//        $rests = $this->stockBalanceRepository->findAll(['where' => $where]);
//        $products = [];
//        $params['where'] = $this->getWhere($params);
//        foreach($rests as $rest) {
//            $productId = $rest->getProductId();
//            $product = $this->handBookRelatedProductRepository->findFirstOrDefault(['id' => $productId]);
//            $matchResult = $this->matchProduct($product, $params['characteristics']);
//            if ($matchResult && !isset($products[$product->getId()]) ) {
//                $products[$productId] = $product;
//            }
//        }
//        return $products;
//    }

    public function setFilterForCategoryAction()
    {

        $post = $this->getRequest()->getPost()->toArray();

        $products = $this->getProducts($post);

        return (new ViewModel(['products' => $products]))->setTerminal(true);

        // exit ("<pre>".print_r($post, true)."</pre>");


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

    public function getFiltredProductForCategoryJsonAction()
    {

        $post = $this->getRequest()->getPost()->toArray();
        $products = $this->getProductCards($post);
        return new JsonModel(["products" => $products]);
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

}
