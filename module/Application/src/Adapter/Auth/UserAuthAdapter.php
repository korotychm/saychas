<?php

// src/Adapter/Auth/UserAuthAdapter.php

namespace Application\Adapter\Auth;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Db\Adapter\AdapterInterface as DbAdapter;
//use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
//use Laminas\Authentication\Result;
use Application\Adapter\Auth\UserAuthResult;
use Application\Resource\StringResource;
use Laminas\Session\Container;

/**
 * Description of AuthAdapter
 *
 * @author alex
 */
class UserAuthAdapter implements AdapterInterface
{

    private ?DbAdapter $adapter;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct(?DbAdapter $adapter = null, $identity = '', $credential = '')
    {
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
//    const FAILURE                        = 0;
//
//    /**
//     * Failure due to identity not being found.
//     */
//    const FAILURE_IDENTITY_NOT_FOUND     = -1;
//
//    /**
//     * Failure due to identity being ambiguous.
//     */
//    const FAILURE_IDENTITY_AMBIGUOUS     = -2;
//
//    /**
//     * Failure due to invalid credential being supplied.
//     */
//    const FAILURE_CREDENTIAL_INVALID     = -3;
//
//    /**
//     * Failure due to uncategorized reasons.
//     */
//    const FAILURE_UNCATEGORIZED          = -4;
//
//    /**
//     * Authentication success.
//     */
//    const SUCCESS                        = 1;
//
        $container = new Container(StringResource::SESSION_NAMESPACE);

        $code = UserAuthResult::FAILURE;

        if (isset($container->userIdentity)) {
            $this->identity = $container->userIdentity;
            $code = UserAuthResult::SUCCESS_FOUND_IN_SESSION;
        }
        $result = new UserAuthResult($code, $this->identity);
        return $result;
    }

}
