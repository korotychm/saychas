<?php
// src\Service\HtmlProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Application\Resource\StringResource;

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
    public function productCard($a=[])
    {
        if (count($a)):
              /*  
              $productCardParam['price']
              $productCardParam['title']
              $productCardParam['img']
              $productCardParam['id']
              $productCardParam['rest']
              $productCardParam['articul']
              $productCardParam['brand']
            */
                $cena=(int)$a['price'];
                $cena=$cena/100;
                $cena= number_format($cena,2,".","&nbsp;");
                
                 $return.="<div class='productcard ' >"
                    ."   <div class='content opacity".(int)$a['rest']."'>"
                    ."       <img src='/images/product/".(($a['img'])?$a['img']:"nophoto_1.jpeg")."' alt='alt' class='productimage'/>"
                    ."       <strong class='blok producttitle'><a  href=#product   >".$a['title']."</a></strong>"
                    ."       <span class='blok'>Id: ".$a['id']."</span>"
                    ."       <span class='blok'>Артикул: ".$a['articul']."</span>"
                    ."       <span class='blok'>Торговая марка: ".$a['brand']."</span>"                         
                    ."       <span class='blok'>Остаток: ".(int)$a['rest']."</span>"
                    ."       <b><span class='blok'>Магазин: ".$a['store']."</span></b>"
                    ."       <i class='blok'> ".$a['store_address']."</i>"                         
                    ."       <span class='blok price'>Цена: ".$cena." &#8381;</span>"
                    ."   </div>"
                    ."</div>"; 
                 
                 return $return;
        endif;
    
    }
    
        
    public function writeUserAddress ()
    {
         $container = new Container(StringResource::SESSION_NAMESPACE);
         $userAddress= $container->userAddress;
         ($userAddress)?:$userAddress="Укажи адрес и получи заказ за час!";
         return "<div class='geo16 open-user-address-form ' ><span>$userAddress</span   > </div>";
    }
    public function inputUserAddressForm ()
    {
        //$return.='<input id="address-input" name="suggestions '.$openir.'"  placeholder="укажи адрес доставки"/>';
           return $return;   
    }
    
    
}