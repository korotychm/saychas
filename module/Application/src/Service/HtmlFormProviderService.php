<?php
// src\Service\HtmlFormProviderService.php

namespace Application\Service;

use Application\Model\Entity;
use Laminas\Session\Container;
use Application\Resource\Resource;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Laminas\Db\ResultSet\HydratingResultSet;

/**
 * Description of HtmlFormProviderService
 *
 * @author alex
 */
class HtmlFormProviderService {
    
    /**
     * @var StockBalanceRepository
     */
    private $stockBalanceRepository;
    
    public function __construct(StockBalanceRepositoryInterface $stockBalanceRepository) {
        $this->stockBalanceRepository = $stockBalanceRepository;
    }
    /**
     * Returns Html string
     * @return string
     */
    public function testForm()
    {
        return '<h1>Hello world!!! Testing form</h1>';
    }
}
