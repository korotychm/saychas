<?php

// src/Controller/UserDataController.php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Mvc\MvcEvent;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Authentication\AuthenticationService;
use Application\Model\Repository\UserRepository;
use Application\Adapter\Auth\UserAuthAdapter;
use Application\Resource\StringResource;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;
use Laminas\Session\Container; // as SessionContainer;
use Application\Service\ExternalCommunicationService;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;
use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;
use Laminas\View\Model\ViewModel;

//use Laminas\Session\SessionManager;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Laminas\ServiceManager\ServiceManager;

/**
 * UserDataController
 */
class UserDataController extends AbstractActionController
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Laminas/Config/Config
     */
//    private $config;

    /**
     * @var Laminas\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * @var Laminas\Db\Adapter\AdapterInterface
     */
//    private $db;

    /**
     * @var Application\Adapter\Auth\UserAuthAdapter
     */
//    private $userAdapter;

    /**
     * @var Application\Service\ExternalCommunicationService
     */
    private $externalCommunicationService;

//    private $sessionContainer;

    /**
     * @var Laminas\Log\Logger
     */
    private $logger;

//    private SessionManager $sessionManager;
//    private ServiceManager $serviceManager;

    /**
     * Constructor.
     * 
     * @param UserRepository $userRepository
     * @param type $authService
     * @param type $externalCommunicationService
     */
    public function __construct(
            UserRepository $userRepository,
            /* $config, */ $authService, /* $db, */ /* $userAdapter, */ $externalCommunicationService/* , $sessionContainer */)
    {
        $this->userRepository = $userRepository;
//        $this->config = $config;
        $this->authService = $authService;

//        $this->sessionContainer = $sessionContainer;
//        $this->db = $db;
//        $this->userAdapter = $userAdapter;
        $this->externalCommunicationService = $externalCommunicationService;

        $this->logger = new Logger();
        $writer = new StreamWriter('php://output');
        $this->logger->addWriter($writer);
    }

    /**
     * Execute the request
     *
     * @param MvcEvent $e
     * @return Laminas\Http\Response
     */
    public function onDispatch(MvcEvent $e)
    {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        return $response;
    }

    /**
     * Clear identity
     *
     * @return Response
     */
    public function clearAction()
    {
        //$container = $this->sessionContainer;// new Container(StringResource::SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
        unset($container->userIdentity);
        if ($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location:/"); //exit();
        return $this->getResponse();
    }

    /**
     * Generate random code to send
     * to use along with the phone number
     *
     * @param int $phone
     * @return int
     */
    private function generateRegistrationCode($phone)
    {
        /** @var $phone */
        /* $phone is meant to be a a session key */
        // Generate new code and store it in session
        //$container = $this->sessionContainer;// new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $container = new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $code = 7777; // simulate generation
        $container->userPhoneIdentity = ['phone' => $phone, 'code' => $code];
        return $code;
    }

    /**
     * Send registration sms
     *
     * @return JsonModel
     */
    public function sendRegistrationSmsAction()
    {
        $post = $this->getRequest()->getPost();

        $code = $this->generateRegistrationCode($post->phone);

        $answer = $this->externalCommunicationService->sendRegistrationSms($post->phone, $code);

        $response = $this->getResponse();
        if (true != $answer['result']) {
            $response->setStatusCode(Response::STATUS_CODE_400);
        } else {
            $response->setStatusCode(Response::STATUS_CODE_200);
        }

        return new JsonModel($answer);
    }

    /**
     * Compare feedback code with the generated one
     *
     * @return JsonModel
     */
    public function codeFeedbackAction()
    {
        // Compare feedback with sent registration code
        $post = $this->getRequest()->getPost();
        $code = $post->code;
        //$container = $this->sessionContainer;// new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $container = new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $storedCode = $container->userPhoneIdentity['code'];
        if ($storedCode == $code) {
            // Unset userPhoneIdentity
            // We only unset userPhoneIdentity if stored code matches the received one
            // So the user has a few attempts
            // Probably we will limit the number of attempts to a certain number;
            unset($container->userPhoneIdentity);
            // Registered
            return new JsonModel(['result' => true]);
        }
        return new JsonModel(['result' => false]);
    }

    /**
     * Set client info.
     *
     * @return JsonModel
     */
    public function setClientInfoAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        //$post['phone'] = (int) $post['phone'];
        $answer = $this->externalCommunicationService->setClientInfo($post);
        return new JsonModel($answer);
    }

    /**
     * Get client info.
     * 
     * @return JsonModel
     */
    public function getClientInfoAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        //$post['phone'] = (int) $post['phone'];
        $answer = $this->externalCommunicationService->getClientInfo($post);
        return new JsonModel($answer);
    }

    /**
     * Update client info.
     * 
     * @return JsonModel
     */
    public function updateClientInfoAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        //$post['phone'] = (int) $post['phone'];
        $answer = $this->externalCommunicationService->updateClientInfo($post);
        return new JsonModel($answer);
    }

    /**
     * Change client password
     * 
     * @return JsonModel
     */
    public function changeClientPasswordAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $answer = $this->externalCommunicationService->changePassword($post);
        return new JsonModel($answer);
    }

    /**
     * Login client
     * 
     * @return JsonModel
     */
    public function clientLoginAction()
    {
        $post = $this->getRequest()->getPost()->toArray();
        $answer = $this->externalCommunicationService->clientLogin($post);
        return new JsonModel($answer);
    }

    public function userAuthModalAction()
    {
        
        /*$print_r = $response = $this->externalCommunicationService->clientLogin([
                            "phone" => "79132146666",
                            "password" => "111",
                            ]);
        exit (print_r($print_r));*/
        
        //userNameInput userSmsCode userPass
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $password = $smsCode = "7777"; //костыль
        
        $title = StringResource::MESSAGE_ENTER_OR_REGISTER_TITLE;
        $buttonLable = StringResource::BUTTON_LABLE_CONTINUE;
        
        $post = $this->getRequest()->getPost();
        if ($goStepOne = $post->goStepOne) {
            unset($container->userAutTmpSession);
        } else {
            $print_r = print_r($post, true) ;
            $return['phone'] = $post->userPhone; // $this->phoneToNum($post->userPhone);
            //$return['phoneFormated'] = StringHelper::phoneToNum($post->userPhone);// $this->phoneToNum($post->userPhone);
            $return['name'] = $post->userNameInput;
            $code = $post->userSmsCode;
            $container = new Container(StringResource::SESSION_NAMESPACE);
            $buttonLable = StringResource::BUTTON_LABLE_ENTER;

            if (!$return['phone']) {
                $error["phone"] = StringResource::ERROR_INPUT_PHONE_MESSAGE;
                
            } else {

                $userAutSession["phone"] = $return['phone'];
                $container->userAutTmpSession = $userAutSession;
                $stepOne = true;
                $user = $this->userRepository->findFirstOrDefault(["phone" => StringHelper::phoneToNum($return['phone'])]);
                if ($user and $userId = $user->getUserId() and $userId = $user->getId()) {
                    $passBlock = true;
                    $title = StringResource::USER_LABLE_HELLO . $user->getName();
                    if ( $post->userPass ){
                        //$response = $this->externalCommunicationService->clientLogin(["phone" =>,  ])
                        $print_r = $response = $this->externalCommunicationService->clientLogin([
                            "phone" => $return['phone'],
                            "password" => $post->userPass,
                            ]);
                        if (!$response["result"]) {
                             $error["password"] = $response["errorDescription"]; 
                        }
                        else {
                            $container->userIdentity = $userId;
                            $reloadPage = true;
                            return new JsonModel(["reload"=>true]);
                        }
                        
                    } /**/
                } else {
                    $CodeBlock = true;
                    $UserBlock = true;
                    $buttonLable = StringResource::BUTTON_LABLE_REGISTER;
                    /* if ($return['name'] and $code == $smsCode) {

                      $user = $this->userRepository->findFirstOrDefault(["id" => $container->userIdentity]);
                      $user->setName($return['name']);
                      $user->setPhone($return['phone']);
                      $this->userRepository->persist($user, ['id' => $user->getId()]);
                      $return["error"] = false;
                      }
                      $return["message"] = StringResource::ERROR_INPUT_NAME_SMS_MESSAGE;  //это телефонный номер  юзера */
                }
            }
        }
        //$return['post'] = $post;

        $view = new ViewModel([
            'reloadPage' => $reloadPage,
            'printr'     =>  "<pre>" . print_r($print_r, true). "</pre>",
            'title'      => $title,
            'buttonLable'=> $buttonLable,
            'error'      => $error,
            'sengingPhoneFormated' => $return['phone'],
            'sengingPhone' => StringHelper::phoneToNum($return['phone']),
            'passBlock' => $passBlock,
            'UserBlock' => $UserBlock,
            'CodeBlock' => $CodeBlock,
            'stepOne'   => $stepOne,
        ]);
        $view->setTemplate('application/common/auth-form-in-modal');
        return $view->setTerminal(true);
    }

}
