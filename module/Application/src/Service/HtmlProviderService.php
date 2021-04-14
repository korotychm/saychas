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

class HtmlProviderService
{

    private $stockBalanceRepository;
    private $providerRepository;
    private $priceRepository;

    public function __construct(
            StockBalanceRepositoryInterface $stockBalanceRepository,
            ProviderRepositoryInterface $providerRepository,
            PriceRepositoryInterface $priceRepository
    )
    {
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->providerRepository = $providerRepository;
        $this->priceRepository = $priceRepository;
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
        if (count($a)):
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
        $return = ""; // new $return; 
        $filters = [];
        foreach ($filteredProducts as $product) {
            $cena = (int) $product->getPrice();
            $cena = $cena / 100;
            $cena = number_format($cena, 2, ".", "&nbsp;");
            $container = new Container(StringResource::SESSION_NAMESPACE);
            $legalStore = $container->legalStore;
            $filtrForCategory = $container->filtrForCategory;
            $timeDelevery = (int) $legalStore[$product->getStoreId()];
            $rest = $this->stockBalanceRepository->find(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            ($timeDelevery and $r) ? $speedlable = "<div class=speedlable>$timeDelevery" . "ч</div>" : $speedlable = ""; 
                
            
            if (!($filtrForCategory[$category_id]['hasRestOnly'] and!$r)) {
                $return->card .= "<div class='productcard ' >"
                        .     $speedlable
                        . "   <div class='content opacity" . $r . "'>"
                        . "       <img src='/images/product/" . (($product->getHttpUrl()) ? $product->getHttpUrl() : "nophoto_1.jpeg") . "' alt='alt' class='productimage'/>"
                        . "       <strong class='blok producttitle'><a  href=/product/{$product->getId()}   >" . $product->getTitle() . "</a></strong>" 
                        //. "       <span class='blok'>картинка: ". $product->getHttpUrl(). "</span>"
                        . "       <span class='blok'>Id: " . $product->getId() . "</span>"
                        . "       <span class='blok'>Артикул: " . $product->getVendorCode() . "</span>"
                        . "       <span class='blok'>Торговая марка: " . $product->getBrandTitle() . "</span>"
                        . "       <span class='blok'>Хар/List: " . $product->getParamValueList2() . "</span>"
                        . "       <span class='blok'>Хар/Json: " . $product->getParamVariableList2() . "</span>"
                        . "       <span class='blok'>Остаток: " . $r . "</span>"
                        . "       <b><span class='blok'>Магазин: " . $product->getStoreTitle() . " (id:{$product->getStoreId()})" . "</span></b>"
                        //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"                         
                        . "       <span class='blok price'>Цена: " . $cena . " &#8381;</span>"
                        . "   </div>"
                        . "</div>";
            }
        }

        $filters = array_diff($filters, array(''));
        if(!empty($filters)) {
            $filters = array_unique($filters);
            
            $join = join(",", $filters);
        } else $join="empty";
        //$join=print_r($filters,true);*/
        $return->filter = $join;   
        $return->categoryId = "00000001";   
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
            $rest = $this->stockBalanceRepository->find(['product_id=?' => $product->getId(), 'store_id=?' => $product->getStoreId()]);
            $r = (int) $rest->getRest();
            ($timeDelevery and $r) ? $speedlable = " / доставка <b class='speedlable2' >$timeDelevery" . "ч</b>" : $speedlable = ""; 
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
        
        
        $filters = array_diff($filters, array(''));
        if(!empty($filters)) {
            $filters = array_unique($filters);
            
            $join = join("</li><li>", $filters);
        } else $join="empty";
        //$join=print_r($filters,true);*/
        
        $j=0;
        $img=array_unique($img);
        foreach ($img as $im){if($im) $imagesready.="<img src='/images/product/$im' alt='alt' class='product-page-image productimage$j' id='productimage$j' />"; $j++;   }
            
        
        
        $return['filter'] = $join;   
        $return['title'] = $title;
        $return['categoryId'] = $categotyId;     
        //$return['categoryTitle'] =$category;
        $return['card'] .= ""
                        . "<div class='pw-contentblock cblock-2'>    
                            <div class='contentpadding'>    
                                 $imagesready 
                            </div>    
                            </div>"
                        . "
                            <div class='pw-contentblock cblock-2'>    
                            <div class='contentpadding'>
                            <div class='productpagecard ' >"     
                        //.     $speedlable
                        . "   <div class='content opacity" . $r . "'>"
                        . "       <h2 class='blok price'>Цена: " . $cena . " &#8381;</h2>"
                        . "       <span class='blok'>Артикул: " . $vendor. "</span>"
                        . "       <span class='blok'>Торговая марка: " . $brand. "</span>"
                        . "       <span class='blok'>Остаток: " . $totalRest . "</span>"
                        . "       <b><span class='blok'>Магазины</span></b><ul><li>".join("</li><li>",$stors)."</li></ul>"
                        . "       <b><span class='blok'>Характеристики</span></b><ul><li>$join</li></ul>"
                        //. "       <i class='blok'> ".$product->getStoreAddress()."</i>"                         
                        . "   </div>"
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
