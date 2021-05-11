<?php

// /module/src/View/Helper/CatalogHelper.php

namespace Application\View\Helper;

use Laminas\View\Helper\AbstractHelper;

class CatalogHelper extends AbstractHelper
{

    protected $count = 0;

    public function __invoke($banzaii)
    {
        $this->count++;
        $output = sprintf("I have seen '$banzaii' %d time(s).", $this->count);
        return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    }

}
