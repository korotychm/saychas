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
            /* $productCardParam = [
              'price'=>$product->getPrice(),
              'title'=>$product->getTitle(),
              'img'=>$product->getHttpUrl()   ,
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
              /* */

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
                $filtersTmp = explode(",", $product->getParamValueList2());
                 $filters = array_merge($filters, $filtersTmp);
            
            if (!($filtrForCategory[$category_id]['hasRestOnly'] and!$r)) {
            
                
                $return->card .= "<div class='productcard ' >"
                        .     $speedlable
                        . "   <div class='content opacity" . $r . "'>"
                        . "       <img src='/images/product/" . (($product->getHttpUrl()) ? $product->getHttpUrl() : "nophoto_1.jpeg") . "' alt='alt' class='productimage'/>"
                        . "       <strong class='blok producttitle'><a  href=#product   >" . $product->getTitle() . "</a></strong>"
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
