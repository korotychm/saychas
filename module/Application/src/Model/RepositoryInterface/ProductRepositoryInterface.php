<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Application\Model\RepositoryInterface;

/**
 * @author alex
 */
interface ProductRepositoryInterface extends RepositoryInterface {
    /**
     * @param int $storeId
     * @param array $params
     */
    public function findProductsByProviderIdAndExtraCondition($storeId, $params=[]);
}
