<?php

// ControlPanel/src\Service\RbacAssertionManager.php

namespace ControlPanel\Service;

use Laminas\Permissions\Rbac\Rbac;

/**
 * This service is used for invoking user-defined RBAC dynamic assertions.
 */
class RbacAssertionManager
{

    /**
     * Auth service.
     * @var Laminas\Authentication\AuthenticationService
     */
    private $authService;
    
    private $userManager;
    
    // private $productManager;
    
    private $container;

    /**
     * Constructs the service.
     */
    public function __construct($container, $authService, $userManager/*, $productManager*/)
    {
        $this->authService = $authService;
        $this->userManager = $userManager;
        //$this->productManager = $productManager;
        $this->container = $container;
    }

    /**
     * This method is used for dynamic assertions.
     * We use assertions to perform additional verification
     * 
     * @param Rbac $rbac
     * @param string $permission
     * @param array $params
     * @return boolean
     */
    public function assert(Rbac $rbac, $permission, $params)
    {
        /**
         * @Todo:
         * Remove comments below and modify the code to conduct additional verification
         */
        $identity = $this->authService->getIdentity();
        //$product = $this->productManager->find(['id' => $params['product_id']]);
        $managerName = $params['manager'];
        // $product = $this->$manager->find(['id' => $params['product_id']]);
        $manager = $this->container->get($managerName);
        $document = $manager->find($params['where']);
        if(null != $document && ($document['provider_id'] != $identity['provider_id']) ) {
            return true;
        }
//        $currentUser = $this->userManager->findOne(['provider_id' => $identity['provider_id'], 'login' => $identity['login'],]);
//
//        if ($permission == 'developer' && $params['user']['login'] == $currentUser['login']) {
//            return true;
//        }

        return false;
    }

}
