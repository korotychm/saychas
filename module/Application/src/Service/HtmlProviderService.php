<?php

// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Application\Resource\StringResource;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Laminas\Db\ResultSet\HydratingResultSet;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;

class HtmlProviderService
{

    private $stockBalanceRepository;
    private $providerRepository;
    private $priceRepository;
    private $characteristicRepository;

    public function __construct(
            StockBalanceRepositoryInterface $stockBalanceRepository,
            ProviderRepositoryInterface $providerRepository,
            PriceRepositoryInterface $priceRepository,
            CharacteristicRepositoryInterface $characteristicRepository
    )
    {
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->providerRepository = $providerRepository;
        $this->priceRepository = $priceRepository;
        $this->characteristicRepository = $characteristicRepository;
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
            $return[] = "Каталог";
            $a = array_reverse($a);
            foreach ($a as $b) {
                $return[] = "<a href=/catalog/" . $b[0] . ">" . $b[1] . "</a>";
            }
            return "<div  class='bread-crumbs'><span class='bread-crumbs-item'>" . join("</span> / <span class='bread-crumbs-item'>", $return) . "</span></div>";
        endif;
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

    public function getCategoryFilterHtml($filters, $category_id)
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $filtrForCategory=$container->filtrForCategory;
        
        if(!$filtred=$filtrForCategory[$category_id]['fltr']) $filtred=[];
        foreach ($filters as $row) {
            $arrayTmp[$row->getId()]['title'] = $row->getTitle();
            $arrayTmp[$row->getId()]['id'] = $row->getId();
            $arrayTmp[$row->getId()]['count']+=(in_array($row->getValId(),$filtred)?1:0);
            $arrayTmp[$row->getId()]['options'] .= "
                     <div class='nopub checkgroup blok ".(in_array($row->getValId(),$filtred)?" zach ":"")."' for='{$row->getValId()}' >{$row->getVal()}
                               <input type='checkbox' id='fltrcheck{$row->getValId()}' name='fltr[]' class='none' value='{$row->getValId()}'  ".(in_array($row->getValId(),$filtred)?" checked ":"")." >
                     </div>";
        }
        if (!$arrayTmp)  return;
        //$filtrForCategory[$category_id];
        foreach ($arrayTmp as $row) {
        
            $return .= '
      <div class="ifilterblock"  >
            <div class="filtritemtitle" rel="' . $row['id'] . '">' . $row['title'].(($count=(int)$row['count'])?"<div class='count' >$count</div>":""). '</div>
            <div class="filtritem" id="fi' . $row['id'] . '">
                <div class="filtritemcontent" id="fc' . $row['id'] . '">
                    <div class="closefilteritem" rel="' . $row['id'] . '">' . $row['title'] . '</div>
                    ' . $row['options'] . "
                    <div class='block'><input type='button' value='применить' class='formsendbutton'  ></div>
                 </div>
            </div>
        </div>";
        }

        return $return;
    }

    public function productCard($filteredProducts, $category_id = 0)
    {
        $return = []; // new $return;
        $filters = [];
        foreach ($filteredProducts as $product) {
            $cena = (int) $product->getPrice();
            $cena = $cena / 100;
            $cena = number_format($cena, 2, ".", "&nbsp;");
            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;
            $filtrForCategory = $container->filtrForCategory;
            $timeDelevery = (int) $legalStore[$product->getStoreId()];
            $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            if (!(!$r and $filtrForCategory[$category_id]['hasRestOnly']))
            {
                $filtersTmp = explode(",", $product->getParamValueList2());
                $filters = array_merge($filters, $filtersTmp);
            }    
            $_id = $product->getId();
            $_return[$_id]['rest'] += $r;
            $_return[$_id]['id'] = $_id;
            $_return[$_id]['imageurl'] = $product->getHttpUrl();
            if (!$_return[$_id]['speedlable'] and $timeDelevery)
                $_return[$_id]['speedlable'] = "<div class=speedlable>$timeDelevery" . "ч</div>";
            if (!$_return[$_id]['image'] and $imageurl = $product->getHttpUrl())
                $_return[$_id]['image'] = $imageurl;
            $_return[$_id]['brand'] = $product->getBrandTitle();
            $_return[$_id]['price'] = $cena;
            $_return[$_id]['art'] = $product->getVendorCode();
            $_return[$_id]['title'] = $product->getTitle();
            $_return[$_id]['cena'] = $cena;
            $prices[]=$cena;

        }

        if ($_return) {
            foreach ($_return as $Card) {
                if (!($filtrForCategory[$category_id]['hasRestOnly'] and!$Card['rest']) and!empty($Card)) {

                    $return['card'] .= "<div class='productcard ' >"
                            . $Card['speedlable']
                            . "   <div class='content opacity" . $Card['rest'] . "'>"
                            . "<a  href='/product/" . $Card['id'] . "' >"
                            //. "       <img src='/images/product/"' alt='alt' class='productimage'/>"
                            . "      <div class='zeroblok'><img src='/img/zero.png' alt='alt' class='productimage zero' style='background-image:url(/images/product/" . (($Card['image']) ? $Card['image'] : "nophoto_1.jpeg") . ")'/></div>"
                            . "</a>"
                            . "       <strong class='blok producttitle'><a  href='/product/" . $Card['id'] . "' >" . $Card['title'] . "</a></strong>"
                            //. "       <span class='blok'>картинка: ". $product->getHttpUrl(). "</span>"
                            //. "       <span class='blok'>Id: " . $product->getId() . "</span>"
                            . "       <span class='blok'>Артикул: " . $Card['art'] . "</span>"
                            . "       <span class='blok'>Торговая марка: " . $Card['brand'] . "</span>"
                            //  . "       <span class='blok'>Хар/List: " . $product->getParamValueList2() . "</span>"
                            //                    . "       <span class='blok'>Хар/Json: " . $product->getParamVariableList2() . "</span>"
                            //                        . "       <span class='blok'>Остаток: " . $Card['rest'] . "</span>"
                            //. "       <b><span class='blok'>Магазин: " . $product->getStoreTitle() . " (id:{$product->getStoreId()})" . "</span></b>"
                            //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"
                            . "       <span class='blok price'>Цена: " . $Card['cena'] . " &#8381;</span>"
                            . "   </div>"
                            . "</div>";
                }
            }
        }
        $filters = (count($filters)) ? array_diff($filters, array('')) : $filters;
        if (count($filters)) {
            $filters = array_unique($filters);
         }
        $return['filter'] = $filters;
        $return['categoryId'] = "00000001";
        return $return;
    }

    public function productPage($filteredProducts, $category_id = 0)
    {

        $return = []; // new $return;
        $filters = [];
        //exit (print_r($filteredProducts));
        foreach ($filteredProducts as $product) {

            //exit (print_r($product));
            $cena = (int) $product->getPrice();
            $cena = $cena / 100;
            $cena = number_format($cena, 2, ".", "&nbsp;");

            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;

            $filtrForCategory = $container->filtrForCategory;
            $timeDelevery = (int) $legalStore[$product->getStoreId()];
            $rest = $this->stockBalanceRepository->findFirstOrDefault(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            if (!$speed or $speed < $timeDelevery)
                $speed = (int) $timeDelevery;
            ($timeDelevery and $r) ? $speedlable = " / доставка <b class='speedlable2' >$speed" . "ч</b>" : $speedlable = "";
            $filtersTmp = explode(",", $product->getParamValueList());
            $filters = array_merge($filters, $filtersTmp);

            $id = $product->getId();

            $title = $product->getTitle();
            $categoryId = $product->getCategoryId();
            //$category = $product->getCategoryTitle();


            $img[] = $product->getHttpUrl();
            // ?"<img src='/images/product/{$product->getHttpUrl()}' alt='alt' class='productimage'/>":"";
            $filtersTmp = explode(",", $product->getParamValueList2());
            $filters = array_merge($filters, $filtersTmp);
            $vendor = $product->getVendorCode();
            $brand = $product->getBrandTitle();
            //$totalRest +=$r;
            $stors[$product->getStoreId()] = "{$product->getStoreTitle()}<span class='blok mini' >остаток: $r $speedlable  </span>";
            $rst[$product->getStoreId()] = $r;
        }
        $totalRest = array_sum($rst);
        ($speed and $totalRest) ? $speedlable2 = "<div class=speedlable>$speed" . "ч</div>" : $speedlable2 = "";

        $filters = array_diff($filters, array(''));
        if (!empty($filters)) {
            $filters = array_unique($filters);

            $join2 = join(",", $filters);
            if ($characterictics = $this->characteristicRepository->getCharacteristicFromList($join2))
                foreach ($characterictics as $char) {
                    $chars .= "<li><em>{$char->getTitle()}</em    >: {$char->getVal()}</li>";
                }
            $join = $chars;
        } else
            $join = "";
        //$join=print_r($filters,true);*/

        $j = 0;
        $img = array_unique($img);
        foreach ($img as $im) {
            if ($im)
              $imagesready .= ((!$j)?"<div class='square'><div class='squarecontent'>":"<div class='product-image-container-mini iblok' >")."<img src='/images/product/$im' alt='alt' class='product-page-image productimage$j' id='productimage$j' />".((!$j)?"</div></div>":"</div>");
            $j++;
        }



        // $return['filter'] = $join;
        $return['title'] = $title;
        $return['categoryId'] = $categoryId;
        //$return['categoryTitle'] =$category;
        $return['card'] .= ""
                . "<div class='pw-contentblock cblock-2'>
                            <div class='contentpadding'>
                           
                                            $speedlable2
                                            $imagesready
                                   
                            </div>
                            </div>"
                . "
                            <div class='pw-contentblock cblock-2'>
                            <div class='contentpadding'>
                            <div class='productpagecard ' >"
                . "   <div class='content opacity" . $r . "'>"
                . "       <h2 class='blok price'>   " . $cena . " &#8381;</h2>"
                . "       <span class='blok'>Артикул: " . $vendor . "</span>"
                . "       <span class='blok'>Торговая марка: " . $brand . "</span>"
                . "       <span class='blok'>Остаток: " . $totalRest . "</span>"
                . "       <b><span class='blok'>Магазины</span></b><ul><li>" . join("</li><li>", $stors) . "</li></ul>";
        $return['card'] .= ($join) ? "<b><span class='blok'>Характеристики</span></b><ul>$join</ul>" : "";
        //. "       <b><span class='blok'>Характеристики</span></b><ul>$join <hr><div class=mini>".str_replace(",","<br>",$join2)." </div></ul>"
        //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"
        $return['card'] .= "   </div>"
                . "</div>"
                . "</div>"
                . "</div>    ";

        // exit(print_r($return));

        return $return;
    }

    public function writeUserAddress()
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $userAddress = $container->userAddress;
        ($userAddress) ?: $userAddress = "Укажи адрес и получи заказ за час!";
        return "<span>$userAddress</span   > ";
    }

}
