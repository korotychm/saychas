<?php

// ControlPanel/src/Helper/ArrayHelper.php

namespace ControlPanel\Helper;

/**
 * Description of ArrayHelper
 *
 * @author alex
 */
class ArrayHelper
{

    public static function extractCredentials(array $credentials): array
    {
        $result = [];
        foreach ($credentials as $c) {
            list($key, $value) = explode(':', $c);
            $result[trim($key)] = trim($value);
        }

        return $result;
    }

}
