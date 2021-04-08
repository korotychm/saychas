<?php
// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Application\Resource\StringResource;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;

class HtmlProviderService
{
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
        
  
        foreach ($filteredProducts as $row){
              $productCardParam = [
                    'price'=>$row->getPrice(),
                    'title'=>$row->getTitle(),
                    'img'=>$row->getUrlHttp(),
                    'id'=>$row->getId(),
                    'rest'=>$row->getRest(),
                    'articul'=>$row->getVendorCode(),
                    'brand'=>$row->getBrandTitle(),
                    'description'=>$row->getDescription(),
                    'param_value'=>$row->getParamValueList(),
                    'param_value'=>$row->getParamValueList(),
                    'store'=>$row->getStoreTitle()." (id:{$row->getStoreId()})",
                    'store_id'=>$row->getStoreId(),
                    //'store_address'=>$row->storeAddress(),
                ];
        
         
                $cena=(int)$productCardParam['price'];
                $cena=$cena/100;
                $cena= number_format($cena,2,".","&nbsp;");
                
                 $return.="<div class='productcard ' >"
                    ."   <div class='content opacity".(int)$productCardParam['rest']."'>"
                    ."       <img src='/images/product/".(($productCardParam['img'])?$productCardParam['img']:"nophoto_1.jpeg")."' alt='alt' class='productimage'/>"
                    ."       <strong class='blok producttitle'><a  href=#product   >".$productCardParam['title']."</a></strong>"
                    ."       <span class='blok'>Id: ".$productCardParam['id']."</span>"
                    ."       <span class='blok'>Артикул: ".$productCardParam['articul']."</span>"
                    ."       <span class='blok'>Торговая марка: ".$productCardParam['brand']."</span>"                         
                    ."       <span class='blok'>Остаток: ".(int)$productCardParam['rest']."</span>"
                    ."       <b><span class='blok'>Магазин: ".$productCardParam['store']."</span></b>"
                    ."       <i class='blok'> ".$productCardParam['store_address']."</i>"                         
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