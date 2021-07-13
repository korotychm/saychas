<?php

// ControlPanel/src\Service\AuthManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;
use Laminas\Cache\Storage\StorageInterface;
use Laminas\Authentication\AuthenticationService;

/**
 * Description of RbacManager
 *
 * @author alex
 */
class RbacManager
{
    /**
     * File system cache.
     * @var Laminas\Cache\Storage\StorageInterface
     */
    private $cache;
    
    /**
     * RBAC service.
     * @var Laminas\Permissions\Rbac\Rbac
     */    
    private $rbac;
    
    /**
     * Auth service.
     * @var Laminas\Authentication\AuthenticationService 
     */
    private $authService;

    /**
     * Assertion managers.
     * @var array
     */
    private $assertionManagers = [];    
    
    /**
     * Constructs the service.
     */
    public function __construct($authService, $cache, $assertionManagers) 
    {
        $this->authService = $authService;
        $this->cache = $cache;
        $this->assertionManagers = $assertionManagers;
    }    
}
