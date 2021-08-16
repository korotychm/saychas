<?php

// src/Helper/StringHelper.php

namespace Application\Helper;

/**
 * Description of StringHelper
 *
 * @author Sizov D.N.
 */
class StringHelper
{

    public static function phoneToNum($destination_numbers)
    {
        $numbers = $sort_numbers = [];
        if (!is_array($destination_numbers)) {
            $destination_numbers = trim($destination_numbers);
            $dest_length = strlen($destination_numbers);
            $destination_numbers = str_replace(array(",", "\n"), ";", $destination_numbers);
            $sort_numbers = explode(';', $destination_numbers);
        } else {
            $sort_numbers = $destination_numbers;
        }

        foreach ($sort_numbers as $arInd) {
            $arInd = trim($arInd);
            $symbol = false;
            $spec_sym = array("+", "(", ")", " ", "-", "_");
            for ($i = 0; $i < strlen($arInd); $i++) {
                if (!is_numeric($arInd[$i]) && !in_array($arInd[$i], $spec_sym)) {
                    $symbol = true;
                }
            }
            if ($symbol) {
                $numbers[] = $arInd;
            } else {
                $arInd = str_replace($spec_sym, "", $arInd);

                if (strlen($arInd) < 10 || strlen($arInd) > 15) {
                    continue;
                } else {
                    if (strlen($arInd) == 10 && $arInd[0] == '9') {
                        $arInd = '7' . $arInd;
                    }
                    if (strlen($arInd) == 11 && $arInd[0] == '8') {
                        $arInd[0] = "7";
                    }
                    $numbers[] = $arInd;
                }
            }
        }
        return $numbers[0];
    }

    public static function eolFormating ($text)
    {
        return "<p>".str_replace("\r\n","<p></p>",$text)."</p>";
    }
    
    public static function phoneFromNum($from)
    {
        return "+"
                . sprintf("%s (%s) %s-%s-%s",
                        substr($from, 0, 1),
                        substr($from, 1, 3),
                        substr($from, 4, 3),
                        substr($from, 7, 2),
                        substr($from, 9)
        );
    }
    public static function cutAddress ($address)
    {
           $address_tmp = explode(",", $address);
           unset($address_tmp[0], $address_tmp[1], $address_tmp[2]);
           return join("," , $address_tmp);
                
    }
    public static function cutAddressCity ($address)
    {
           $address_tmp = explode(",", $address);
           unset($address_tmp[0]);
           return join("," , $address_tmp);
                
    }

}
