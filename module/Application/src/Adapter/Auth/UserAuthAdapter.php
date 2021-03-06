<?php

// src/Adapter/Auth/UserAuthAdapter.php

namespace Application\Adapter\Auth;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Db\Adapter\AdapterInterface as DbAdapter;
//use Laminas\Mvc\Controller\AbstractActionController;
//use Laminas\Mvc\Controller\Plugin\Params;
//use Laminas\Authentication\Adapter\Exception\ExceptionInterface;
//use Laminas\Authentication\Result;
use Application\Adapter\Auth\UserAuthResult;
use Application\Resource\Resource;
use Application\Model\Repository\UserRepository;
use Application\Model\Repository\UserDataRepository;
use Application\Model\Entity\User;
use Application\Helper\ArrayHelper;
use Application\Helper\CryptHelper;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Http\Client;
use Laminas\Http\Cookies;
//use Application\Model\Entity\UserData;
//use Application\Model\Entity\Basket;
use Laminas\Session\Container; // as SessionContainer;

/**
 * Description of AuthAdapter
 *
 * @author alex
 */
class UserAuthAdapter implements AdapterInterface
{

    private $userRepository;
    private ?DbAdapter $adapter;
    private $authService;

//    private $sessionContainer;

    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, ?DbAdapter $adapter = null, $authService = null, $identity = '', $credential = '')
    {
        $this->userRepository = $userRepository;
        $this->adapter = $adapter;
        $this->identity = $identity;
        $this->credential = $credential;
        $this->authService = $authService;
    }

    /**
     * Update user data after login
     *
     * @param User $oldUser
     * @param type $container
     */
    private function updateUserData(User $oldUser, $container)
    {
        $userOldData = $oldUser->getUserData(); //->current();
        foreach ($userOldData as $ud) {
            $userData = clone $ud;
            $userData->setUserId($container->userIdentity);
            $userData->persist(['user_id' => $container->userIdentity, 'fias_id' => $ud->getFiasId(), $ud->getFiasLevel()]);
        }
    }

    private function updateBasketData(User $oldUser, $container)
    {
        $userOldBasket = $oldUser->getBasketData();
        foreach ($userOldBasket as $basket) {
            $basketData = clone $basket;
            $basketData->setUserId($container->userIdentity);
            $basketData->persist(['user_id' => $container->userIdentity, 'product_id' => $basket->getProductId(), 'order_id' => 0]);
            $basketData->remove(['where' => ['user_id' => $container->userOldIdentity, 'product_id' => $basket->getProductId()]]);
        }
    }

    /**
     * Performs an authentication attempt
     *
     * @return \Laminas\Authentication\Result
     * @throws \Laminas\Authentication\Adapter\Exception\ExceptionInterface
     *     If authentication cannot be performed
     */
    public function authenticateOld()
    {
        //$container = $this->sessionContainer;// new Container(Resource::SESSION_NAMESPACE);
        $container = new Container(Resource::SESSION_NAMESPACE);

        $code = UserAuthResult::FAILURE;

        if (!isset($container->userIdentity)) {
            $user = new User();
            $user->init();
            $userId = $user->persist(['id' => null]);
//            $userId = $this->userRepository->persist($user, ['id' => null /* $user->getId() */ ]);
            $container->userIdentity = $userId;
            $container->userOldIdentity = $userId;
            $this->identity = $userId;
            $code = UserAuthResult::SUCCESS;
        } else {
            $user = $this->userRepository->find(['id' => $container->userIdentity]);
//            $user = User::find(['id' => $container->userIdentity]);
//            if(!$container->userOldIdentity) {
//                throw new \Exception('Unexpected error: no data found for registered user');
//            }
            if ($container->userOldIdentity && ($container->userIdentity != $container->userOldIdentity)) {
                //$oldUser = User::find(['id' => $container->userOldIdentity]);
                $oldUser = $this->userRepository->find(['id' => $container->userOldIdentity]);
                $this->updateUserData($oldUser, $container);
                $this->updateBasketData($oldUser, $container);
            }

            $this->identity = $container->userIdentity;
            if (null == $user) {
                throw new \Exception('Unknown identity error');
            }
            $code = UserAuthResult::SUCCESS;
        }

        $result = new UserAuthResult($code, $this->identity);
        return $result;
    }

    /**
     * Performs an authentication attempt
     *
     * @return UserAuthResult
     */
    public function authenticate()
    {
        $container = new Container(Resource::SESSION_NAMESPACE);

        if (!isset($container->userIdentity)) {
            return $this->findUserCookie($container);
        }

        return $this->authUser($container);
    }

    /**
     *
     * @param object $container
     * @return UserAuthResult
     */
    private function findUserCookie($container)
    {
        $userPhone = !empty($userCookie = filter_input(INPUT_COOKIE, Resource::USER_COOKIE_NAME, FILTER_DEFAULT)) ? CryptHelper::decrypt(urldecode($userCookie)) : null;
        $user = (!empty($userPhone)) ? User::find(['phone' => $userPhone]) : null;

        if (!empty($user) and!empty($userId = $user->getId())) {
            $container->userIdentity = $userId;
            $container->userOldIdentity = $userId;
            $this->identity = $userId;
            $code = UserAuthResult::SUCCESS;
            setcookie(Resource::USER_COOKIE_NAME, CryptHelper::encrypt($userPhone), time() + Resource::USER_COOKIE_TIME_LIVE, "/");

            return new UserAuthResult($code, $this->identity);
        }

        return $this->createUser($container);
    }

    /**
     *
     * @param object $container
     * @return UserAuthResult
     */
    private function createUser($container)
    {

        $user = new User();
        $user->init();
        $userId = $user->persist(['id' => null]);
//            $userId = $this->userRepository->persist($user, ['id' => null /* $user->getId() */ ]);
        $container->userIdentity = $userId;
        $container->userOldIdentity = $userId;
        $this->identity = $userId;
        $code = UserAuthResult::SUCCESS;

        return new UserAuthResult($code, $this->identity);
    }

    /**
     *
     * @param object $container
     * @return UserAuthResult
     * @throws \Exception
     */
    private function authUser($container)
    {
//            if(!$container->userOldIdentity) {
//                throw new \Exception('Unexpected error: no data found for registered user');
//            }
        if (empty($user = User::find(['id' => $container->userIdentity]))) {
            return $this->createUser($container);
        }

        if ($container->userOldIdentity && ($container->userIdentity != $container->userOldIdentity)) {
            $oldUser = User::find(['id' => $container->userOldIdentity]);
            $this->updateUserData($oldUser, $container);
            $this->updateBasketData($oldUser, $container);
        }

        $this->identity = $container->userIdentity;
        $code = UserAuthResult::SUCCESS;

        return new UserAuthResult($code, $this->identity);
    }

}

//                $userOldData = $oldUser->getUserData();//->current();
//                foreach($userOldData as $ud) {
//                    $userData = new UserData();
//                    $userData->setAddress($ud->getAddress());
//                    $userData->setFiasId($ud->getFiasId());
//                    $userData->setFiasLevel($ud->getFiasLevel());
//                    $userData->setUserId($container->userIdentity);
//                    $userData->persist(['user_id' => $container->userIdentity, 'fias_id' => $ud->getFiasId(), $ud->getFiasLevel()]);
//                }













//            $basketData = new Basket();
//            $basketData->setUserId($container->userIdentity);
//            $basketData->setProductId($basket->getProductId());
//            $basketData->setOrderId($basket->getOrderId());
//            $basketData->setPrice($basket->getPrice());
//            $basketData->setDiscount($basket->getDiscount());
//            $basketData->setDiscountDescription($basket->getDiscountDescription());
//            $basketData->setTotal($basket->getTotal());











//            $userData = new UserData();
//            $userData->setAddress($ud->getAddress());
//            $userData->setFiasId($ud->getFiasId());
//            $userData->setFiasLevel($ud->getFiasLevel());

