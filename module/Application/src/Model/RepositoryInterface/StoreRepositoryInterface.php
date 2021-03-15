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
interface StoreRepositoryInterface extends RepositoryInterface {
    public function findStoresByProviderId($provider_id);
}
