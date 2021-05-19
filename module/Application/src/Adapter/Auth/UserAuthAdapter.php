<?php

// src/Adapter/Auth/UserAuthAdapter.php

namespace Application\Adapter\Auth;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Db\Adapter\AdapterInterface as DbAdapter;
//use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
//use Laminas\Authentication\Result;
use Application\Adapter\Auth\UserAuthResult;
use Application\Resource\StringResource;
use Application\Model\Repository\UserRepository;
use Application\Model\Entity\User;
use Laminas\Session\Container;

/**
 * Description of AuthAdapter
 *
 * @author alex
 */
class UserAuthAdapter implements AdapterInterface
{

    private $userRepository;
    private ?DbAdapter $adapter;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, ?DbAdapter $adapter = null, $identity = '', $credential = '')
    {
        $this->userRepository = $userRepository;
        $this->adapter = $adapter;
        $this->identity = $identity;
        $this->credential = $credential;
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
        $container = new Container(StringResource::SESSION_NAMESPACE);

        $code = UserAuthResult::FAILURE;

        if(!isset($container->userIdentity)) {
            $user = new User();
            $user->init();
            $userId = $this->userRepository->persist($user, []);
            $this->identity = $container->userIdentity = $userId;
            $code = UserAuthResult::SUCCESS;
        }else{
            $user = $this->userRepository->find(['id' => $container->userIdentity]);
            $this->identity = $container->userIdentity;
            if(null == $user) {
                throw new \Exception('Unknown identity error');
            }
            $code = UserAuthResult::SUCCESS;
        }
        
        $result = new UserAuthResult($code, $this->identity);
        return $result;
    }

}
