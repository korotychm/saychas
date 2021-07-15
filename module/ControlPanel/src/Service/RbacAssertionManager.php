<?php

// ControlPanel/src\Service\AuthManager.php

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

    /**
     * Constructs the service.
     */
    public function __construct($authService)
    {
        $this->authService = $authService;
    }

    /**
     * This method is used for dynamic assertions.
     */
    public function assert(Rbac $rbac, $permission, $params)
    {
//        $currentUser = $this->entityManager->getRepository(User::class)
//                ->findOneByEmail($this->authService->getIdentity());
//
//        if ($permission=='profile.own.view' && $params['user']->getId()==$currentUser->getId())
//            return true;

        return false;
    }

}
