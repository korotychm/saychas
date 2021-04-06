<?php
// src/Model/Repository/RepositoryInterface/StoreRepositoryInterface.php

namespace Application\Model\RepositoryInterface;

/**
 * @author alex
 */
interface StoreRepositoryInterface extends RepositoryInterface {
    /**
     * @param int $provider_id
     */
    public function findStoresByProviderIdAndExtraCondition($provider_id, $param);
}
