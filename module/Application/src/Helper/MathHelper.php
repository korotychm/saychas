<?php

// src/Helper/MathHelper.php

namespace Application\Helper;

/**
 * Description of MathHelper
 *
 * @author alex
 */
class MathHelper
{
 
    /**
     * 
     * @param int $price
     * @param int $discount
     * @return int
     */
    public static function roundRealPrice ($price, $discount)
    {
        $realPrice = ($price - $price * $discount /100);
        
        return  round($realPrice/100) * 100;
    }
    
    /**
     * 
     * @param int $price
     * @return int
     */
    public static function roundPrice ($price)
    {
        return  round($price/100) * 100;
    }
    
}
