<?php

// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Laminas\Json\Json;
use Application\Resource\StringResource;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
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
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Model\Entity\Store;
use Application\Model\Entity\Basket;
use Application\Helper\ArrayHelper;
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
        return Json::decode($mainMenu->getValue() , Json::TYPE_ARRAY);
      
    }
    
    
    /**
     * Returns Html string
     * @return string
     */
    /*public function testHtml()
    {
        return '<h1>Hello world!!!</h1>';
    }*/
    
    
    
    /**
     * Returns Array
     * @return Array
     */
    public function basketCheckBeforeSendService($param, $basket)
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $param["legalStore"] = $container->legalStore;
        $legalStoreKey = (!empty($param["legalStore"])) ? array_keys($param["legalStore"]) : [];
        $return["result"] = true;
        $error = ["result" => false, "reload" => true, "reloadUrl" => "/basket"];
        $whatHappened=[];
        /**/ if ($param['basketUserId'] != $param['userId']) {
            return $error;
        }/**/

        foreach ($basket as $basketItem) {
            $basketProducts[$basketItem->getProductId()] = [
                'price' => $basketItem->getPrice(),
                'total' => $basketItem->getTotal(),
            ];
        }
        $test = false;

        while (list($key, $product) = each($param['postedProducts'])) { //
            if (empty($basketProducts[$key] or $test)) {
                $error["reloadUrl"] = "/client-orders";
                return $error;
            }
            if (empty($param["legalStore"][$product['store']]) or $test) {
                $whatHappened['stores'][$product['store']][] = $key;
                $return["result"] = false;
            }

            $productRow = $this->productRepository->find(['id' => $key]);
            $price = (int) $productRow->receivePriceObject()->getPrice();
            $rest = $productRow->receiveRest($legalStoreKey);
            if ($basketProducts[$key]["price"] != $price or $test) {
                $whatHappened['products'][$key]['oldprice'] = $basketProducts[$key]["price"];
                $whatHappened['products'][$key]['price'] = $price;
                $return["result"] = false;
            }
            if ($basketProducts[$key]["total"] > $rest or $test) {
                $whatHappened['products'][$key]['oldrest'] = $basketProducts[$key]["total"];
                $whatHappened['products'][$key]['rest'] = $rest;
                $return["result"] = false;
            }
            //$test  = false;
        }
        if (!empty($whatHappened)) {
            $container->whatHappened = $whatHappened;
        }
        $return['test'] = [$whatHappened];
        return $return;
    }

    /**
     * Returns Html string
     * @return string
     */
 /*   public function breadCrumbs($a = [])
    {
        if ($a and count($a)){
            $return[] = "<a href=# class='catalogshow'>Каталог</a>";
            $a = array_reverse($a);
            foreach ($a as $b) {
                $return[] = "<a href=/catalog/" . $b[0] . ">" . $b[1] . "</a>";
            }
            return join('<b class="brandcolor"> : </b>', $return);
        }
    }*/

    public function orderList($orders)
    {
        $returns=[];
        foreach ($orders as $order) {

            $return['orderId'] = $order->getOrderId();
            $return['orderStatus'] = $order->getStatus();
            $return['basketInfo'] = Json::decode($order->getBasketInfo(), Json::TYPE_ARRAY);
            unset($return['basketInfo']['userGeoLocation']['data']);

            $return['deliveryInfo'] = Json::decode($order->getDeliveryInfo(), Json::TYPE_ARRAY);
            $return['date'] = $order->getDateCreated();
            $returns[] = $return;
            
        }
        //json_encode($return)
        return $returns;
    }

/*    public function breadCrumbsMenu($a = [])
    {
        if ($a and count($a)) {
            //<span class='bread-crumbs-item'></span>"
            //$return[] = "<a href=# class='catalogshow'>Каталог</a>";
            $a = array_reverse($a);
            $j = 0;
            foreach ($a as $b) {
                $return[] = "<span class='bread-crumbs-item breadtab$j'><a href=/catalog/" . $b[0] . ">" . $b[1] . "</a></span>";
                if ($j < 4){
                    $j++;
                }    
            }
            return "<div  class='bread-crumbs'>" . join("", $return) . "</div>";
        }
 }*/

    /**
     * Returns Html string
     * @return string
     */
    public function getCategoryFilterArray($filter, $categoryTree)
    {
        if (!$filter or!$categoryTree)
            return;
        $tree = [];
        foreach ($categoryTree as $category)
            $tree[] = $category[0];
        if (!count($tree))
            return;
        $where = "and `tit`.filter=1 and `tit`.category_id in (0," . join(",", $tree) . ")";
        return $where; // (print_r($filter, true));
    }

    public function getCategoryFilter($category_id = 0)
    {

    }

    /**
     * Return Html
     * @return string
     */
    public function getCategoryFilterHtml($filters, $category_id, $price = ["minprice" => 12000, "maxprice" => 54000])
    {
        $typeText[0] = "Заголовок";
        $typeText[1] = "Строка";
        $typeText[2] = "Число";
        $typeText[3] = "Булево";
        $typeText[4] = "Ссылка.Характеристики";
        $typeText[5] = "Ссылка.Поставщики";
        $typeText[6] = "Ссылка.Бренды";
        $typeText[7] = "Ссылка.Цвета";
        $typeText[8] = "Ссылка.Страна";
        $pricesel['maxprice'] = $price['maxprice'];
        $pricesel['minprice'] = $price['minprice'];

        if (!$filters or!$category_id)
            return;
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory = $container->filtrForCategory;
        if (!$filtred = $filtrForCategory[$category_id]['fltr'])
            $filtred = [];
        $return = "";
        $j = 0;
        foreach ($filters as $row) {
            $row['val'] = explode(",", $row['val']);
            $row['val'] = array_unique($row['val']);
            $getUnit = $this->characteristicRepository->findFirstOrDefault(["id" => $row['id']])->getUnit();
            sort($row['val']);
            //$row['val']=array_diff ([""], $row['val']);
            unset($options);
            foreach ($row['val'] as $val) {
                $j++;
                $char = $this->characteristicValueRepository->findFirstOrDefault(['id' => $val]);
                if ($val) {
                    $valuetext = $val;
                    if ($row['type'] == 4)
                        $valuetext = $char->getTitle();
                    elseif ($row['type'] == 5)
                        $valuetext = $this->providerRepository->findFirstOrDefault(['id' => $val])->getTitle();
                    elseif ($row['type'] == 6)
                        $valuetext = $this->brandRepository->findFirstOrDefault(['id' => $val])->getTitle();
                    elseif ($row['type'] == 7) {
                        $color = $this->colorRepository->findFirstOrDefault(['id' => $val]);
                        $valuetext = "<div class='iblok relative' >"
                                . " <div class=' checkgroup relative cirkulcheck ' for='$j' style='background-color:{$color->getValue()};' title='  {$color->getTitle()} '></div>"
                                //. "      {$color->getTitle()}   "
                                . "</div>";
                    } elseif ($row['type'] == 8)
                        $valuetext = $this->countryRepository->findFirstOrDefault(['id' => $val])->getTitle(); /**/

                    if ($row['type'] == 2) {
                        $options[] = $val;
                    } elseif ($row['type'] == 7) {
                        $options .= $valuetext
                                . "<input
                                    type='checkbox'
                                    class='none fltrcheck$j'
                                    name='characteristics[" . $row['id'] . "][]'

                                    value='" . $val . "'  "
                                . (false and in_array($option['valId'], $filtred) ? " checked " : "") . " >
                                ";
                    } else
                        $options .= "<div class='nopub checkgroup blok " . (in_array($option['valId'], $filtred) ? " zach " : "") . "' for='$j' >" . $valuetext
                                . "<input
                                     type='checkbox'
                                     class='none fltrcheck$j'
                                     name='characteristics[" . $row['id'] . "][]'

                                     value='" . $val . "'  "
                                . (false and in_array($option['valId'], $filtred) ? " checked " : "") . " >
                               </div>";
                }
            }
            if ($options) {
                if ($row['type'] == 2) {
                    $min = min($options);
                    $max = max($options);
                    // $options = "<input type=number step=0.1  min='$min' max='$max' class=iblok style='width:80px; margin-right:10px;' value='$min' >"
                    //          . "<input type=number step=0.1  min='$min' max='$max' class=iblok style='width:80px; ' value='$max' >";
                    $rzn = $max - $min;
                    $step = 1;
                    if ($rzn > 10000)
                        $step = 100;
                    elseif ($rzn > 1000)
                        $step = 10;
                    elseif ($rzn > 100)
                        $step = 1;
                    elseif ($rzn < 10)
                        $step = 0.1;

                    $maxsel = $max;
                    $minsel = $min;
                    $rangeId = str_replace("-", "", $row['id']);
                    $options = '
    <script>
        $(function(){
            $("#rangeslider' . $rangeId . '").ionRangeSlider({
                    hide_min_max: true,
                    keyboard: true,
                    min: ' . $min . ',
                    max: ' . $max . ',
                    from:' . $minsel . ',
                    to:  ' . $maxsel . ',
                    hideMinMax:true,
                    type: "double",
                    step: ' . $step . ',
                    postfix: "' . $getUnit . '",
                    grid: false,
                    onChange: function (obj) {

                        $("#minCost2' . $rangeId . '").val(obj.from);
                        $("#maxCost2-' . $rangeId . '").val(obj.to);
                        $("#minCost' . $rangeId . '").html(obj.from);
                        $("#maxCost' . $rangeId . '").html(obj.to);
              }
            });
        })
     </script> '
                            . "

                <div style='padding:0px 6px; display:block; position:relative'>
                    <input type='text' id='rangeslider$rangeId' class='rangeslider'  value='' name='characteristics[" . $row['id'] . "][]'  style=''/>
                        <div  style='' class='minvaluenum' ><span class='gray'>от</span>&nbsp;<span id='minCost$rangeId'>" . $minsel . "</span>
                       </div><div
                       style='' class='maxvaluenum' ><span class='gray'>до</span>&nbsp;<span id='maxCost$rangeId'>" . $maxsel . "</span></div>
                    <!-- input type=hidden class='numonly'   pattern='^[ 0-9]+$' name=\"characteristics[" . $row['id'] . "]['min']\" id='minCost2$rangeId' value='" . $minsel . "'
                    ><input type=hidden class='numonly'   pattern='^[ 0-9]+$' name=\"characteristics[" . $row['id'] . "]['max']\" id='maxCost2$rangeId' value='" . $maxsel . "' -->
                </div>
        ";
                } elseif ($row['type'] == 3) {
                    $options = "
                    <div class=blok >
                        <div class='fltronoff   onoff ' for=123  rel=1 >Нет
                            <input type='checkbox' rel=1 class='none  relcheck fltrcheck123' name='characteristics[" . $row['id'] . "]' value='0' >
                        </div>
                        <div class='fltronoff onoff  ' for=122  rel=1 >Да
                                <input type='checkbox' rel=1 class='none  relcheck  fltrcheck122' name='characteristics[" . $row['id'] . "]' value='1' >
                        </div>
                    </div>";
                    /* . "<radiogroup>"
                      . "<div class=blok ><input type=radio name='characteristics[".$row['id']."][]' value=1 > Да</div>"
                      . "<div class=blok ><input type=radio name='characteristics[".$row['id']."][]' value=0 > Нет</div>"
                      . "<div class=blok ><input type=radio name='characteristics[".$row['id']."][]' value=-1 checked > Не важно</div>"
                      . "</radiogroup>"; */
                }

                $return .= '<div class="ifilterblock"  >
                            <div class="filtritemtitle" rel="' . $row['id'] . '">' . $row['tit'] . (($getUnit) ? " <span class='gray iblok'>$getUnit</span>" : "") . ((false and $count = (int) $row['type']) ? "<div class='count' >$count</div>" : "") . '
                                <span class="blok mini gray nobold" >' . $typeText[$row['type']] . '</span>
                                     <span class="blok mini gray nobold" >id: ' . $row['id'] . '</span>
                             </div>

                            <!-- div class="filtritem" id="fi' . $row['id'] . '" -->
                                <div class="filtritemcontent" id="fc' . $row['id'] . '">
                                    <!-- div class="blok" ><div class="closefilteritem" rel="' . $row['id'] . '">' . $row['tit'] . '</div></div -->
                                    ' . $options . "
                                    <!-- div class='block'><input type='button' value='применить' class='formsendbuttonnew'  ></div-->
                                 </div>
                            <!-- /div -->
                        </div>";
            }
        }

        $rzn = $price['maxprice'] / 100 - $price['minprice'] / 100;
        if ($rzn > 10000)
            $step = 100;
        elseif ($rzn > 1000)
            $step = 10;
        elseif ($rzn > 10)
            $step = 1;
        //elseif ($rzn < 10) $step=0.1;


        (true or $return) ? $return = '
        <div  class="formsendbutton" > post filter view</div>
        <input type=hidden name="offset" value="72" id="sqlOutline"  >
        <input type=hidden name="limit" value="72" id="sqlOutline"  >
        <input type=hidden name="category_id" value="'.$category_id.'"   >
        <script>
            $(function(){
                $("#rangeslider").ionRangeSlider({
                    hide_min_max: true,
                    keyboard: true,
                    min: ' . (int) $price['minprice'] . ',
                    max: ' . (int) $price['maxprice'] . ',
             //        from:' . (int) $pricesel['minprice'] . ',
             //       to:  ' . (int) $pricesel['maxprice'] . ',
                    hideMinMax:true,
                    type: "double",
                    step: ' . ($step * 100) . ',
                    postfix: "₽",
                    grid: false,
                    onChange: function (obj) {
                        console.log(' . $step . ');
                        var fmin = new Intl.NumberFormat("ru-RU").format(obj.from/100)
                        var fmax = new Intl.NumberFormat("ru-RU").format(obj.to/100)

                        $("#minCost2").val(obj.from);
                        $("#maxCost2").val(obj.to);
                        $("#minCost").html(fmin);
                        $("#maxCost").html(fmax);
                        $("#sub0").html("!");
                        $("#submitfiltr").show();
                        $(".submitfiltr").show();
		}
            });
        })
        </script> '
                        . "
        <div class=blok >
            <div class='fltrblock'>
                   <div class='filtritemtitleprice blokl' >Цена <span class='gray iblok'>₽</span></div>
                <div style='padding:0px 6px; display:block; position:relative'>
                    <input type='text' id='rangeslider' class='rangeslider'  value='' name='priceRange'  style=''/>
                        <div  style='' class='minvaluenum' ><span class='gray'>от</span>&nbsp;<span id=minCost>" . number_format($pricesel['minprice'] / 100, 0, ',', ' ') . "</span>
                       </div><div
                       style='' class='maxvaluenum' ><span class='gray'>до</span>&nbsp;<span id=maxCost>" . number_format($pricesel['maxprice'] / 100, 0, ',', ' ') . "</span></div>
                   </div>
            </div>
        </div>
        " . $return : "";
        //
        return $return;
    }

    public function getCategoryFilterJson($filters)
    {
        /*$typeText[0] = "Заголовок";
        $typeText[1] = "Строка";
        $typeText[2] = "Число";
        $typeText[3] = "Булево";
        $typeText[4] = "Ссылка.Характеристики";
        $typeText[5] = "Ссылка.Поставщики";
        $typeText[6] = "Ссылка.Бренды";
        $typeText[7] = "Ссылка.Цвета";
        $typeText[8] = "Ссылка.Страна"; */
        /*$pricesel['maxprice'] = $price['maxprice'];
        $pricesel['minprice'] = $price['minprice'];*/
        
        if (empty($filters)){
            return["error" => "errorId"];
        }
        $return = [];
        
        $j = 0;
        foreach ($filters as $row) {
           // return $row;
            $row['val'] = explode(",", $row['val']);
            $row['val'] = array_unique($row['val']);
            
            $getUnit = $this->characteristicRepository->findFirstOrDefault(["id" => $row['id']])->getUnit();
            sort($row['val']);

            //$row['val']=array_diff ([""], $row['val']);
            $chars = [];
            foreach ($row['val'] as $val) {
                $j++;
                $char = $this->characteristicValueRepository->findFirstOrDefault(['id' => $val]);
                    $valuetext = $val;
                    if ($row['type'] == 4)
                        $valuetext = $char->getTitle();
                    elseif ($row['type'] == 5)
                        $valuetext = $this->providerRepository->findFirstOrDefault(['id' => $val])->getTitle();
                    elseif ($row['type'] == 6)
                        $valuetext = $this->brandRepository->findFirstOrDefault(['id' => $val])->getTitle();
                    elseif ($row['type'] == 7) {
                        $color = $this->colorRepository->findFirstOrDefault(['id' => $val]);
                        $valuetext = [$color->getValue(),$color->getTitle()];
                    } elseif ($row['type'] == 8)
                        $valuetext = $this->countryRepository->findFirstOrDefault(['id' => $val])->getTitle(); /**/

                  $chars[] = [
                      "valueCode" => $val,
                      "value" => $valuetext,
                     
                      ];
                
                 
            }
            $return[]=["id" => $row['id'], "title" => $row['tit'],  "type"  => $row['type'], "unit" => $getUnit, "options" => $chars ];
        }
        return $return;
    }



    private function valueParce($v = [], $chType)
    {
        $bool = ["нет", "да"];
        if (!$v or !is_array($v))
            return $v;
        foreach ($v as $val) {
            if (!$val)
                continue;
            if ($chType == 3)
                $value[] = $bool[$val];

            elseif ($chType == 8) {
                $b = $this->countryRepository->findFirstOrDefault(['id' => $val]);
                $value[] = "<img style='margin-right:5px;' class='iblok' src='/img/flags/" . strtolower($b->getCode()) . ".gif' >" . $b->getTitle();
            } elseif ($chType == 4) {

                $value[] = $this->characteristicValueRepository->findFirstOrDefault(['id' => $val])->getTitle();
            } elseif ($chType == 6) {
                $value[] = $this->brandRepository->findFirstOrDefault(['id' => $val])->getTitle();
            } elseif ($chType == 7) {

                $color = $this->colorRepository->findFirstOrDefault(['id' => $val]);
                $value[] = "<div class='iblok relative'  >"
                        . "     <div class='cirkul iblok relative' style='background-color:{$color->getValue()}; border:1px solid var(--gray); width:25px; height:25px; vertical-align:middle'></div>"
                        . "      {$color->getTitle()}   "
                        . "</div>";
            } else
                $value = $v;
        }
        if ($value)
            return  print_r(join(", ", $value), true);
    }

    public function productPageService($filteredProducts, $category_id = 0)
    {
        $productImages = $return = $filters = [];
        foreach ($filteredProducts as $product) {

            $return['price'] = (int)$product->getPrice();
            $return['price_formated' ]= number_format(($return['price']/100), 0, "", "&nbsp;");
            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;
            $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $return['rest'] =  (int) $rest->getRest();
            $return['product_id'] = $id = $product->getId();
            $return['title'] = $product->getTitle();
            $return['category_id'] =  $categoryId = $product->getCategoryId();
            $charNew = $product->getParamVariableList();
            $characteristicsArray=[];
            if(!empty($charNew)) {
                $characteristicsArray = Json::decode($charNew, Json::TYPE_ARRAY);
            }
            $productImages[] = $product->getHttpUrl();
            $vendor = $product->getVendorCode();
            $return["brand"]["title"] = $product->getBrandTitle();
            $return["brand"]["id"] = $product->getBrandId();
            $brandobject = $this->brandRepository->findFirstOrDefault(['id' => $return["brand"]["id"]]);
            $return["brand"]["image"] = $brandobject->getImage();
            if ($description = $product->getDescription()){
                $return['description']["text"] = StringHelper::eolFormating($description);
                $return['description']['if_spoiler']=((strlen($description) < 501));
                $return['description']['tinytext'] = StringHelper::eolFormating(mb_substr($description,0,500));
            }
        }
        $characteristicsArray  = array_diff($characteristicsArray , array(''));
        if (!empty($characteristicsArray)) {
            foreach ($characteristicsArray as $char) {
                $ch = $this->characteristicRepository->findFirstOrDefault(['id' => $char['id'] . "-" . $categoryId]);
                $chArray = $ch->getIsList();
                $chType = $ch->getType();
                $getmain=($ch->getIsMain())?1:0;
                if ($value = $char['value']) {
                    ($chArray) ? $v = $value : $v[] = $value;
                    $value = $this->valueParce($v, $chType);
                    unset ($v);
                }
                $return["characteristics"][$getmain][] = [
                    "id" => $char['id'],
                    "title" => $ch->getTitle(),
                    "type"=> $chType,
                    "array" => $chArray ,
                    "value" => $value,
                    "unit" => $ch->getUnit(),
                ];
            }
        } else $return["characteristics"]=[];
        $productImages = array_unique($productImages);
        $return['images']=$productImages;
        
        $return['categoryId'] = $categoryId;
        $return['appendParams'] = ['vendorCode' => $vendor,'rest' => $totalRest,'test' => "test",];
        //exit(print_r($return));
        return $return;
    }

    public function writeUserAddress($user = null)
    {
        //$userId = $this->identity();
        /* $userData = new UserData();
          $userData->getUserId($userId)
          ->getAddress()
          ->getGeodata(); */
        
        $userData = $userAddress = $username = $hasalt = $altcontent="";
        $container = new Container(StringResource::SESSION_NAMESPACE);
        if (null != $user){
            $username = $user->getName();
            $userData = $user->getUserData();
            $usdat = $userData->current();
        }
        
        if (!empty($usdat)) {
            $userAddress = $usdat->getAddress(); //$container->userAddress;
            $userGeodata = $usdat->getGeoData();
            //exit ($userGeodata);

            $i = 0;  //индекс для лимита вывода адресов!
            foreach ($userData as $adress) {
                $adressId = $adress->getId();
                $adressText = $adress->getAddress();
                $altmenu[] = "<span class='menuitem pointer setuseraddress' rel='$adressId' >$adressText</span>";
                $i++;
                if ($i == 5)
                    break;
            }
            //unset($altmenu[0]);
            if (!empty($altmenu)) {
                $hasalt = " hasalt ";
                $altmenu[] = "<span class='menuitem pointer open-user-address-form red'  >Ввести адрес</span>";
                //<span class="strelka"></span>
                $altcontent = '<div class="altcontentview">

                          <div class="blok ">'
                        . join("", $altmenu)
                        . '</div>
                 </div>         ';
            } else { 
             
            }
        }
        ($userAddress) ?: $userAddress = "Укажи адрес и получи заказ за час!";

        return "<span class='blok relative $hasalt useraddressalt' >"
                . "$altcontent"
                . "<span>$userAddress</span>"
                . "<textarea id='geodatadadata' class='none' >$userGeodata</textarea></span>"
        //. "<h1></h1>"
        //
        //. "<input type=hidden22 id='geodatadadata22' class='none22' value=\"".($userGeodata2)?$userGeodata:"{2222}"."\" />"
        ;
    }

    public function getUserInfo($user)
    {
        if (null == $user) return [];
        //$container = new Container(StringResource::SESSION_NAMESPACE);
        $return['id'] = $user->getId();
        $return['userid'] = $user->getUserId();
        $return['name'] = $user->getName();
        $return['phone'] = $user->getPhone();
        $userData = $user->getUserData();

        $usdat = $userData->current();
        if (null != $usdat) {
            $return['userAddress'] = $usdat->getAddress(); //$container->userAddress;
            $return['userGeodata'] = $usdat->getGeoData();
            //exit ($userGeodata);
        }
        //exit (print_r($return));
        return $return;
    }

    public function basketPayInfoData($post, $param)
    {
        $products = $post->products;
        $storeAdress = [];
        if ($selfdelevery = $post->selfdelevery and $countSelfdelevery = count($selfdelevery)) {
            foreach ($selfdelevery as $providerinfo) {
                $stores = Store::findAll(['where' => ['id' => $providerinfo]]);
                $store = $stores->current();
                $storeAdress[] = StringHelper::cutAddress($store->getAddress());
            }
        }
        $j = 0;
        if (!empty($products)){
            while (list($p, $c) = each($products)) {

                $product = $this->productRepository->find(['id' => $p]);
                if (null == $product)
                    continue;
                if (!$price = (int) $product->receivePriceObject()->getPrice())
                    continue;
                $total += ($price * $c['count']);
                $j += $c["count"];
                if ($providerId = $product->getProviderId())
                    $provider[$providerId] = 1;
            }
        }
        if (!empty($provider)){
            $countDelevery = count($provider);
        }
        $countDelevery = (int) $countDelevery - $countSelfdelevery;
        if (!$post->ordermerge) {
            $priceDelevery = $countDelevery * $param['hourPrice'];
        } else {
            $timeDelevery = (!$post->ordermerge) ? $post->timepointtext1 : $post->timepointtext3;
            $priceDelevery = $countDelevery * $param['mergePrice'] + ceil($countDelevery / $param['mergecount']) * $param['mergePriceFirst'];
            $countDelevery = ceil($countDelevery / $param['mergecount']);
        }
        //$return["textDelevery"] = "за час";
        $return["basketpricetotalall"] = $return["total"] = $total;
        $return["count"] = $j;
        $return["timeDelevery"] = $timeDelevery;
        $return["countSelfdelevery"] = $countSelfdelevery;
        $return["priceDelevery"] = $priceDelevery;
        $return["countDelevery"] = $countDelevery;
        $return["countDeleveryText"] = $countDelevery;
        $return["countDeleveryText"] .= ($countDelevery < 2 ) ? " доставка " : (($countDelevery > 1 and $countDelevery < 5) ? " доставки" : " доставок ");
        $return["storeAdress"] = $storeAdress;

        return $return;
    }

    public function basketMergeData($post, $param)
    {
        /* $param = [
          "hourPrice" => 29900,  //цена доставки за час
          "mergePrice" => 5000, //цена доставки за три часа
          "mergePriceFirst" => 24900,  //цена доставки за первый махгазин  при объеденении заказа
          "mergecount" => 4, //количество объеденямых магазинов
          ]; */
        $return = [];
        $products = $post->products;
        if (!$selfdelevery = $post->selfdelevery)
            $countSelfdelevery = 0;
        else
            $countSelfdelevery = count($selfdelevery);
        //return ['count' => print_r($products , true)];
        $container = new Container(StringResource::SESSION_NAMESPACE);
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray = (!empty($container->legalStoreArray)) ? $container->legalStoreArray : [];

        if ($products and!empty($products)) {
            $products = array_keys($products);
            foreach ($products as $pId) {
                //$return["count"] = print_r($pId, true);         break;
                $product = $this->productRepository->find(['id' => $pId]);
                //$return["count"] = print_r($product, true);

                $providerId = $product->getProviderId();
                $provider = $this->providerRepository->find(['id' => $providerId]);

                $store = $provider->recieveStoresInList($legalStore);
                $idStore = $store->getId();
                $timeClose[$idStore] = $legalStoresArray[$idStore]['time_until_closing'];
             }
            $return['timeClose'] = min($timeClose);
            //<option value="0" rel=" в течение часа ">сейчас за час</option>
            $timeDelevery1Hour[] = [
                "lable" => StringResource::BASKET_SAYCHAS_title,
                "value" => 0,
                "rel" => StringResource::BASKET_SAYCHAS_do,
            ];
            /**/$timeDelevery3Hour[] = [
                "lable" => StringResource::BASKET_SAYCHAS3_title,
                "value" => 0,
                "rel" => StringResource::BASKET_SAYCHAS3_do,
            ]; /**/

            for ($i = 1; $i <= 12; $i++) {
                $timeStart = time() + 3600 * $i;
                $timeEnd = time() + 3600 * $i + 3600;
                $time3End = time() + 3600 * $i + 3600 * 3;

                if ($timeEnd > $return['timeClose']){
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
            $return["count"] = count($timeClose) - $countSelfdelevery;
            $return["select1hour"] = $timeDelevery1Hour;
            $return["select3hour"] = $timeDelevery3Hour;
            $return["hourPrice"] = $return["count"] * $param["hourPrice"];
            $return["hour3Price"] = $return["count"] * $param['mergePrice'] + ceil($return["count"] / $param['mergecount']) * $param['mergePriceFirst'];
        }
        return $return;
    }

    public function basketWhatHappenedUpdate($userId, $products)
    {
        while (list($productId, $changes) = each($products)) {
            $product_id[] = $productId;
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
        return "обновлено товаров - $j";
        //return $product_id;
    }

    public function basketData($basket)
    {
        $countproducts = 0;
        $countprovider = [];
        $container = new Container(StringResource::SESSION_NAMESPACE);
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray = $container->legalStoreArray;

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
                    //$countproducts +=$count ;
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
                ];
            }
            if ($whatHappened) {
                $container->whatHappened = $whatHappened;
            }
        }
        if (!$item or!count($item)){
            return;
        }
        //$return = [];
        $g = 0;
        while (list($prov, $prod) = each($item)) {
            $j++; //индекс  для управления сортировкой  магазинов по статусу доступности
            $provider = $this->providerRepository->find(['id' => $prov]);
            $store = $provider->recieveStoresInList($legalStore);
            unset($idStore, $timStoreOpen);
            $infostore1c = "";
            if (null != $store) {
                $idStore = $store->getId();
                $infostore1c .= ($legalStoresArray[$idStore]['working_hours_from']) ?
                        "сегодня с " . substr($legalStoresArray[$idStore]['working_hours_from'], 0, -3) . " до " . substr($legalStoresArray[$idStore]['working_hours_to'], 0, -3) : "";
                $infostore1c .= ($legalStoresArray[$idStore]['time_until_closing']) ? "<span class='blok mini'>заказать возможно до  " . date("Y.m.d H:i", $legalStoresArray[$idStore]['time_until_closing']) . "</span>" : "";
                $IntervalOpen = $legalStoresArray[$idStore]['time_until_open'];
                $timStoreOpen = $IntervalOpen + time();
            }
            if (null != $store) {

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
                        $provider_disable = StringResource::STORE_CLOSE_FOR_NIGHT;
                        $infostore1c = StringResource::STORE_CLOSE_FOR_NIGHT_ALT;
                        $infostore1c .= (date("d") == date("d", $timStoreOpen)) ? " сегодня " : " завтра ";
                        $infostore1c .= "в " . date("H:i", $timStoreOpen);
                    } else {
                        $returnprefix = $j + 100;
                        //закрыт на неопределеноне время
                        $provider_disable = StringResource::STORE_UNAVALBLE;
                        $infostore1c = StringResource::STORE_UNAVALBLE_ALT;
                    }
                }
            } else {
                $provider_disable = StringResource::STORE_OUT_OF_RANGE;
                $returnprefix = $j + 1000;
                $provider_address = $provider_worktime = $provider_timeclose = "";
                $infostore1c = StringResource::STORE_OUT_OF_RANGE_ALT;
                //$provider_store_off = "Комментарий из 1с ".$ifostore1c;
            }

            $return["product"][$returnprefix] = [
                "provider_id" => $provider_store_id, //$prov,
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
                "provider_store_off" => $provider_store_off,
                "products" => $prod,
                "infostore1c" => $infostore1c,
            ];
        }
        if ($countproducts) {
            $countproviders = (int) count($countprovider);
            $return["title"] = ($countproducts == 1) ? "$countproducts позиция " : ($countproducts > 1 and $countproducts < 5) ? "$countproducts позиции " : "$countproducts позиций ";
            $return["title"] .= "из ";
            $return["title"] .= ($countproviders == 1) ? "$countproducts магазина " : "$countproducts магазинов ";
        }
        $return ["countproviders"] = $countproviders;
        $return ["countprducts"] = $countproducts;
        if (is_array($return["product"]))
            ksort($return["product"]);
        return $return;
    }

}
