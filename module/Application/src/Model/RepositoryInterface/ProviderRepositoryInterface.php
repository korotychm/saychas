<?php
// src/Model/Repository/RepositoryInterface/ProviderRepositoryInterface.php

namespace Application\Model\RepositoryInterface;

interface ProviderRepositoryInterface extends RepositoryInterface
{
  /**/
  //* Returns a list of providers from only availble stores,  width limit and order
  /**/
   public function findAvailableProviders ($param,$order, $limit, $offset);
    

}