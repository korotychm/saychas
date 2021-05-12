<?php

// src/Model/Factory/PostRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Post;
use Application\Model\Repository\PostRepository;
use Laminas\Db\Adapter\AdapterInterface;
// use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class PostRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof PostRepository) {
            throw new Exception("not instanceof PostRepository");
        }

        $adapter = $container->get(AdapterInterface::class);

        return new PostRepository(
                $adapter,
                new \Laminas\Hydrator\ClassMethodsHydrator(),
                new Post
        );
    }

}
