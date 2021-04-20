<?php
// src/Model/Factory/PostRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Hydrator\UserHydrator;
use Application\Model\Repository\PostRepository;
use Laminas\ServiceManager\Factory\FactoryInterface;


/**
 * Description of UserHydratorFactory
 *
 * @author alex
 */
class UserHydratorFactory implements FactoryInterface
{
   
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if($requestedName instanceof UserHydrator){
            throw new Exception("not instanceof UserHydrator");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        $postRepository =    $container->get(PostRepository::class);
        
        return new UserHydrator($postRepository);
    }
}