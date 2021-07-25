<?php
namespace ControlPanel\Service\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\Session\SessionManager;
use Laminas\Authentication\Storage\Session as SessionStorage;
use ControlPanel\Service\AuthAdapter;

/**
 * The factory responsible for creating of authentication service.
 */
class AuthenticationServiceFactory implements FactoryInterface
{
    /**
     * This method creates the Laminas\Authentication\AuthenticationService service 
     * and returns its instance. 
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

//$config = new StandardConfig();
//$config->setOptions([
//    'remember_me_seconds' => 1800,
//    'name'                => 'laminas',
//]);
//$manager = new SessionManager($config);

        $sessionManager = $container->get(SessionManager::class);
        $config = $sessionManager->getConfig();
//        $config->setRememberMeSeconds(1);
        $config->setCookieLifeTime(60*60*24*30);
//        echo '<pre>';
//        print_r($config->getCookieLifeTime());
//        echo '</pre>';
//        exit;
        //CONTROL_PANEL_SESSION
        //$authStorage = new SessionStorage('RoleDemo_Auth', 'session', $sessionManager);
        $authStorage = new SessionStorage(\ControlPanel\Resource\StringResource::CONTROL_PANEL_SESSION, 'session', $sessionManager);
        $authAdapter = $container->get(AuthAdapter::class);

        // Create the service and inject dependencies into its constructor.
        return new AuthenticationService($authStorage, $authAdapter);
    }
}

