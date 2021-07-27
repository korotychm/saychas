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
    private function testEmail($email)
    {
        /* if (filter_var($email, FILTER_VALIDATE_EMAIL))return true;
          return false; */
        return true;
    }

    private function generateRegistrationCode($phone, $length = 4)
    {
        /** @var $phone */
        /* $phone is meant to be a a session key */
        // Generate new code and store it in session
        //$container = $this->sessionContainer;// new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        /* $suffle=[0,1,3,4,5,6,7,8,9];
          shuffle($suffle);
          for ($i=0; $i < $length; $i++ ){
          $code.=$suffle[$i]; //real generation
          }/* */

        //$container = new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
        $code = 7777; // simulate generation
        $container->userPhoneIdentity = ['phone' => $phone, 'code' => $code, 'live' => (time() + 60)];
        return $code;
    }

    private function sendSms($phone)
    {
        $code = $this->generateRegistrationCode($phone);
        $answer = $this->externalCommunicationService->sendRegistrationSms($phone, $code);
        return $answer;
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
        //$container = new Container(StringResource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $container = new Container(StringResource::SESSION_NAMESPACE);
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

        $container = new Container(StringResource::SESSION_NAMESPACE);
        $userAutSession = $container->userAutSession;
        $title = StringResource::MESSAGE_ENTER_OR_REGISTER_TITLE;
        $buttonLable = StringResource::BUTTON_LABLE_CONTINUE;

        $post = $this->getRequest()->getPost();
        if ($goStepOne = $post->goStepOne) {
            unset($container->userAutTmpSession);
            unset($container->userPhoneIdentity);
        } else {
            $print_r = $post;
            $return['phone'] = $post->userPhone;
            $return['name'] = $post->userNameInput;
            $code = $post->userSmsCode;
            $container = new Container(StringResource::SESSION_NAMESPACE);
            $buttonLable = StringResource::BUTTON_LABLE_ENTER;

            if (!$return['phone']) {
                $error["phone"] = StringResource::ERROR_INPUT_PHONE_MESSAGE;
            } else {

                $userAutSession["phone"] = $return['phone'];

                $stepOne = true;
                $user = $this->userRepository->findFirstOrDefault(["phone" => StringHelper::phoneToNum($return['phone'])]);
                if ($user and $userId = $user->getUserId() and $userId = $user->getId()) {
                    if ($post->forgetPassHidden) {
                        
                        $userAutSession["passforget"]=1;
                        $title = StringResource::MESSAGE_PASSFORGOT_TITLE;
                        $CodeBlock = true;
                        $passForgetBlock = true;

                        $buttonLable = StringResource::BUTTON_LABLE_PASS_CHANGE;
                        $userPhoneIdentity = $container->userPhoneIdentity;
                        $codeExist = $userPhoneIdentity['code'];

                        if (!$codeExist) {
                            $print_r = $codeSendAnswer = $this->sendSms(StringHelper::phoneToNum($return['phone']));
                            if (!$codeSendAnswer['result']) {
                                $error['sms'] = StringResource::ERROR_SEND_SMS_MESSAGE;
                            } else {
                                $print_r = $codeExist;
                            }
                        } else {
                            if (!$userSmsCode or $userSmsCode != $codeExist) {
                                $registerPossible = false;
                                unset($userAutSession['smscode']);
                                $error['smscode'] = StringResource::ERROR_SEND_SMS_CODE_MESSAGE;
                            } else {
                                $userAutSession['smscode'] = $userSmsCode;
                            }
                        }
                    } else {
                        $passBlock = true;
                        $title = StringResource::USER_LABLE_HELLO . $user->getName();
                        if ($post->userPass) {
                            $print_r = $response = $this->externalCommunicationService->clientLogin([
                                "phone" => StringHelper::phoneToNum($return['phone']),
                                "password" => $post->userPass,
                            ]);
                            if (!$response["result"]) {
                                $error["password"] = $response["errorDescription"];
                            } else {
                                $container->userIdentity = $userId;
                                //$reloadPage = true;
                                unset($container->userAutSession);
                                unset($container->userPhoneIdentity);
                                return new JsonModel(["reload" => true]);
                            }
                        }
                    } /**/
                } else {
                    //exit (print_r($user));
                    $title = StringResource::MESSAGE_REGISTER_TITLE;
                    $CodeBlock = true;
                    $UserBlock = true;
                    $buttonLable = StringResource::BUTTON_LABLE_REGISTER;
                    $userPhoneIdentity = $container->userPhoneIdentity;
                    $codeExist = $userPhoneIdentity['code'];
                    if (!$codeExist) {

                        $print_r = $codeSendAnswer = $this->sendSms(StringHelper::phoneToNum($return['phone']));
                        if (!$codeSendAnswer['result']) {
                            $error['sms'] = StringResource::ERROR_SEND_SMS_MESSAGE;
                        } else {
                            $print_r = $codeExist;
                        }
                    } else {

                        $registerPossible = true;
                        $userSmsCode = $post->userSmsCode;
                        $userName = null == $post->userName ? '' : $post->userName;
                        $userMail = $post->userMail;

                        if (!$userSmsCode or $userSmsCode != $codeExist) {
                            $registerPossible = false;
                            unset($userAutSession['smscode']);
                            $error['smscode'] = StringResource::ERROR_SEND_SMS_CODE_MESSAGE;
                        } else {
                            $userAutSession['smscode'] = $userSmsCode;
                        }

                        if (strlen($userName) > 1) {
                            $userAutSession['username'] = $userName;
                        } else {
                            $registerPossible = false;
                            unset($userAutSession['username']);
                            $error['username'] = StringResource::ERROR_SEND_USERNAME_MESSAGE;
                        }

                        if ($this->testEmail($userMail)) {
                            $userAutSession['usermail'] = $userMail;
                        } else {
                            $registerPossible = false;
                            unset($userAutSession['usermail']);
                            $error['usermail'] = StringResource::ERROR_SEND_EMAIL_MESSAGE;
                        }/* */
                        if ($registerPossible) {

                            //$error["1c"] = "!!!";  
                            //$print_r = 
                            $paramsFor1c = [
                                'name' => $userName,
                                'phone' => StringHelper::phoneToNum($return['phone']),
                                'email' => $userMail,
                            ];

                            //$print_r =  $user_Id = $container->userIdentity;  
                            $answer = $this->externalCommunicationService->setClientInfo($paramsFor1c);

                            if (!$answer["result"]) {
                                $error["1c"] = $answer['errorDescription'] . "!!!";
                            } else {

                                $error["1c"] = $answer['id'] . "!!!";
                                $userId = $container->userIdentity;
                                $print_r = $newUser = $this->userRepository->findFirstOrDefault(["id" => $userId]);
                                $newUser->setId($container->userIdentity);
                                $newUser->setName($userName);
                                $newUser->setUserId($answer['id']);
                                $print_r = $newUser->setPhone(StringHelper::phoneToNum($return['phone']));
                                $this->userRepository->persist($newUser, ['id' => $userId]);

                                unset($container->userAutSession);
                                unset($container->userPhoneIdentity);

                                return new JsonModel(["reload" => true]);
                            }
                        }
                    }
                }
            }
            //$container->userAutTmpSession = $userAutSession;
        }
        //$return['post'] = $post;
        $container->userAutTmpSession = $userAutSession;

        $view = new ViewModel([
            //'reloadPage' => $reloadPage,
            'printr' => "<pre>" . print_r($print_r, true) . "</pre>",
            'title' => $title,
            'buttonLable' => $buttonLable,
            'error' => $error,
            'sengingPhoneFormated' => $return['phone'],
            'sengingPhone' => StringHelper::phoneToNum($return['phone']),
            'passBlock' => $passBlock,
            'UserBlock' => $UserBlock,
            'CodeBlock' => $CodeBlock,
            'stepOne' => $stepOne,
            'user' => $userAutSession,
            'passForgetBlock' => $passForgetBlock,
                /* 'userName'   => $userAutSession['username'],
                  'userMail'   => $userAutSession[''], */
        ]);
        $view->setTemplate('application/common/auth-form-in-modal');
        return $view->setTerminal(true);
    }

}
