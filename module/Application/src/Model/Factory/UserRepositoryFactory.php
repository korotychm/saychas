<?php
// src/Model/Factory/UserRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\User;
use Application\Model\Entity\Entity;
use Application\Model\Repository\UserRepository;
use Application\Model\Repository\PostRepository;
use Laminas\Hydrator\Aggregate\HydrateEvent;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\EventManager;
// use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserRepositoryFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof UserRepository){
            throw new Exception("not instanceof UserRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        $postRepository = $container->get(PostRepository::class);
        
        $cache             = new \Laminas\Cache\Storage\Adapter\Memory;// new Memory();
        
        $cacheReadListener = function (HydrateEvent $event) use ($cache) {
//            $object = $event->getExtractionObject();
//
//            if (!$object instanceof BlogPost) {
//                return;
//            }
//
//            if ($cache->hasItem($object->getId())) {
//                $event->setExtractedData($cache->getItem($object->getId()));
//                $event->stopPropagation();
//            }
        };

        $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();
//        $hydrator->getEventManager()->attach(HydrateEvent::EVENT_HYDRATE, $cacheReadListener, 1000);
        
        return new UserRepository(
            $adapter,
            $hydrator,//new ReflectionHydrator(),
            new User($postRepository) // new User('', '', '', '')
        );
    }
}