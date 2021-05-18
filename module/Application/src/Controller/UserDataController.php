<?php
/**
 * changed
 */

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Repository\UserRepository;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;



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
    
    public function saveAction()
    {
        $post = $this->params()->fromPost();
        
//        $users = $this->userRepository->findAll([]);
//        foreach ($users as $user) {
//            print_r($user);
//        }
        
        $user = new \Application\Model\Entity\User();
        //$user->setId(9);
        $user->setName('user2');
        $user->setEmail('email3');
        $user->setGeodata('geodata3');
        $user->setAddress('address2');
        try {
            $id = $this->userRepository->persist($user, []);
            echo $id.'<br/>';
            print_r($user);
        }catch (InvalidQueryException $e) {
            print_r($e->getMessage());
            exit;
        }
        return $this->getResponse();
    }

    
}
