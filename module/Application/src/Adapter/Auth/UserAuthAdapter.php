<?php
// src/Adapter/Auth/AuthAdapter.php

namespace Application\Adapter\Auth;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
use Laminas\Authentication\Result;
/**
 * Description of AuthAdapter
 *
 * @author alex
 */
class UserAuthAdapter
{
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct($username, $password)
    {
        // ...
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Laminas\Authentication\Result
     * @throws \Laminas\Authentication\Adapter\Exception\ExceptionInterface
     *     If authentication cannot be performed
     */
    public function authenticate()
    {
        // ...
    }    
}
