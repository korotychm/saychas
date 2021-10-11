<?php

// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Laminas\Json\Json;
//use Laminas\Http\Response;
use Application\Resource\Resource;
//use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
//use Laminas\Db\ResultSet\HydratingResultSet;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\CountryRepositoryInterface;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicValueRepositoryInterface;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
//use Application\Model\RepositoryInterface\StoreRepositoryInterface;
//use Application\Model\Entity\User;
//use Application\Model\Entity\UserData;
use Application\Model\Entity\Store;
use Application\Model\Entity\Basket;
use Application\Model\Entity\ProductFavorites;
//use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;

class HtmlProviderService
{

    private $stockBalanceRepository;
    private $brandRepository;
    private $countryRepository;
    private $providerRepository;
    private $priceRepository;
    private $characteristicRepository;
    private $productCharacteristicRepository;
    private $characteristicValueRepository;
    private $colorRepository;
    private $basketRepository;
    private $productRepository;
    private $productImageRepository;

    public function __construct(
            StockBalanceRepositoryInterface $stockBalanceRepository,
            BrandRepositoryInterface $brandRepository,
            ColorRepositoryInterface $colorRepository,
            CountryRepositoryInterface $countryRepository,
            ProviderRepositoryInterface $providerRepository,
            PriceRepositoryInterface $priceRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository,
            CharacteristicValueRepositoryInterface $characteristicValueRepository,
            BasketRepositoryInterface $basketRepository,
            HandbookRelatedProductRepositoryInterface $productRepository,
            ProductImageRepositoryInterface $productImageRepository
    )
    {
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->countryRepository = $countryRepository;
        $this->providerRepository = $providerRepository;
        $this->priceRepository = $priceRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->characteristicValueRepository = $characteristicValueRepository;
        $this->basketRepository = $basketRepository;
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
    }

    /**
     * Returns Array
     * @return Array
     */
    public function getMainMenu($mainMenu)
    {
        return Json::decode($mainMenu->getValue(), Json::TYPE_ARRAY);
    }

    /**
     * Returns Array
     * @return Array
     */
    public function getUserPayCardInfoService($userPaycards)
    {
        $payCards = [];
        if (!empty($userPaycards)) {

            foreach ($userPaycards as $paycard) {
                $payCards[] = ["id" => $paycard->getCardId(), "pan" => $paycard->getPan()];
            }
        }
        return $payCards;
    }

    /**
     * Returns Array
     * @return Array
     */
    public function basketCheckBeforeSendService($param, $basket)
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $param["legalStore"] = $container->legalStore;
        $legalStoreKey = (!empty($param["legalStore"])) ? array_keys($param["legalStore"]) : [];
        $return["result"] = true;
        $error = ["result" => false, "reload" => true, "reloadUrl" => "/basket"];
        $whatHappened = [];
        /**/ if ($param['basketUserId'] != $param['userId']) {
            return $error;
        }/**/
        foreach ($basket as $basketItem) {
            $basketProducts[$basketItem->getProductId()] = ['price' => $basketItem->getPrice(), 'total' => $basketItem->getTotal(),];
        }
        while (list($key, $product) = each($param['postedProducts'])) { //
            if (empty($basketProducts[$key])) {
                $error["reloadUrl"] = "/user/orders";
                return $error;
            }
            if (empty($param["legalStore"][$product['store']])) {
                $whatHappened['stores'][$product['store']][] = $key;
                $return["result"] = false;
            }
            $productRow = $this->productRepository->find(['id' => $key]);
            $price = (int) $productRow->receivePriceObject()->getPrice();
            $rest = $productRow->receiveRest($legalStoreKey);
            if ($basketProducts[$key]["price"] != $price) {
                $whatHappened['products'][$key]['oldprice'] = $basketProducts[$key]["price"];
                $whatHappened['products'][$key]['price'] = $price;
                $return["result"] = false;
            }
            if ($basketProducts[$key]["total"] > $rest) {
                $whatHappened['products'][$key]['oldrest'] = $basketProducts[$key]["total"];
                $whatHappened['products'][$key]['rest'] = $rest;
                $return["result"] = false;
            }
        }
        if (!empty($whatHappened)) {
            $container->whatHappened = $whatHappened;
        }
        $return['test'] = [$whatHappened];
        return $return;
    }

    public function orderList($orders)
    {
        $returns = [];
        foreach ($orders as $order) {

            $return['orderId'] = $order->getOrderId();
            $return['orderStatus'] = $order->getStatus();
            $return['orderDate'] = $order->getDateCreated(); //date_created
            $return['basketInfo'] = ($order->getBasketInfo()) ? Json::decode($order->getBasketInfo(), Json::TYPE_ARRAY) : [];
            unset($return['basketInfo']['userGeoLocation']['data']);

            $return['deliveryInfo'] = ($order->getDeliveryInfo()) ? Json::decode($order->getDeliveryInfo(), Json::TYPE_ARRAY) : [];
            $return['paymentInfo'] = ($order->getPaymentInfo()) ? Json::decode($order->getPaymentInfo(), Json::TYPE_ARRAY) : [];
            $return['totalBill'] = ($order->getConfirmInfo()) ? Json::decode($order->getConfirmInfo(), Json::TYPE_ARRAY) : [];
            $return['date'] = $order->getDateCreated();
            $returns[] = $return;
        }
        //json_encode($return)
        return $returns;
    }

    public function getCategoryFilterJson($filters)
    {
        if (empty($filters)) {
            return["error" => "errorId"];
        }
        foreach ($filters as $row) {
            $row['val'] = explode(",", $row['val']);
            $row['val'] = array_unique($row['val']);
            //$getUnit = $this->characteristicRepository->findFirstOrDefault(["id" => $row['id']])->getUnit();
            sort($row['val']);
            $chars = [];
            foreach ($row['val'] as $val) {
                //$j++;
                $char = $this->characteristicValueRepository->findFirstOrDefault(['id' => $val]);
                $valuetext = $val;
                if ($row['type'] == Resource:: CHAR_VALUE_REF) {
                    $valuetext = $char->getTitle();
                } elseif ($row['type'] == Resource::PROVIDER_REF) {
                    $valuetext = $this->providerRepository->findFirstOrDefault(['id' => $val])->getTitle();
                } elseif ($row['type'] == Resource::BRAND_REF) {
                    $valuetext = $this->brandRepository->findFirstOrDefault(['id' => $val])->getTitle();
                } elseif ($row['type'] == Resource::COLOR_REF) {
                    $color = $this->colorRepository->findFirstOrDefault(['id' => $val]);
                    $valuetext = [$color->getValue(), $color->getTitle()];
                } elseif ($row['type'] == Resource::COUNTRY_REF) {
                    $valuetext = $this->countryRepository->findFirstOrDefault(['id' => $val])->getTitle(); /**/
                }
                $chars[] = [
                    "valueCode" => $val,
                    "value" => $valuetext,
                ];
            }
            $return[] = ["id" => $row['id'], "title" => $row['tit'], "type" => $row['type'], "unit" => $this->characteristicRepository->findFirstOrDefault(["id" => $row['id']])->getUnit(), "options" => $chars];
        }
        return !empty($return) ? $return : [];
    }

    private function valueParce(array $v = [], $chType)
    {
        //$bool = [Resource::NO, Resource::YES];
        if (!$v or!is_array($v)) {
            return;
        }
        $value = [];
        foreach ($v as $val) {
            if (!$val) {
                continue;
            } elseif ($chType == Resource::BOOLEAN) {
                $value[] = $val == 0 ? Resource::NO : Resource::YES;
            } elseif ($chType == Resource::COUNTRY_REF) {
                $value[] = $this->countryRepository->findFirstOrDefault(['id' => $val])->getTitle();
            } elseif ($chType == Resource::CHAR_VALUE_REF) {
                $value[] = $this->characteristicValueRepository->findFirstOrDefault(['id' => $val])->getTitle();
            } elseif ($chType == Resource::BRAND_REF) {
                $value[] = "<a href='/brand/$val' >" . $this->brandRepository->findFirstOrDefault(['id' => $val])->getTitle() . "</a>";
            } elseif ($chType == Resource::COLOR_REF) {
                $color = $this->colorRepository->findFirstOrDefault(['id' => $val]);
                $value[] = "<div class='iblok relative'  ><div class='cirkul iblok relative' style='background-color:{$color->getValue()};'></div>{$color->getTitle()}</div>";
            } else {
                $value = $v;
            }
        }

        return !empty($value) ? join(", ", $value) : "";
    }

    public function productPageService($filteredProducts)
    {
        $productImages = $return = $filters = [];
        //foreach ($filteredProducts as $product) {
        $product = $filteredProducts->current();
        $return['oldPrice'] = 0;
        $return['price'] = (int) $product->getPrice();
        $return['discont'] = (int) $product->getDiscount();

        if ($return['discont'] > 0) {
            $return['oldPrice'] = $return['price'];
            $return['price'] = $return['oldPrice'] - ($return['oldPrice'] * $return['discont'] / 100);
        }

        $return['price_formated'] = number_format(($return['price'] / 100), 0, "", "&nbsp;");
        $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
        $return['rest'] = ($rest) ? (int) $rest->getRest() : 0;
        $return['product_id'] = $id = $product->getId();
        $return['title'] = $product->getTitle();
        $parentCategoryId = $product->getParentCategoryId();
        $categoryId = $product->getCategoryId();
        
        if (!empty($parentCategoryId) && $categoryId != $parentCategoryId) {
            $categoryId = $parentCategoryId;
        }
        
        $return['category_id'] = $categoryId;
        $charNew = $product->getParamVariableList();
        $characteristicsArray = !empty($charNew) ? Json::decode($charNew, Json::TYPE_ARRAY) : [];
        $productImages[] = $product->getHttpUrl();
        $vendor = $product->getVendorCode();
        $productId = $product->getId();
        $return["brand"]["title"] = $product->getBrandTitle();
        $return["brand"]["id"] = $product->getBrandId();
        $return["brand"]["image"] = $this->brandRepository->findFirstOrDefault(['id' => $return["brand"]["id"]])->getImage();
        $return["provider"]["id"] = $product->getProviderId();
        $provider = $this->providerRepository->findFirstOrDefault(['id' => $return["provider"]["id"]]);
        $return["provider"]["image"] = (!empty($provider)) ? $provider->getImage() : "";
        $return["provider"]["title"] = (!empty($provider)) ? $provider->getTitle() : "";
        $description = $product->getDescription();

        if (!empty($description)) {
            $return['description']["text"] = StringHelper::eolFormating($description);
            $return['description']['if_spoiler'] = ((strlen($description) < 501));
            $return['description']['tinytext'] = StringHelper::eolFormating(mb_substr($description, 0, 500));
        }

        //$characteristicsArray = array_diff($characteristicsArray, array(''));
        if (!empty($characteristicsArray)) {
            foreach ($characteristicsArray as $char) {
                $ch = $this->characteristicRepository->findFirstOrDefault(['id' => $char['id'] . "-" . $categoryId]);
                $chArray = $ch->getIsList();
                $chType = $ch->getType();

                if ($char['value']) {
                    ($chArray) ? $v = $char['value'] : $v[] = $char['value'];
                    $value = $this->valueParce($v, $chType);
                    unset($v);
                }

                $char = ["id" => $char['id'], "title" => $ch->getTitle(), "type" => $chType, "array" => $chArray, "value" => $value, "unit" => $ch->getUnit(),];
                $return["characteristics"][0][] = $char;
                if ($ch->getIsMain()) {
                    $return["characteristics"][1][] = $char;
                }
            }
        } else {
            $return["characteristics"] = [];
        }
        
        $return['categoryId'] = $categoryId;
        $return['appendParams'] = ['vendorCode' => $vendor, 'productId' => $productId, 'rest' => $return['rest'], 'test' => "test",];

        return $return;
    }

    public function getUserAddresses($user = null, $limit)
    {
        $return = ['address' => [], 'addresses' => []];
        if (null === $user) {
            return $return;
        }
        $addressData = $user->getUserData();
        if (!empty($addressData)) {
            $i = 0;  //индекс для лимита вывода адресов!
            foreach ($addressData as $address) {
                if ($i === 0) {
                    $return['address'] = ["id" => $address->getId(), "text" => $address->getAddress(), "geodata" => $address->getGeoData()];
                } else {
                    $return['addresses'][] = ["id" => $address->getId(), "text" => $address->getAddress()];
                }
                $i++;
                if ($i > $limit) {
                    break;
                }
            }
        }
        return $return;
    }

    public function getUserInfo($user)
    {
        if (null == $user) {
            return [];
        }
        $return['id'] = $user->getId();
        $return['userid'] = $user->getUserId();
        $return['name'] = $user->getName();
        $return['phone'] = $user->getPhone();
        $return['email'] = $user->getEmail();
        $userData = $user->getUserData();
        $usdat = $userData->current();
        if (null != $usdat) {
            $return['userAddress'] = $usdat->getAddress(); //$container->userAddress;
            $return['userGeodata'] = $usdat->getGeoData();
        }
        return $return;
    }

    public function basketPayInfoData($post, $param)
    {
        $products = $post->products;
        $storeAdress = [];
        if (!empty($selfdelevery = $post->selfdelevery and $countSelfdelevery = count($selfdelevery))) {
            foreach ($selfdelevery as $providerinfo) {
                $stores = Store::findAll(['where' => ['id' => $providerinfo]]);
                $store = $stores->current();
                $storeAdress[] = StringHelper::cutAddress($store->getAddress());
            }
        }
        $j = 0;
        if (!empty($products)) {
            while (list($p, $c) = each($products)) {
                if ($c["count"] <= 0) {
                    continue;
                }
                $product = $this->productRepository->find(['id' => $p]);
                if (null == $product) {
                    continue;
                }
                if (!$price = (int) $product->receivePriceObject()->getPrice()) {
                    continue;
                }
                $total += ($price * $c['count']);
                $j += $c["count"];
                if ($providerId = $product->getProviderId()) {
                    $provider[$providerId] = 1;
                }
            }
        }
        if (!empty($provider)) {
            $countDelevery = count($provider);
        }
        $countDelevery = (int) $countDelevery - $countSelfdelevery;
        if (!$post->ordermerge) {
            $priceDelevery = $countDelevery * $param['hourPrice'];
        } else {
            $timeDelevery = (!$post->ordermerge) ? $post->timepointtext1 : $post->timepointtext3;
            $priceDelevery = $countDelevery * $param['mergePrice'] + ceil($countDelevery / $param['mergecount']) * $param['mergePriceFirst'];
            $countDelevery = ceil($countDelevery / $param['mergecount']);
            $countDelevery = ($countDelevery < 0) ? 0 : $countDelevery;
        }
        $return["basketpricetotalall"] = $return["producttotal"] = $total;
        $return["productcount"] = $j;
        $return["timeDelevery"] = $timeDelevery;
        $return["countSelfdelevery"] = $countSelfdelevery;
        $return["priceDelevery"] = $priceDelevery;
        $return["countDelevery"] = $countDelevery;
        $return["countDeleveryText"] = $countDelevery;
        $return["countDeleveryText"] .= ($countDelevery < 2 ) ? " доставка " : (($countDelevery > 1 and $countDelevery < 5) ? " доставки" : " доставок ");
        $return["storeAdress"] = $storeAdress;
        return $return;

//         'productcount' => $row['count'],
//            'producttotal' => $row['total'],
    }

    public function basketMergeData($post, $param)
    {
        $return = [];
        $timeDelevery3Hour = $timeDelevery1Hour = [];
        $products = $post->products;
        if (!$selfdelevery = $post->selfdelevery) {
            $countSelfdelevery = 0;
        } else {
            $countSelfdelevery = count($selfdelevery);
        }
        //return ['count' => print_r($products , true)];
        $container = new Container(Resource::SESSION_NAMESPACE);
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray = (!empty($container->legalStoreArray)) ? $container->legalStoreArray : [];

        if ($products and!empty($products)) {
            $products = array_keys($products);
            foreach ($products as $pId) {
                $product = $this->productRepository->find(['id' => $pId]);
                $providerId = $product->getProviderId();
                $provider = $this->providerRepository->find(['id' => $providerId]);
                $store = $provider->recieveStoresInList($legalStore);
                $idStore = $store->getId();
                $timeClose[$idStore] = $legalStoresArray[$idStore]['time_until_closing'];
            }
            $return['timeClose'] = min($timeClose);
            $timeDelevery1Hour[] = [
                "lable" => Resource::BASKET_SAYCHAS_title,
                "value" => 0,
                "rel" => Resource::BASKET_SAYCHAS_do,
            ];
            $timeDelevery3Hour[] = [
                "lable" => Resource::BASKET_SAYCHAS3_title,
                "value" => 0,
                "rel" => Resource::BASKET_SAYCHAS3_do,
            ];

            for ($i = 1; $i <= 12; $i++) {
                $timeStart = time() + 3600 * $i;
                $timeEnd = time() + 3600 * $i + 3600;
                $time3End = time() + 3600 * $i + 3600 * 3;

                if ($timeEnd > $return['timeClose']) {
                    break;
                }
                $value = "c " . date("H", $timeStart) . ":00" . " до " . date("H", $timeEnd) . ":00";
                $value3 = "c " . date("H", $timeStart) . ":00" . " до " . date("H", $time3End) . ":00";
                $rel = (date("d", $timeStart) == date("d")) ? " сегодня " : " завтра ";

                $timeDelevery1Hour[] = [
                    "lable" => $value, //.date("r",$return['timeClose']),
                    "value" => date("H", $timeStart),
                    "rel" => $rel . $value,
                ];

                if ($time3End < $return['timeClose']) {
                    $timeDelevery3Hour[] = [
                        "lable" => $value3,
                        "value" => date("H", $timeStart),
                        "rel" => $rel . $value3,
                    ];
                }
            }
            //$return["count"] = min($timeClose);
            $return["countStors"] = count($timeClose) - $countSelfdelevery;
            $return["select1hour"] = $timeDelevery1Hour;
            $return["select3hour"] = $timeDelevery3Hour;
            $return["hourPrice"] = $return["countStors"] * $param["hourPrice"];
            $return["hour3Price"] = $return["countStors"] * $param['mergePrice'] + ceil($return["countStors"] / $param['mergecount']) * $param['mergePriceFirst'];
        }
        return $return;
    }

    public function basketWhatHappenedUpdate($userId, $products)
    {
        $j = 0;
        while (list($productId, $changes) = each($products)) {
            $persist = false;
            $basketItem = Basket::findFirstOrDefault(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);

            if (null != $changes['rest'] and $changes['rest'] >= 0) {
                $basketItem->setTotal($changes['rest']);
                $persist = true;
            }
            if ($changes['price'] > 0) {
                $basketItem->setPrice($changes['price']);
                $persist = true;
            }
            if ($persist) {
                $basketItem->persist(['user_id' => $userId, 'product_id' => $productId, 'order_id' => "0"]);
                $j++;
            } /**/
        }
        return "$j products updated ";
        //return $product_id;
    }

    public function basketData($basket, $userId)
    {
        $countproducts = 0;
        $countprovider = [];
        $productStoreId = null;
        $whatHappened = null;
        $container = new Container(Resource::SESSION_NAMESPACE);
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray = $container->legalStoreArray;
        $item = [];
        foreach ($basket as $b) {
            if ($pId = $b->productId) {
                /** @var HandbookRelatedProduct */
                $product = $this->productRepository->find(['id' => $pId]);
                $oldprice = $b->price;
                $price = (int) $product->receivePriceObject()->getPrice();
                $rest = $product->receiveRest($legalStore);
                $productProviderId = $product->getProviderId();
                $productProvider = $this->providerRepository->find(['id' => $productProviderId]);
                $count = $b->total;
                $productStore = $productProvider->recieveStoresInList($legalStore);
                $productAvailable = (null != $productStore);
                unset($productStoreId);
                if ($rest) {
                    $availblechek[$productProviderId] = true;
                }
                if ($productAvailable) {
                    $productStoreId = $productStore->getId();
                    if ($oldprice != $price) {
                        $whatHappened['products'][$pId]['oldprice'] = $oldprice;
                        $whatHappened['products'][$pId]['price'] = $price;
                    }
                    if ($count > $rest) {
                        $whatHappened['products'][$pId]['oldrest'] = $count;
                        $whatHappened['products'][$pId]['rest'] = $rest;
                        $count = $rest;
                    }
                    if ($count == 0 and 0 < $rest) {
                        $whatHappened['products'][$pId]['instock'] = $rest;
                    }
                }//if ($rest) $countproducts +=$count ;
                if ($rest) {
                    $countproducts++;
                    $countprovider[$product->getProviderId()] = 1;
                }
                $item[$product->getProviderId()][] = [
                    'id' => $pId,
                    'image' => $this->productImageRepository->findFirstOrDefault(["product_id" => $pId])->getHttpUrl(),
                    'title' => $product->getTitle(),
                    'price' => $price,
                    'oldprice' => $b->price,
                    // 'availble' => '1',
                    'availble' => $rest,
                    'count' => $count,
                    'store' => $productStoreId,
                    'isFav' => $this->isInFavorites($pId, $userId),
                ];
            }
            if ($whatHappened) {
                $container->whatHappened = $whatHappened;
            }
        }
        if (empty($item)) {
            return;
        }



        while (list($prov, $prod) = each($item)) {
            $j++; //индекс  для управления сортировкой  магазинов по статусу доступности
            $provider = $this->providerRepository->find(['id' => $prov]);
            $store = $provider->recieveStoresInList($legalStore);

            $infostore1c = "";
            if (null != $store) {
                $idStore = $store->getId();
                $infostore1c .= ($legalStoresArray[$idStore]['working_hours_from']) ?
                        "сегодня с " . substr($legalStoresArray[$idStore]['working_hours_from'], 0, -3) . " до " . substr($legalStoresArray[$idStore]['working_hours_to'], 0, -3) : "";
                $infostore1c .= ($legalStoresArray[$idStore]['time_until_closing']) ? "<span class='blok mini'>заказать возможно до  " . date("Y.m.d H:i", $legalStoresArray[$idStore]['time_until_closing']) . "</span>" : "";
                $IntervalOpen = $legalStoresArray[$idStore]['time_until_open'];
                $timStoreOpen = $IntervalOpen + time();
                if (!$legalStoresArray[$idStore]['status']) {
                    //все работает
                    $provider_disable = false;
                    $returnprefix = $j * -1;
                    $provider_address = $store->getAddress() . $ifostore1c;
                    $provider_address .= ($store->getDescription()) ? ", " . $store->getDescription() : "";
                    $provider_store = $store->getTitle();
                    $provider_store_id = $store->getId();
                    $provider_addressappend = StringHelper::cutAddress($provider_address);
                    $countprovider++;
                } else {
                    if ($IntervalOpen > 0) {
                        $returnprefix = $j;
                        //закрыт на ночь
                        $provider_disable = Resource::STORE_CLOSE_FOR_NIGHT;
                        $infostore1c = Resource::STORE_CLOSE_FOR_NIGHT_ALT;
                        $infostore1c .= (date("d") == date("d", $timStoreOpen)) ? " сегодня " : " завтра ";
                        $infostore1c .= "в " . date("H:i", $timStoreOpen);
                    } else {
                        $returnprefix = $j + 100; //индекс для сортировки
                        //закрыт на неопределеноне время
                        $provider_disable = Resource::STORE_UNAVALBLE;
                        $infostore1c = Resource::STORE_UNAVALBLE_ALT;
                    }
                }

                //  unset($idStore, $timStoreOpen);
            } else {
                $provider_disable = Resource::STORE_OUT_OF_RANGE;
                $returnprefix = $j + 1000; //индекс для сортировки
                $provider_address = $provider_worktime = $provider_timeclose = "";
                $infostore1c = Resource::STORE_OUT_OF_RANGE_ALT;
            }
            $return["product"][$returnprefix] = [
                "provider_id" => $provider_store_id, //$prov,
                "provider_main_id" => $prov,
                "availblechek" => $availblechek[$prov],
                "provider_disable" => $provider_disable,
                "provider_name" => $provider->getTitle(),
                "provider_logo" => $provider->getImage(),
                "provider_address" => $provider_address,
                "provider_addressappend" => $provider_addressappend,
                "provider_worktime" => $provider_worktime,
                "provider_timeclose" => "",
                "provider_store" => $provider_store,
                "provider_store_id" => $provider_store_id,
                //"provider_store_off" => $provider_store_off,
                "products" => $prod,
                "infostore1c" => $infostore1c,
            ];
        }
        $countproviders = (int) count($countprovider);
        if ($countproducts) {

            $return["title"] = ($countproducts == 1) ? "$countproducts позиция " : ($countproducts > 1 and $countproducts < 5) ? "$countproducts позиции " : "$countproducts позиций ";
            $return["title"] .= "из ";
            $return["title"] .= ($countproviders == 1) ? "$countproducts магазина " : "$countproducts магазинов ";
        }
        $return ["countproviders"] = $countproviders;
        $return ["countprducts"] = $countproducts;
        if (!empty($return["product"])) {
            ksort($return["product"]);
        }
        //exit (print_r($return["product"]));
        return $return;
    }

    private function isInFavorites($productId, $userId)
    {
        if (!empty($userId) && !empty($productId)) {
            if (!empty(ProductFavorites::find(['user_id' => $userId, 'product_id' => $productId]))) {
                return true;
            }
        }
        return false;
    }

}
