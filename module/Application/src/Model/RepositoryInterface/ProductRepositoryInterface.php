<?php

// src/Model/Repository/RepositoryInterface/ProductRepositoryInterface.php

namespace Application\Model\RepositoryInterface;

/**
 * @author alex
 */
interface ProductRepositoryInterface extends RepositoryInterface
{

    /**
     * @param int $storeId
     * @param array $params
     */
    public function findProductsByProviderIdAndExtraCondition($storeId, $params = []);
}
