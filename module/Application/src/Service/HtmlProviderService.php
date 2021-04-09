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
               * 
               */
            //$provier = $this->prov
            //$price = $this->priceRepository->find('');
                
                //$provider = $this->providerRepository->find(['product_id'=>$product->getId()]);
                
                //exit(print_r(['product_id=?'=>$product->getId(), 'provider_id=?', $product->getProviderId()])); 
                $cena = $this->priceRepository->find(['product_id'=>$product->getId(), 'provider_id', $product->getProviderId()]);
                
                
                
                //$cena= (int)$product->getPrice();
                $cena=$cena/100;
                $cena= number_format($cena,2,".","&nbsp;");
                $container = new Container(StringResource::SESSION_NAMESPACE);  
                $legalStore =  $container->legalStore;
                $timeDelevery=(int)$legalStore[$product->getStoreId()];
                
                ($timeDelevery)?$speedlable="<div class=speedlable>$timeDelevery"."ч</div>":$speedlable="";

                $rest=$this->stockBalanceRepository->find(['product_id=?' => $product->getId(), 'store_id=?' =>$product->getStoreId()]);
                                
                $r = (int) $rest->getRest();


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