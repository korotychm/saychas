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
            $_id = $product->getId();  
            $_return[$_id]['id']=$_id;
            $_return[$_id]['imageurl'] = $product->getHttpUrl();
            if (!$_return[$_id]['speedlable']  and $timeDelevery ) $_return[$_id]['speedlable'] =  "<div class=speedlable>$timeDelevery" . "ч</div>"; 
             if(!$_return[$_id]['image'] and $imageurl=$product->getHttpUrl())$_return[$_id]['image'] =$imageurl;
            $_return[$_id]['brand'] = $product->getBrandTitle();
            $_return[$_id]['price'] = $cena;
            $_return[$_id]['art'] =  $product->getVendorCode() ;
            $_return[$_id]['title'] =  $product->getTitle();
            $_return[$_id]['cena'] = $cena;
            
            
            //if (!($filtrForCategory[$category_id]['hasRestOnly'] and!$r)) {
                
            //}
        }
        
        if ($_return) foreach ($_return as $Card){   
            
            
            $return['card'] .= "<div class='productcard ' >"
                    .     $Card['speedlable']
                    . "   <div class='content'>"
                    . "<a  href='/product/".$Card['id']."' >"
                    . "       <img src='/images/product/".(($Card['image'])?$Card['image']:"nophoto_1.jpeg")."' alt='alt' class='productimage'/>"
                    . "</a>"
                    . "       <strong class='blok producttitle'><a  href='/product/".$Card['id']."' >" . $Card['title'] . "</a></strong>" 
                        //. "       <span class='blok'>картинка: ". $product->getHttpUrl(). "</span>"
                        //. "       <span class='blok'>Id: " . $product->getId() . "</span>"
                    . "       <span class='blok'>Артикул: " .$Card['art']. "</span>"
                    . "       <span class='blok'>Торговая марка: " .$Card['brand']  . "</span>"
                      //  . "       <span class='blok'>Хар/List: " . $product->getParamValueList2() . "</span>"
    //                    . "       <span class='blok'>Хар/Json: " . $product->getParamVariableList2() . "</span>"
                        //. "       <span class='blok'>Остаток: " . $r . "</span>"
                        //. "       <b><span class='blok'>Магазин: " . $product->getStoreTitle() . " (id:{$product->getStoreId()})" . "</span></b>"
                        //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"                         
                    . "       <span class='blok price'>Цена: " . $Card['cena'] . " &#8381;</span>"
                    . "   </div>"
                    . "</div>";
    }
        

        $filters = (count($filters))?array_diff($filters, array('')):$filters;
        if(count($filters)) {
            $filters = array_unique($filters);
            
            $join = join(",", $filters);
        } else $join="";
        //$join=print_r($filters,true);*/
        $return['filter'] = $join;   
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
            if (!$speed or $speed<$timeDelevery ) $speed=(int)$timeDelevery;
            ($timeDelevery and $r) ? $speedlable = " / доставка <b class='speedlable2' >$speed" . "ч</b>" : $speedlable = ""; 
                $filtersTmp = explode(",", $product->getParamValueList());
                 $filters = array_merge($filters, $filtersTmp);
            
            $id=$product->getId();
            
            $title = $product->getTitle();
            $categotyId = $product->getCategoryId();                    
            //$category = $product->getCategoryTitle();                    
           
            
            $img[]=$product->getHttpUrl();
                    // ?"<img src='/images/product/{$product->getHttpUrl()}' alt='alt' class='productimage'/>":"";    
            $filtersTmp = explode(",", $product->getParamValueList2());
            $filters = array_merge($filters, $filtersTmp);
            $vendor=$product->getVendorCode() ;
            $brand=$product->getBrandTitle() ;
            //$totalRest +=$r;
            $stors[$product->getStoreId()]="{$product->getStoreTitle()}<span class='blok mini' >остаток: $r $speedlable  </span>" ;
            $rst[$product->getStoreId()]=$r;
        }
        $totalRest= array_sum($rst);
        ($speed and $totalRest) ? $speedlable2 = "<div class=speedlable>$speed" . "ч</div>" : $speedlable2 = ""; 
        
        $filters = array_diff($filters, array(''));
        if(!empty($filters)) {
            $filters = array_unique($filters);
            
            $join2 = join(",", $filters);
            if($characterictics= $this->characteristicRepository->getCharacteristicFromList($join2))
            foreach ($characterictics as $char) 
            {
                $chars.="<li><em>{$char->getTitle()}</em    >: {$char->getVal()}</li>";
            }
            $join =$chars;
        } else $join="";
        //$join=print_r($filters,true);*/
        
        $j=0;
        $img=array_unique($img);
        foreach ($img as $im){if($im) $imagesready.="<img src='/images/product/$im' alt='alt' class='product-page-image productimage$j' id='productimage$j' />"; $j++;   }
            
        
        
       // $return['filter'] = $join;   
        $return['title'] = $title;
        $return['categoryId'] = $categotyId;     
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
                        . "       <span class='blok'>Артикул: " . $vendor. "</span>"
                        . "       <span class='blok'>Торговая марка: " . $brand. "</span>"
                        . "       <span class='blok'>Остаток: " . $totalRest . "</span>"
                        . "       <b><span class='blok'>Магазины</span></b><ul><li>".join("</li><li>",$stors)."</li></ul>";
        $return['card'] .=($join)?"<b><span class='blok'>Характеристики</span></b><ul>$join</ul>":"";        
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

    public function inputUserAddressForm()
    {
        //$return.='<input id="address-input" name="suggestions '.$openir.'"  placeholder="укажи адрес доставки"/>';
        return $return;
    }

}
