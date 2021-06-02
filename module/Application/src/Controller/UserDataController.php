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
use Laminas\Session\Container;
use Application\Service\ExternalCommunicationService;
use Laminas\View\Model\JsonModel;
use Laminas\Http\Response;

//use Laminas\Session\SessionManager;
//use Laminas\ServiceManager\Factory\InvokableFactory;
//use Laminas\ServiceManager\ServiceManager;

/**
 * UserDataController
 */
class UserDataController extends AbstractActionController {

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Laminas/Config/Config
     */
    private $config;

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
    private $userAdapter;

    /**
     * @var Application\Service\ExternalCommunicationService
     */
    private $externalCommunicationService;

    /**
     * @var Laminas\Log\Logger
     */
    private $logger;

//    private SessionManager $sessionManager;
//    private ServiceManager $serviceManager;

    /**
     * Constructor
     *
     * @param UserRepository $userRepository
     * @param Config $config
     * @param AuthenticationService $authService
     * @param AdapterInterface $db
     * @param UserAuthAdapter $userAdapter
     * @param ExternalCommunicationService $externalCommunicationService
     */
    public function __construct(
            UserRepository $userRepository,
            $config, $authService, $db, $userAdapter, $externalCommunicationService) {
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

    /**
     * Execute the request
     *
     * @param MvcEvent $e
     * @return Laminas\Http\Response
     */
    public function onDispatch(MvcEvent $e) {
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        return $response;
    }

    /**
     * Clear identity
     *
     * @return Response
     */
    public function clearAction() {
        $container = new Container(StringResource::SESSION_NAMESPACE);
        unset($container->userIdentity);
        if ($this->authService->hasIdentity()) {
            $this->authService->clearIdentity();
        }
        return $this->getResponse();
    }

    /**
     * Generate random code to send
     * to use along with the phone number
     *
     * @param int $phone
     * @return int
     */
    private function generateRegistrationCode($phone) {
        /** @var $phone */
        /* $phone is meant to be a a session key */
        // Generate new code and store it in session
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
    public function sendRegistrationSmsAction() {
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
    public function codeFeedbackAction() {
        // Compare feedback with sent registration code
        $post = $this->getRequest()->getPost();
        $code = $post->code;
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

}
