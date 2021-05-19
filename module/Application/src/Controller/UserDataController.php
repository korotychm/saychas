<?php
/**
 * changed
 */

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\User;
use Application\Model\Repository\UserRepository;
use Application\Adapter\Auth\UserAuthAdapter;
use Application\Resource\StringResource;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;
use Laminas\Session\Container;



class UserDataController extends AbstractActionController
{
    private $userRepository;
    private $config;
    private $authService;
    private $db;
    private $userAdapter;
    
    private $logger;

    public function __construct(
            UserRepository $userRepository,
            $config, $authService, $db, $userAdapter)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
        $this->authService = $authService;
        $this->db = $db;
        $this->userAdapter = $userAdapter;
        
        $this->logger = new Logger();
        $writer = new StreamWriter('php://output');
        $this->logger->addWriter($writer);
    }

    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        return $response;
        
    }

    public function clearAction()
    {
        if($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        return $this->getResponse();
    }
    public function createAction()
    {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        
        if(!isset($container->userIdentity)) {
            $user = new User();
            $user->init();
            $userId = $this->userRepository->persist($user, []);
            $container->userIdentity = $userId;
        }
        $userAuthAdapter = new UserAuthAdapter($this->userRepository);
//        $userAuthAdapter->setIdentity($container->userIdentity);
//        $userAuthAdapter->setCredential($container->userIdentity);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        $this->logger->info('<br/>');
        $this->logger->info($code);
        if($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            unset($container->userIdentity);
        }

        return $this->getResponse();
        
    }
    
    public function saveAction()
    {
        $post = $this->params()->fromPost();
        
//        $users = $this->userRepository->findAll([]);
//        foreach ($users as $user) {
//            print_r($user);
//        }

        $user = new \Application\Model\Entity\User();
        $user->setId(1);
        $user->setName('user2');
        $user->setEmail('email4');
        $user->setGeodata('geodata3');
        $user->setAddress('address2');
        $user_id = $this->userRepository->persist($user, []);
        
        $ud = [];
        $userData = new \Application\Model\Entity\UserData();
        $userData->setId(3);
//        $userData->setUserId(1);
        $userData->setAddress('address3 - shmadres - asdf');
        $userData->setGeodata('geodata3 - shmeodata - adsf');
//        $userData->setTime(time());
        $ud[] = $userData;
        $userData1 = new \Application\Model\Entity\UserData();
        $userData1->setId(4);
        $userData1->setUserId(1);
        $userData1->setAddress('address4 - shmnadres');
        $userData1->setGeodata('geodata4 - shmeodata');
//        $userData1->setTime(time());
        $ud[] = $userData1;
        
        $user->setUserData($ud);
        
        try {
//            $id = $this->userRepository->persist($user, []);
//            echo $id.'<br/>';
            $user_data = $user->getUserData();
            foreach($user_data as $udata) {
                echo '<pre>';
                print_r($udata);
                echo '</pre>';
            }
            echo $user->getId().'<br/>';
            echo $user->getName().'<br/>';
            echo $user->getPhone().'<br/>';
            echo $user->getEmail().'<br/>';
            echo $user->getAddress().'<br/>';
        }catch (InvalidQueryException $e) {
            print_r($e->getMessage());
            exit;
        }
        return $this->getResponse();
    }

    
}
