<?php
// src/Command/Factory/FetchImagesCommandFactory.php

namespace Application\Command\Factory;

use Interop\Container\ContainerInterface;
//use Application\Command\FetchImagesCommand;
//use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Adapter\Adapter;
use Laminas\ServiceManager\Factory\FactoryInterface;

class FetchImagesCommandFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof FetchImagesCommand){
            throw new \Exception("not instanceof FetchImagesCommand");
        }
        
        //$adapter = $container->get(AdapterInterface::class);
        $adapter = $container->get('Application\Db\WriteAdapter');
//        $adapter = new Adapter([
//            'driver'   => 'Pdo_Mysql',
//            'database' => 'saychas_z',
//            'username' => 'saychas_z',
//            'password' => 'saychas_z',
//        ]);
        
        return new $requestedName( // new FetchImagesCommand(
            $adapter,
            $requestedName
        );
    }
}