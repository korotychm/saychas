<?php
/**
 * changed
 */

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Authentication\AuthenticationService;
//use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Model\Repository\UserRepository;
use Application\Adapter\Auth\UserAuthAdapter;
use Application\Resource\StringResource;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;
use Laminas\Session\Container;
use Application\Service\ExternalCommunicationService;



class UserDataController extends AbstractActionController
{
    private $userRepository;
    private $config;
    private $authService;
    private $db;
    private $userAdapter;
    private $externalCommunicationService;
    
    private $logger;

    public function __construct(
            UserRepository $userRepository,
            $config, $authService, $db, $userAdapter, $externalCommunicationService)
    {
        $this->userRepository = $userRepository;
        $this->config = $config;
        $this->authService = $authService;
        $this->db = $db;
        $this->userAdapter = $userAdapter;
        $this->externalCommunicationService = $externalCommunicationService;
        
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
        $container = new Container(StringResource::SESSION_NAMESPACE);
        unset($container->userIdentity);
        if($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        return $this->getResponse();
    }
    
    public function sendRegistrationSmsAction()
    {
        //$post = $this->params()->fromPost();
        $post = $this->getRequest()->getPost();
        
        $code = $this->externalCommunicationService->sendRegistrationSms($post->phone, $post->code);
        print_r($code);
        return $this->getResponse();
    }
    
    public function addUserDataAction()
    {
        $post=$this->getRequest()->getPost();
        foreach($post as $key => $vale) {
            
        }
        $code = $this->externalCommunicationService->sendRegistrationSms();
        print_r($code);

//        $userId = $this->authService->getIdentity();
//        $userData = new UserData();
//        $userData->setAddress('address1');
//        $userData->setGeodata('geodata1');
//        //$userData->setTime(time());
//        
//        if(null != $userId) {
//            $user = $this->userRepository->find(['id'=>$userId]);
//            if(null != $user) {
//                // User found
//                $user->setUserData([$userData]);
//            }
//        }
        exit;
        return $this->getResponse();
    }
    
    public function createAction()
    {
        $userAuthAdapter = new UserAuthAdapter($this->userRepository);
        $result = $this->authService->authenticate($userAuthAdapter);
        $identity = $this->identity();// $result->getIdentity();
        $this->logger->info('identity = '.$identity);

        return $this->getResponse();
        
    }
    
    public function saveAction()
    {
        $post = $this->params()->fromPost();

        $user = new \Application\Model\Entity\User();
        $user->setId(1);
        $user->setName('user2');
//        $user_id = $this->userRepository->persist($user, []);
        
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
            $user_data = $user->getUserData();
            foreach($user_data as $udata) {
                echo '<pre>';
                print_r($udata);
                echo '</pre>';
            }
            echo $user->getId().'<br/>';
            echo $user->getName().'<br/>';
        }catch (InvalidQueryException $e) {
            print_r($e->getMessage());
            exit;
        }
        return $this->getResponse();
    }

    
}
