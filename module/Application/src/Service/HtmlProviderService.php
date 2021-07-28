<?php

// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
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
     * Returns Html string
     * @return string
     */
    public function testHtml()
    {
        return '<h1>Hello world!!!</h1>';
    }

    /**
     * Returns Html string
     * @return string
     */
    public function breadCrumbs($a = [])
    {
        if ($a and count($a)):
            //<span class='bread-crumbs-item'></span>"
            $return[] = "<a href=# class='catalogshow'>Каталог</a>";
            $a = array_reverse($a);
            foreach ($a as $b) {
                $return[] = "<a href=/catalog/" . $b[0] . ">" . $b[1] . "</a>";
            }
            //return "<div  class='bread-crumbs'><span class='bread-crumbs-item'>" . join("</span> / <span class='bread-crumbs-item'>", $return) . "</span></div>";
            return join('<b class="brandcolor"> : </b>', $return);
        endif;
    }

    public function breadCrumbsMenu($a = [])
    {
        if ($a and count($a)) {
            //<span class='bread-crumbs-item'></span>"
            //$return[] = "<a href=# class='catalogshow'>Каталог</a>";
            $a = array_reverse($a);
            $j = 0;
            foreach ($a as $b) {

                $return[] = "<span class='bread-crumbs-item breadtab$j'><a href=/catalog/" . $b[0] . ">" . $b[1] . "</a></span>";
                if ($j < 4)
                    $j++;
            }
            return "<div  class='bread-crumbs'>" . join("", $return) . "</div>";
            // return join('<b class="brandcolor"> : </b>', $return);
        }
    }

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
        <script>
            $(function(){
                $("#rangeslider").ionRangeSlider({
                    hide_min_max: true,
                    keyboard: true,
                    min: ' . (int)$price['minprice'] . ',
                    max: ' . (int)$price['maxprice'] . ',
             //        from:' . (int)$pricesel['minprice'] . ',
             //       to:  ' . (int)$pricesel['maxprice'] . ',
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
        " . $return . "
            <!-- div class=blok >
            <div class='fltrblock'>
                   <div class='filtritemtitle blokl' >Тест булевое значение</div>
                    <div class=blok >
                        <div class=' fltronoff  onoff ' for=123  rel=1 >Нет 
                            <input type='checkbox' rel=1 class='none  relcheck fltrcheck123' name='characteristics[" . $row['id'] . "][]' value='0' >
                        </div>
                        <div class='fltronoff onoff zach ' for=122  rel=1 >Да
                                <input type='checkbox' rel=1 class='none  relcheck  fltrcheck122' name='characteristics[" . $row['id'] . "][]' value='1' checked >
                        </div>
                    </div>
                </div>
             </div>
             <div class=blok >
            <div class='fltrblock'>
                   <div class='filtritemtitle blokl' >Тест радиокнопки</div>
                    <div class=blok >
                        <div class='   radio ' for=1232  rel=1 >Нет 
                            <input type='checkbox' rel=1 class='none  relcheck fltrcheck1232' name='characteristics[test][]' value='0' >
                        </div>
                        <div class=' radio zach ' for=1222  rel=1 >Да
                                <input type='checkbox' rel=1 class='none  relcheck  fltrcheck1222' name='characteristics[test][]' value='1' checked >
                        </div>
                        <div class=' radio ' for=12222  rel=1 >По-барабану
                                <input type='checkbox' rel=1 class='none  relcheck  fltrcheck12222' name='characteristics[test][]' value='3'  >
                        </div>
                    </div>
                </div>
             </div-->" : "";
        //
        return $return;
    }

    public function productCard($filteredProducts, $category_id = 0)
    {
        $return = []; // new $return;
        $filters = [];

        foreach ($filteredProducts as $product) {
            $cena = $price = (int) $product->getPrice();
            $cena = $cena / 100;
            $cena = number_format($cena, 0, "", "&nbsp;");

            $oldprice = (int) $product->getOldPrice();
            if ($oldprice > $price) {
                $oldprice = $oldprice / 100;
                $oldprice = number_format($oldprice, 0, "", "&nbsp;");
            } else
                $oldprice = 0; /**/

            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;
            $filtrForCategory = $container->filtrForCategory;
            $timeDelevery = (int) $legalStore[$product->getStoreId()];
            $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            $_id = $product->getId();
            $_return[$_id]['rest'] += $r;
            $_return[$_id]['id'] = $_id;
            $_return[$_id]['imageurl'] = $product->getHttpUrl(); //            $_return[$_id]['imageurl'] = $product->getProductImage()->getHttpUrl();

            if (!$_return[$_id]['speedlable'] and $timeDelevery)
                $_return[$_id]['speedlable'] = "<div class=speedlable>$timeDelevery" . "ч</div>";
            if (!$_return[$_id]['image'] and $imageurl = $product->getHttpUrl())
                $_return[$_id]['image'] = $imageurl;
            $_return[$_id]['brand'] = $product->getBrandTitle();
            $_return[$_id]['price'] = $cena;
            $_return[$_id]['oldprice'] = $oldprice;
            $_return[$_id]['art'] = $product->getVendorCode();
            $_return[$_id]['title'] = $product->getTitle();
            $_return[$_id]['cena'] = $cena;
            $prices[] = $cena;
        }

        if ($_return) {
            foreach ($_return as $Card) {
                if (!($filtrForCategory[$category_id]['hasRestOnly'] and!$Card['rest']) and!empty($Card)) {

                    $return['card']
                            //.=$this->partial('application/ajax/partials/card', ['card' => $card]); .= "<div class='productcard ' >"
                            // . $Card['speedlable']
                            . "<div class='contentabsolute  content opacity" . $Card['rest'] . "'>"
                            . "<a  href='/product/" . $Card['id'] . "' >"
                            . "      <div class='zeroblok'><img src='/img/zero.png' alt='alt' class='productimage zero' style='background-image:url(/images/product/" . (($Card['image']) ? $Card['image'] : "nophoto_1.jpeg") . ")'/></div>"
                            . "</a>"
                            . "       <strong class='blok producttitle'><a  href='/product/" . $Card['id'] . "' >" . $Card['title'] . "</a></strong>"
                            . "         <div class='inactiveblok'></div>"
                            . "       <span class='price'>" . $Card['cena'] . "&#8381;</span>"
                            . (($Card['oldprice']) ? "       <span class='oldprice'>" . $Card['oldprice'] . "&#8381;</span>" : "")
                            . "        <div class='payblockcard'>"
                            . "             <div class='paybutton' rel='" . $Card['id'] . "' >в корзину</div>"
                            . "        </div>"
                            . "   </div>"
                            . "   <div class='content opacity" . $Card['rest'] . "'>"
                            . "<a  href='/product/" . $Card['id'] . "' >"
                            //. "       <img src='/images/product/"' alt='alt' class='productimage'/>"
                            . "      <div class='zeroblok'><img src='/img/zero.png' alt='alt' class='productimage zero' style='background-image:url(/images/product/" . (($Card['image']) ? $Card['image'] : "nophoto_1.jpeg") . ")'/></div>"
                            . "</a>"
                            . "       <strong class='blok producttitle'><a  href='/product/" . $Card['id'] . "' >" . $Card['title'] . "</a></strong>"
                            . "         <div class='inactiveblok'></div>" . "       <span class='price'>" . $Card['cena'] . "&#8381;</span>"
                            . (($Card['oldprice']) ? "       <span class='oldprice'>" . $Card['oldprice'] . "&#8381;</span>" : "")
                            . "   </div>"
                            . "</div>"; /**/
                }
            }
        }
        return $return;
    }

    private function valueParce($v = [], $chType)
    {
        $bool = ["нет", "да"];
        if (!$v or!is_array($v))
            return $v;
        foreach ($v as $val) {
            if (!$val)
                continue;
            //if ($chArray and is_array($value)) $value=join(", ",$value);
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
            return print_r(join(", ", $value), true);
    }

    public function productPage($filteredProducts, $category_id = 0)
    {
        $return = $filters = [];
        if (!$filteredProducts->count()) {
            header("HTTP/1.1 301 Moved Permanently");
            header("Location:/");
            exit();
        }
        foreach ($filteredProducts as $product) {
            $cena = $price = (int) $product->getPrice();
            $cena = $cena / 100;
            $cena = number_format($cena, 0, "", "&nbsp;");

            $oldprice = (int) $product->getOldPrice();
            if ($oldprice > $price) {
                $oldprice = $oldprice / 100;
                $oldprice = number_format($oldprice, 0, "", "&nbsp;");
            } else
                $oldprice = 0; /**/

            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;

            $filtrForCategory = $container->filtrForCategory;
            $timeDelevery = (int) $legalStore[$product->getStoreId()];
            $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            if (!$speed or $speed < $timeDelevery) {
                $speed = (int) $timeDelevery;
            }
            ($timeDelevery and $r) ? $speedlable = " / доставка <b class='speedlable2' >$speed" . "ч</b>" : $speedlable = "";
            //$filtersTmp = explode(",", $product->getParamValueList());
            //$filters = array_merge($filters, $filtersTmp);
            $id = $product->getId();
            $title = $product->getTitle();
            $categoryId = $product->getCategoryId();
            $charNew = $product->getParamVariableList();
            $charNew = json_decode($charNew, true);
            $charsNew = "<pre>" . print_r($charNew, true) . "</pre>";

            //$category = $product->getCategoryTitle();

            $img[] = $product->getHttpUrl();
            //$filtersTmp = explode(",", $product->getParamValueList2());
            //$filters = array_merge($filters, $filtersTmp);
            $vendor = $product->getVendorCode();
            $brandtitle = $product->getBrandTitle();

            $brandid = $product->getBrandId();
            //exit($brandid."!");
            $brandobject = $this->brandRepository->findFirstOrDefault(['id' => $brandid]);
            $brandimage = $brandobject->getImage();
            //exit($brandimage."!");
            $description = $product->getDescription();

            $description = (strlen($description) < 501) ? "<p>" . str_replace("\n", "</p><p>", $description) . "</p>" : ""
                    . "<div  id='spoiler-hide-$id' >"
                    . "     <p><div class='blok relative'>"
                    . "         " . substr(strip_tags($description), 0, 500)
                    . "         <div class='gradientbottom'></div>"
                    . "     </div>"
                    . "     <a href=# class='redlink spoileropenlink ' rel='$id'  >развернуть описание&darr;</a>"
                    . "     </p>"
                    . "</div>"
                    . "<div  id='spoiler-show-$id'   class='blok' style='display:none' ><p>" . str_replace("\n", "</p><p>", $description) . "</p></div>";

            $stors[$product->getStoreId()] = "{$product->getStoreTitle()}<span class='blok mini' >остаток: $r $speedlable  </span>";
            $rst[$product->getStoreId()] = $r;
        }
        $totalRest = (count($rst)) ? array_sum($rst) : 0;
        ($speed and $totalRest) ? $speedlable2 = "<div class=speedlable>$speed" . "ч</div>" : $speedlable2 = "";

        $charNew = array_diff($charNew, array(''));
        if (!empty($charNew)) {
            if ($characterictics = $charNew)
                $j = 0;

            foreach ($characterictics as $char) {
                $charRow = "";
                $ch = $this->characteristicRepository->findFirstOrDefault(['id' => $char['id'] . "-" . $categoryId]);
                $chTit = $ch->getTitle();
                $chType = (int) $ch->getType();
                $chArray = $ch->getIsList();
                $chMain = $ch->getIsMain();
                $idchar = $char['id'];
                if ($value = $char['value'] /* or true /* */) {

                    ($chArray) ? $v = $value : $v[] = $value;
                    $value = $this->valueParce($v, $chType);
                    unset($v);
                    $charRow = "<div class='char-row'><span class='char-title'><span>" . $chTit . " "
                            . "" . $ch->getUnit() . ""
                            . "</span></span><span class=char-value ><span>$value</span></span></div>";
                    $j++;
                } elseif ($chType == 0) {
                    $charRow = "<h3>$chTit</h3>";
                    $j++;
                }
                if ($chMain)
                    $chars .= $charRow;
                $charsmore .= $charRow;
            }

            $join = $chars;
        } else
            $join = "";
        $j = 0;
        $img = array_unique($img);
        foreach ($img as $im) {
            if ($im) {
                $borderred = "";
                $image = "<img src='/images/product/$im' alt='alt' class='product-page-image productimage$j' id='productimage$j' title='img$j' />";
                if (!$j) {
                    $mainimage = "<div class='square'><div class='squarecontent'>$image</div></div>";
                    $borderred = " borderred ";
                }
                $j++;
                $image = "<img src='/images/product/$im' alt='alt' class='product-page-image productimage$j' id='productimage$j' title='img$j' />";
                $imgicons .= "<div class='product-image-container-mini iblok $borderred' >$image</div>";
            }
        }
        $return['title'] = $title;
        $return['categoryId'] = $categoryId;
        $return['card'] .= ""
                . "<div class='pw-contentblock cblock-2'>"
                . "         <div class='inactiveblok'></div>"
                . "</div>"
                . "<div class='pw-contentblock gray iblokr cblock-2'>Код товара: $vendor</div>"
                . "<div class='pw-contentblock cblock-3'>
                    <div class='contentpadding' id=productpageimg>
                            $speedlable2
                            $imagesready
                    <div class='iblok iconimg' style='width:98px;' >$imgicons</div>
                    <div class='iblok mainimg'  style='width:calc(100% - 110px) ' >$mainimage</div>   
                     </div>
                 </div>"
                . "
                 <div class='pw-contentblock cblock-3'>
                    <div class='contentpadding'>
                      <div class='productpagecard ' >"
                . "   <div class='content opacity-" . $r . "'>"
        ;

        $return['card'] .= ($join) ? "<div class='char-blok'>$join</div>" : "";
        //. "       <b><span class='blok'>Характеристики</span></b><ul>$join <hr><div class=mini>".str_replace(",","<br>",$join2)." </div></ul>"
        //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"
        $return['card'] .= "   </div>"
                . "</div>"
                . "</div>"
                . "</div>    "
                . "<div class='pw-contentblock cblock-3'>
                         <div class='contentpadding'>
				<div class='paybox' >"
                . "     		<div class='contentpadding'>
						<h2 class='blok price'>   " . $cena . " &#8381; "
                . (($oldprice) ? "<span class='oldprice'>" . ($oldprice) . "&nbsp;&#8381;</span>" : "")
                . "</h2>
					</div>
        				<div class='volna' ></div>
            				<div class='contentpadding'>
						доставка
                                        </div>
                                        <div class='pw-contentblock cblock-2'>
                                            <div class='contentpadding'>
                                                 <div class=paybutton rel='$id' >в корзину</div>
                                            </div>
                                         </div>   
                                         <div class='pw-contentblock cblock-2'>
                                            <div class='contentpadding'>
                                                 <div class=paybuttonwhite rel='$id' >купить сразу</div>
                                            </div>
                                         </div>   
									"
                . "         <div class='contentpadding'>
                                <div class='favstar favtext'>Добавить в избранное</div>
                            </div>        
                      </div>
                       <div class=brandblok >
                               " . (($brandimage) ? "<div class='brandlogo' style='background-image:url(\"/images/brand/$brandimage\")'></div>" : " <div class='brandlogo' >$brandtitle</div>") . "
                               <a class='brandlink' href=# >Все товары марки&nbsp;&rarr;</a>
                         </div>
                    </div>
                 </div>
                 "
        ;
        $return['card'] .= ""
                . "<div class=blok  >"
                . "    <div class='pw-contentblock cblock-5' >
                            <div class='contentpadding ' >"
                //. $charsNew."!!!"
                . $description
                . (($charsmore) ? "<h3>Характеристики</h3>
                    
                                <div class='char-blok-bottom'>
                                    $charsmore
                                </div>" : "") . "
                            </div>
                        </div>"
                . "<div class='pw-contentblock cblock-3' >"
                . "<div class='contentpadding ' >"
                . "<div class='mini opacity0'>
                            <UL><span class='blok'>Артикул: " . $vendor . "</span>"
                //. "       <span class='blok'>Торговая марка: " . $brand . "</span>"
                . "               <span class='blok'>Остаток: " . $totalRest . "</span>"
                . "           <b><span class='blok'>Магазины</span></b><li>" . join("</li><li>", $stors) . "</li>
                            </ul>
                      </div>
                      </div>
                      </div>
                      </div>"
                . "</div>";

        return $return;
    }

    public function writeUserAddress($user = null)
    {
        //$userId = $this->identity();
        /* $userData = new UserData();
          $userData->getUserId($userId)
          ->getAddress()
          ->getGeodata(); */
        $container = new Container(StringResource::SESSION_NAMESPACE);

        $username = $user->getName();
        $userData = $user->getUserData();

        $usdat = $userData->current();
        if (null != $usdat) {
            $userAddress = $usdat->getAddress(); //$container->userAddress;
            $userGeodata = $usdat->getGeoData();
            //exit ($userGeodata);
        }
        ($userAddress) ?: $userAddress = "Укажи адрес и получи заказ за час!";
        return "<span>$userAddress</span>"
                . "<textarea id='geodatadadata' class='none' >$userGeodata</textarea>"
        //. "<h1></h1>"
        //
        //. "<input type=hidden22 id='geodatadadata22' class='none22' value=\"".($userGeodata2)?$userGeodata:"{2222}"."\" />"
        ;
    }
    
    public function getUserInfo($user)
    {
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
        /*   $param = [
           "hourPrice"=> 333,
           "mergePrice"=> 50,
         
           "mergePriceFirst"=> 150,
        ];*/
        
        $return["post"] = "<pre>" . print_r($post, true) . "</pre>";
        $products = $post->products;
        $storeAdress=[];
        if ($selfdelevery = $post->selfdelevery and $countSelfdelevery = count($selfdelevery)) {
            foreach ($selfdelevery as $providerinfo) {
                //$provider = $this->storeReposi
                $stores = Store::findAll(['where' =>['provider_id' => $providerinfo]]);
                $store = $stores->current();
                //$address = explode(",", $store->getAddress());/**/
                
                $storeAdress[] = StringHelper::cutAddress( $store->getAddress());/**/
                //$storeAdress[]= print_r($stores, true);
                //$storeAdress[] = $providerinfo;
            }
        }


        $j = 0;
        if (!empty($products))
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
        if ($provider)
            $countDelevery = count($provider);
        $countDelevery = (int) $countDelevery - $countSelfdelevery;
        if (!$post->ordermerge){
            $priceDelevery =  $countDelevery * $param['hourPrice'];                   
        }
        else {
            
            $timeDelevery=(!$post->ordermerge)?$post->timepointtext1:$post->timepointtext3;
            
            $priceDelevery = $countDelevery*$param['mergePrice'] +  ceil($countDelevery/$param['mergecount'])* $param['mergePriceFirst'];
            $countDelevery = ceil($countDelevery/$param['mergecount']);        
            
        }

        
        //$return['payEnable'] =($total>0 and  ($countSelfdelevery or ($countDelevery and $timeDelevery)))?true:false; 
        $return["textDelevery"] = "за час";
        $return["basketpricetotalall"] = $return["total"] = $total;
        $return["count"] = $j;
        $return["timeDelevery"] = $timeDelevery;
        $return["countSelfdelevery"] = $countSelfdelevery;
        $return["priceDelevery"] = $priceDelevery;
        $return["countDelevery"] = $countDelevery ;
        $return["countDeleveryText"] = $countDelevery ;
        $return["countDeleveryText"] .= ($countDelevery < 2 )?" доставка ":(($countDelevery > 1 and $countDelevery < 5)?" доставки":" доставок ");
        $return["storeAdress"] = $storeAdress ;
        //$return["paycardinfo"] = "4276 5555 **** <span class='red'>1234&darr;</span>";

        return $return;
    }

    public function basketMergeData($post, $param)
    {
        /*$param = [
           "hourPrice" => 29900,  //цена доставки за час
           "mergePrice" => 5000, //цена доставки за три часа
           "mergePriceFirst" => 24900,  //цена доставки за первый махгазин  при объеденении заказа
           "mergecount" => 4, //количество объеденямых магазинов
        ];*/
        $return = [];
        $products = $post->products;
        if(!$selfdelevery = $post->selfdelevery) $countSelfdelevery = 0;  else $countSelfdelevery = count($selfdelevery);
        //return ['count' => print_r($products , true)];  
        $container = new Container(StringResource::SESSION_NAMESPACE);
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray = (!empty($container->legalStoreArray))?$container->legalStoreArray:[]; 
        
        if( $products and !empty($products)){
            $products = array_keys($products);
            foreach($products as $pId){
                //$return["count"] = print_r($pId, true);         break;
                $product = $this->productRepository->find(['id' => $pId]);
                //$return["count"] = print_r($product, true);
                
                $providerId = $product->getProviderId();
                $provider = $this->providerRepository->find(['id' => $providerId]);
                
                $store = $provider->recieveStoresInList($legalStore);
                $idStore = $store->getId();
                //
                //
                //$return["count"] = print_r($idStore, true);         break;
                $timeClose[$idStore] =$legalStoresArray[$idStore]['time_until_closing']; 
                //$legalStoresArray[$idStore]['time_until_closing'];
                
            }
            $return['timeClose'] = min($timeClose);
            //<option value="0" rel=" в течение часа ">сейчас за час</option>
            $timeDelevery1Hour[]=[
                    "lable" => StringResource::BASKET_SAYCHAS_title,
                    "value" => 0,
                    "rel" => StringResource::BASKET_SAYCHAS_do ,
                    ];
            /**/$timeDelevery3Hour[]=[
                    "lable" => StringResource::BASKET_SAYCHAS3_title,
                    "value" => 0,
                    "rel" => StringResource::BASKET_SAYCHAS3_do ,
                    ];/**/
                    
            for ($i = 1; $i <= 12; $i++) {
                $timeStart = time() + 3600 * $i; 
                $timeEnd = time() + 3600 * $i + 3600; 
                $time3End = time() + 3600 * $i + 3600 * 3; 
                if($timeEnd > $return['timeClose']) break;
                
                $value  = "c ".date("H", $timeStart).":00"." до ".date("H", $timeEnd).":00";
                $value3 = "c ".date("H", $timeStart).":00"." до ".date("H", $time3End).":00";
                
                $rel = (date("d", $timeStart) == date("d"))?" сегодня ":" завтра ";
                
                
                $timeDelevery1Hour[]=[
                    "lable" => $value,
                    "value" => date("H", $timeStart),
                    "rel" => $rel.$value,
                    ];
                
                if($time3End < $return['timeClose']) {
                    $timeDelevery3Hour[]=[
                    "lable" => $value3,
                    "value" => date("H", $timeStart),
                    "rel" => $rel.$value3,
                    ];
                }
            }
             //$return["count"] = min($timeClose);
            $return["count"] = count($timeClose)-$countSelfdelevery;
            $return["select1hour"] = $timeDelevery1Hour;
            $return["select3hour"] = $timeDelevery3Hour;
            $return["hourPrice"] = $return["count"]*$param["hourPrice"];
            $return["hour3Price"] = $return["count"]*$param['mergePrice'] +  ceil($return["count"]/$param['mergecount'])* $param['mergePriceFirst'];
            
            
            
        }
        return $return;
    }
    
    
    public function basketData($basket)
    {
        $countproducts = 0;
        $countprovider=[];
        $container = new Container(StringResource::SESSION_NAMESPACE);
        
        foreach ($basket as $b) {
            if ($pId = $b->productId) {
                /** @var HandbookRelatedProduct */
                $product = $this->productRepository->find(['id' => $pId]);
                
                $oldprice = $b->price;
                $price = (int) $product->receivePriceObject()->getPrice();
                if ($oldprice != $price ){
                   $whatHappened[$pId]['oldprice']=$oldprice; 
                   $whatHappened[$pId]['price'] = $price; 
                }
                
                if ($product->receiveRest())
                    $availblechek[$product->getProviderId()] = true;
                $rest = $product->receiveRest();
                
                $count = $b->total;
                if ($count > $rest)
                   $whatHappened[$pId]['oldrest']=$count; 
                   $whatHappened[$pId]['rest'] = $rest; 
                    
                    $count = $rest;
                //if ($rest) $countproducts +=$count ;
                if ($rest) {
                    $countproducts ++; 
                    //$countproducts +=$count ;
                    $countprovider[$product->getProviderId()]=1;
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
                    
                ];
            }
            if ($whatHappened)  $container->whatHappened = $whatHappened;
        }
        if (!$item or!count($item))
            return ;

        
        if (empty($container->legalStore)) {
            $container->legalStore = [];
        }
        $legalStore = array_keys($container->legalStore);
        $legalStoresArray =  $container->legalStoreArray; 
        //$return = [];
        $g = 0;
        while (list($prov, $prod) = each($item)) {
            $j++;
            $provider = $this->providerRepository->find(['id' => $prov]);
            $store = $provider->recieveStoresInList($legalStore);
            //exit( print_r($store));
            unset($idStore, $timStoreOpen );
            $infostore1c="";
            if (null != $store){
                $idStore = $store->getId();
                $infostore1c .=($legalStoresArray[$idStore]['working_hours_from'])?
                        "сегодня с ".substr($legalStoresArray[$idStore]['working_hours_from'],0,-3)." до ".substr($legalStoresArray[$idStore]['working_hours_to'],0,-3)
                        :"";
                $infostore1c .=($legalStoresArray[$idStore]['time_until_closing'])
                        ?"<span class='blok mini'>заказать возможно до  ".date("Y.m.d H:i",$legalStoresArray[$idStore]['time_until_closing'])."</span>"
                        :"";
                
                $timStoreOpen=$legalStoresArray[$idStore]['time_until_open']+time();
                //$timStoreOpen = time()+60*60*1;
            }
            if (null != $store){
                 if($legalStoresArray[$idStore]['status'] ) {
                    $provider_disable = false;
                    $returnprefix = $j * -1;
                    $provider_address = $store->getAddress().$ifostore1c;
                    $provider_address .= ($store->getDescription())?", ".$store->getDescription():"";
                    $provider_store = $store->getTitle();
                    $provider_addressappend = StringHelper::cutAddress($provider_address);
                    $countprovider ++;
                 }
                 else {
                    $returnprefix = $j;
                    if( $timStoreOpen > time() and $legalStoresArray[$idStore]['time_until_open']  < 60*60*24 ){
                        $provider_disable = StringResource::STORE_CLOSE_FOR_NIGHT;
                        $infostore1c=StringResource::STORE_CLOSE_FOR_NIGHT_ALT;
                        $infostore1c.=(date("d") == date("d", $timStoreOpen))?" сегодня ":" завтра ";
                        $infostore1c.="в ".date("H:i", $timStoreOpen);
                    }
                    else {
                        $provider_disable = StringResource::STORE_UNAVALBLE;
                        $infostore1c=StringResource::STORE_UNAVALBLE_ALT;
                    }
                     
                 }
            } 
            else {
                $provider_disable = StringResource::STORE_OUT_OF_RANGE;
                $returnprefix = $j+100;
                $provider_address = $provider_worktime = $provider_timeclose = "";
                $infostore1c = StringResource::STORE_OUT_OF_RANGE_ALT;
                //$provider_store_off = "Комментарий из 1с ".$ifostore1c;
            }

            $return["product"][$returnprefix] = [
                "provider_id" => $prov,
                "availblechek" => $availblechek[$prov],
                "provider_disable" => $provider_disable,
                "provider_name" => $provider->getTitle(),
                "provider_logo" => $provider->getImage(),
                "provider_address" => $provider_address,
                "provider_addressappend" => $provider_addressappend,
                "provider_worktime" => $provider_worktime,
                "provider_timeclose" => "",
                "provider_store" => $provider_store,
                "provider_store_off" => $provider_store_off,
                "products" => $prod,
                "infostore1c" => $infostore1c,
            ];
        }
        if ($countproducts){
            $countproviders = (int)count($countprovider);
            $return["title"]  = ($countproducts == 1)?"$countproducts позиция ":($countproducts >1 and $countproducts < 5)?"$countproducts позиции ":"$countproducts позиций ";
            $return["title"] .= "из ";
            $return["title"] .=($countproviders == 1)?"$countproducts магазина ": "$countproducts магазинов ";
        }
        $return ["countproviders"] = $countproviders;
        $return ["countprducts"] = $countproducts;
        if(is_array($return["product"]))     ksort($return["product"]);
        return $return;
    }

}
