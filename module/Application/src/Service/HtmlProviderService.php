<?php
// Application\src\Service\HtmlProviderService.php

namespace Application\Service;

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
           return   "<span class='bread-crumbs-item'>".join("</span> / <span class='bread-crumbs-item'>",$return)."</span>";
        endif;
    }
    
}