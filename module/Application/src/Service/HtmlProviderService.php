<?php
// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Application\Resource\StringResource;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Laminas\Db\ResultSet\HydratingResultSet;

class HtmlProviderService
{
    private $stockBalanceRepository;
    
    public function __construct(StockBalanceRepositoryInterface $stockBalanceRepository) {
        $this->stockBalanceRepository = $stockBalanceRepository;
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
    public function breadCrumbs($a=[])
    {
        if (count($a)):
            //<span class='bread-crumbs-item'></span>"
            $return[]="Каталог";
            $a=array_reverse($a);
            foreach ($a as $b){
                $return[]="<a href=/catalog/".$b[0].">".$b[1]."</a>";            
            }
           return   "<div  class='bread-crumbs'><span class='bread-crumbs-item'>".join("</span> / <span class='bread-crumbs-item'>",$return)."</span></div>";
        endif;
    }
    
    /**
     * Returns Html string
     * @return string
     */
    public function productCard($filteredProducts)
    {
        
  
        foreach ($filteredProducts as $product){
              /*$productCardParam = [
                    'price'=>$product->getPrice(),
                    'title'=>$product->getTitle(),
                    'img'=>$product->getUrlHttp()   ,
                    'id'=>$product->getId(),
                        'rest'=>$product->getRest(),
                    'articul'=>$product->getVendorCode(),
                    'brand'=>$product->getBrandTitle(),
                    'description'=>$product->getDescription(),
                    'param_value'=>$product->getParamValueList(),
                    'param_value'=>$product->getParamValueList(),
                    'store'=>$product->getStoreTitle()." (id:{$product->getStoreId()})",
                    'store_id'=>$product->getStoreId(),
                    //'store_address'=>$row->storeAddress(),
                ];
        */
                $cena=(int)$product->getPrice();
                $cena=$cena/100;
                $cena= number_format($cena,2,".","&nbsp;");
                $container = new Container(StringResource::SESSION_NAMESPACE);  
                $legalStore =  $container->legalStore;
                $timeDelevery=(int)$legalStore[$product->getStoreId()];
                
                ($timeDelevery)?$speedlable="<div class=speedlable>$timeDelevery"."ч</div>":$speedlable="";
                
                $rest=$this->stockBalanceRepository->findAll(['product_id' => $product->getId(), 'store_id' =>$product->getStoreId(), 'array'=>1]);
                $r = $rest[0]['rest'];

                 $return.="<div class='productcard ' >"
                    .$speedlable     
                    ."   <div class='content opacity".$r."'>"
                    ."       <img src='/images/product/".(($product->getUrlHttp())?$product->getUrlHttp():"nophoto_1.jpeg")."' alt='alt' class='productimage'/>"
                    ."       <strong class='blok producttitle'><a  href=#product   >".$product->getTitle()."</a></strong>"
                    ."       <span class='blok'>Id: ".$product->getId()."</span>"
                    ."       <span class='blok'>Артикул: ".$product->getVendorCode()."</span>"
                    ."       <span class='blok'>Торговая марка: ".$product->getBrandTitle()."</span>"                         
                    ."       <span class='blok'>Остаток: ".$r."</span>"
                    ."       <b><span class='blok'>Магазин: ".$product->getStoreTitle()." (id:{$product->getStoreId()})"."</span></b>"
                  //."       <i class='blok'> ".$product->storeAddress()."</i>"                         
                    ."       <span class='blok price'>Цена: ".$cena." &#8381;</span>"
                    ."   </div>"
                    ."</div>"; 
      //return $return;
        }
      return $return;
    
    }
    
        
    public function writeUserAddress ()
    {
         $container = new Container(StringResource::SESSION_NAMESPACE);
         $userAddress= $container->userAddress;
         ($userAddress)?:$userAddress="Укажи адрес и получи заказ за час!";
         return "<span>$userAddress</span   > ";
    }
    public function inputUserAddressForm ()
    {
        //$return.='<input id="address-input" name="suggestions '.$openir.'"  placeholder="укажи адрес доставки"/>';
           return $return;   
    }
    
    
}