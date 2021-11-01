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
use Application\Resource\Resource;
use Laminas\Log\Logger;
use Laminas\Log\Writer\Stream as StreamWriter;
use Laminas\Session\Container; // as SessionContainer;
use Application\Service\ExternalCommunicationService;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\User;
use ControlPanel\Service\EntityManager;
//use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\Entity\Setting;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;
use Application\Helper\ArrayHelper;
use Application\Helper\StringHelper;
use Laminas\View\Model\ViewModel;
use Laminas\Db\Sql\Sql;
use Laminas\Json\Json;
use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;

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
    private $db;

    /**
     * @var Application\Adapter\Auth\UserAuthAdapter
     */
//    private $userAdapter;

    /**
     * @var Application\Service\ExternalCommunicationService
     */
    private $externalCommunicationService;

    /**
     * @var EntityManager
     */
    private EntityManager $entityManager;

//    private $sessionContainer;

    /**
     * @var Laminas\Log\Logger
     */
    private $logger;

    /**
     * @var CommonHelperFunctions
     */
    private $commonHelperFuncions;

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
            /* $config, */ $authService, $db, /* $userAdapter, */ $externalCommunicationService/* , $sessionContainer */, $commonHelperFunctions, EntityManager $entityManager)
    {
        $this->userRepository = $userRepository;
//        $this->config = $config;
        $this->authService = $authService;

//        $this->sessionContainer = $sessionContainer;
        $this->db = $db;
//        $this->userAdapter = $userAdapter;
        $this->externalCommunicationService = $externalCommunicationService;

        $this->entityManager = $entityManager;

        $this->commonHelperFuncions = $commonHelperFunctions;

        $this->logger = new Logger();
        $writer = new StreamWriter('php://output');
        $this->logger->addWriter($writer);

        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(User::class);
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
        //$container = $this->sessionContainer;// new Container(Resource::SESSION_NAMESPACE);
        $container = new Container(Resource::SESSION_NAMESPACE);
        unset($container->userIdentity);
        if ($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        $this->getResponse()->setStatusCode(301);
        return $this->redirect()->toRoute('home');
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
        $validator = new \Laminas\Validator\EmailAddress();
        /* return (filter_var($email, FILTER_VALIDATE_EMAIL)); */
        return ($validator->isValid($email));
    }

    private function testPassw($pass)
    {
        if (!$pass or!trim($pass)){
            return false;
        }
        if (strlen($pass) < 6){
            return false;
        }

        //$validator = new \Laminas\Validator\Regex(['pattern' => '/^(?=.*\d)(?=.*[a-Z])[0-9a-Z]{6,}$/']);
        $validator = new \Laminas\Validator\Regex(['pattern' => '/^[a-zA-Z0-9]*$/']);
        /* (/^
          (?=.*\d)                //should contain at least one digit
          (?=.*[a-z])             //should contain at least one lower case
          (?=.*[A-Z])             //should contain at least one upper case
          [a-zA-Z0-9]{6,}         //should contain at least 6 from the mentioned characters
          $/) */
        return $validator->isValid($pass);
    }

    /**
     * Generate registration code
     *
     * @param string $phone
     * @param int $length
     * @return string
     */
    private function generateRegistrationCode($phone, $length = 4)
    {
        $code = "";
        $lenght = ($lenght < 1 and $lenght > 9) ? $lenght : 4;
        $digits = [1, 3, 4, 5, 6, 7, 8, 9];
        shuffle($digits);

        for ($i = 0; $i < $length; $i++) {
            $code .= $digits[$i];
        }

        $container = new Container(Resource::SESSION_NAMESPACE);
        $container->userPhoneIdentity = ['phone' => $phone, 'code' => $code, 'live' => (time() + 60)];
        return $code;
    }

    /**
     * Call to phone number
     *
     * @param int $phone
     * @return array
     */
    private function sendSms($phone)
    {
        $code = $this->generateRegistrationCode($phone);
        $answer = $this->externalCommunicationService->sendRegistrationSms($phone, $code);
        return $answer;
    }

    /**
     * Call to phone number
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
     * Get final bill for client order and confirm payment
     *
     * @return Json
     */
    public function getOrderBillAction()
    {
        //$post[] = $this->getRequest()->getPost()->toArray();
        $post["1C"] = Json::decode(file_get_contents('php://input'), Json::TYPE_ARRAY);
        $orderId = $post["OrderId"];
        //$order = ClientOrder::find(['order_id' => $orderId]);
//        $userId = $order->getUserId();
//        $user = User::find(["id" => $userId]);
        $post["User"] = $userInfo = $this->commonHelperFuncions->getUserInfo();
        mail("d.sizov@saychas.ru", "confirm_payment_$orderId.log", print_r($post, true)); // лог на почту
        $response = $this->getResponse();
        $response->setStatusCode(Response::STATUS_CODE_200);
        $answer = ['result' => true, 'description' => 'ok'];

        return new JsonModel($answer);
    }

    /**
     * Send Basket Data Action
     *
     * @return JsonModel
     */
    public function sendBasketDataAction()
    {
        $content = $this->getRequest()->getPost()->toArray();
        //return new JsonModel($content);
        $userId = $this->identity();
        $param = (!empty($delivery_params = Setting::find(['id' => 'delivery_params']))) ? Json::decode($delivery_params->getValue(), Json::TYPE_ARRAY) : [];
        $orderset = $this->externalCommunicationService->sendBasketData($content, $param);
        if (!$orderset['response']['result']) {
            return new JsonModel(["result" => false, "description" => $orderset['response']['errorDescription']]);
        }
        $orderId = $orderset['response']['order_id'];
        $order = ClientOrder::findFirstOrDefault(['order_id' => $orderId]);
        $orderCreate = $this->externalCommunicationService->createClientOrder($orderset, $order, $userId);
        if (!$orderCreate['result']) {
            return new JsonModel(["result" => false, "description" => $orderCreate['description']]);
        }
        $basketSet = \Application\Model\Entity\Basket::findAll(['where' => ['product_id' => $orderCreate['products'], 'user_id' => $userId, 'order_id' => 0]]);
        foreach ($basketSet as $basket) {
            $basket->setOrderId($orderId);
            $basket->persist(['product_id' => $basket->getProductId(), 'user_id' => $basket->getUserId(), 'order_id' => 0]);
        }

        return new JsonModel(["result" => true, "orderId" => $orderId]);
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
        //$container = $this->sessionContainer;// new Container(Resource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        //$container = new Container(Resource::CODE_CONFIRMATION_SESSION_NAMESPACE);
        $container = new Container(Resource::SESSION_NAMESPACE);
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
        $container = new Container (Resource::SESSION_NAMESPACE);
        $userAutSession = (!empty($container->userAutSession)) ? $container->userAutSession : [];
        $return['title'] = $title = Resource::MESSAGE_ENTER_OR_REGISTER_TITLE;
        $return['buttonLable'] = $buttonLable = Resource::BUTTON_LABLE_CONTINUE;
        $post = $this->getRequest()->getPost();

        /* if ($post->recall == '1' ){
          unset($container->userPhoneIdentity);
          return $this->userModalView($return);
          } */

        if (!empty($goStepOne = $post->goStepOne)) {
            unset($container->userAutSession);
            // unset($container->userPhoneIdentity);
            return $this->userModalView($return);
        }

        $return['sendingPhoneFormated'] = $return['phone'] = (!empty($post->userPhone)) ? $post->userPhone : $userAutSession['phone'];
        $return['sendingPhone'] = ($return['phone']) ? StringHelper::phoneToNum($return['phone']) : "";

        if (!$userAutSession['phone']  and (empty($return['sendingPhone']) or strlen($return['sendingPhone']) < 11)) {
            $return['error']['phone'] = Resource::ERROR_INPUT_PHONE_MESSAGE;
            return $this->userModalView($return);
        }
        $user = $this->userRepository->findFirstOrDefault(["phone" => $return['sendingPhone']]);
        $userId = $user->getUserId();
        $return['title'] =  (!empty($userId)) ? $title = Resource::USER_LABLE_HELLO . $user->getName() : Resource::MESSAGE_REGISTER_TITLE;
        $userAutSession['phone'] = $return['sendingPhoneFormated'];
        $container->userAutSession = $userAutSession;
        $return['stepOne'] = $return['CodeBlock'] = true;

        if (empty($userAutSession['smscode'])) {
            return $this->userModalSendSms($return);
        }

        if (empty($userAutSession['phoneValid'])){ 
            
            if($userAutSession['smscode'] != $post->userSmsCode) {
                $return['error']['sms'] = !empty($post->userSmsCode) ? Resource::ERROR_SEND_SMS_CODE_MESSAGE : "";
                return $this->userModalView($return);
            } 
            else  {
                $userAutSession['phoneValid'] = true;
                $container->userAutSession = $userAutSession;
            }
           return $this->userModalView($return);
        }  
        
        
        
        return (!empty($userId)) ? $this->userModalAuthorisation($user) : $this->userModalRegistration($return, $user, $post);

    }

    private function userModalAuthorisation($user)
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $container->userIdentity = $user->getId();
        $this->userModalUpdateGeo($user);
        unset($container->userAutSession, $container->userPhoneIdentity);
        return new JsonModel(["reload" => true]);
    }

    private function userModalRegistration($return, $user,  $post)
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userAutSession = ($container->userAutSession) ? $container->userAutSession : [];

        $return['CodeBlock'] = false;
        $return['UserBlock'] = true;

        if ($post->userblok_post != "1") {
            return $this->userModalView($return);
        }

//        $userAutSession['username'] = null == $post->userName ? '' : $post->userName;
//        $userAutSession['usermail'] = null == $post->userName ? '' : $post->userMail;
        $userAutSession['username'] = $post->userName ?? '';
        $userAutSession['usermail'] = $post->userMail ?? '';
        
        $container->userAutSession = $userAutSession;

        if (empty($userAutSession['username']) or strlen($userAutSession['username']) < 3 ) {
            $return['error']['username'] = Resource::ERROR_SEND_USERNAME_MESSAGE;
            return $this->userModalView($return);
        }

        if (!$this->testEmail($userAutSession['usermail'])) {
            $return['error']['usermail'] = Resource::ERROR_SEND_EMAIL_MESSAGE;
            return $this->userModalView($return);
        }

        $params = ['name' => $userAutSession['username'], 'phone' => $return['sendingPhone'], 'email' => $userAutSession['usermail'],];
        $response = $this->externalCommunicationService->setClientInfo($params);
        //$answer = !empty($response) ? $response : ["result" => false, "errorDescription" => "no connection"];
        $answer = $response ?? ["result" => false, "errorDescription" => "no connection"];
        if (!$answer["result"]) {
            $return["error"]["1c"] = $answer['errorDescription'];
            return $this->userModalView($return);
        }

        $userId = $container->userIdentity;
        $newUser = $this->userRepository->findFirstOrDefault(["id" => $userId]);
        $newUser->setId($userId)->setName($userAutSession['username'])->setEmail($userAutSession['usermail'])->setUserId($answer['id'])->setPhone($return['sendingPhone']);
        $newUser->persist(['id' => $userId]);
        $this->userModalUpdateGeo($user);

        unset($container->userAutSession, $container->userPhoneIdentity);
        return new JsonModel(["reload" => true]);
    }

    private function userModalUpdateGeo ($user)
    {
        $userdata = $user->getUserData();
        $userGeodata = ($userdata->count() > 0 ) ? $userdata->current()->getGeodata() : null;
        if (!empty($userGeodata)) {
            $this->commonHelperFuncions->updateLegalStores($userGeodata);
        }
    }

    private function userModalSendSms($return)
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $userAutSession = ($container->userAutSession) ? $container->userAutSession : [];
        $codeSendAnswer = $this->sendSms($return['sendingPhone']);
        //$container->userPhoneIdentity['code'];
        if (!$codeSendAnswer['result']) {
            $return ['error']['sms'] = (!$codeSendAnswer['result']) ? (Resource::ERROR_SEND_SMS_MESSAGE . ":<br> " . $codeSendAnswer['errorDescription'] ) : "";
            return $this->userModalView($return);
        }
        $userAutSession['smscode'] = $container->userPhoneIdentity['code'];
        $container->userAutSession = $userAutSession;
        //exit(print_r($container->userAutSession ));
        return $this->userModalView($return);
    }

    private function userModalView($return)
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return["user"] = $container->userAutSession;
        $view = new ViewModel($return);
        $view->setTemplate('application/common/auth-form-in-modal');
        return $view->setTerminal(true);
    }

}
