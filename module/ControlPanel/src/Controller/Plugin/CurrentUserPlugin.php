<?php

// ControlPanel/src/Controller/Plugin/CurrentUserPlugin.php

namespace ControlPanel\Controller\Plugin;

use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use ControlPanel\Model\Entity\User;
use ControlPanel\Service\UserManager;

/**
 * This controller plugin is designed to let you get the currently logged in User entity
 * inside your controller.
 */
class CurrentUserPlugin extends AbstractPlugin
{

    /**
     * Entity manager.
     * @var ControlPanel\Service\UserManager
     */
    protected UserManager $userManager;

    /**
     * Authentication service.
     * @var Laminas\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * Logged in user.
     * @var ControlPanel\Model\Entity\User
     */
    protected $user = null;

    /**
     * Constructor.
     */
    public function __construct($userManager, $authService)
    {
        $this->userManager = $userManager;
        $this->authService = $authService;
    }

    /**
     * This method is called when you invoke this plugin in your controller: $user = $this->currentUser();
     * @param bool $useCachedUser If true, the User entity is fetched only on the first call (and cached on subsequent calls).
     * @return User|null
     */
    public function __invoke($useCachedUser = true)
    {
        // If current user is already fetched, return it.
        if ($useCachedUser && $this->user !== null) {
            return $this->user;
        }

        // Check if user is logged in.
        if ($this->authService->hasIdentity()) {

            // Fetch User entity from 1c.
            $identity = $this->authService->getIdentity();
            $this->user = $this->userManager->findOneUserObject(['provider_id' => $identity['provider_id'], 'login' => $identity['login'],]);

            if ($this->user == null) {
                // Oops.. the identity presents in session, but there is no such user in database.
                // We throw an exception, because this is a possible security problem.
                throw new \Exception('Not found user with such email');
            }

            // Return found User.
            return $this->user;
        }

        return null;
    }

}
