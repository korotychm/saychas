<?php
// src/Command/Factory/FetchImagesCommandFactory.php

namespace Application\Command\Factory;

use Interop\Container\ContainerInterface;
//use Application\Model\Repository\UserRepository;
//use Application\Model\Repository\PostRepository;
//use Application\Command\FetchImagesCommand;
//use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CommandFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof $requestedName){
            throw new \Exception("not instanceof ".$requestedName);
        }
        
        //$adapter = $container->get(AdapterInterface::class);
//        $userRepository = $container->get(UserRepository::class);
//        $postRepository = $container->get(PostRepository::class);
        $adapter = $container->get('Application\Db\WriteAdapter');
//        $adapter = new Adapter([
//            'driver'   => 'Pdo_Mysql',
//            'database' => 'saychas_z',
//            'username' => 'saychas_z',
//            'password' => 'saychas_z',
//        ]);
        
        return new $requestedName( // new FetchImagesCommand(
            $adapter,
            $requestedName,
            $container
//            ,
//            $userRepository,
//            $postRepository
        );
    }
}