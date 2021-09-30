<?php

// src/Model/Factory/CategoryRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Application\Model\Entity\Category;
use Application\Model\Repository\CategoryRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CategoryRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof CategoryRepository) {
            throw new Exception("not instanceof CategoryRepository");
        }

        $config = $container->get('Config');

        $adapter = $container->get(AdapterInterface::class);

        $adp = $container->get('Application\Db\WriteAdapter');
        
        $cache = $container->get('FilesystemCache');

        return new CategoryRepository(
                $adapter,
                new ReflectionHydrator(),
                new Category('', 0, 0, null, null),
                $config['parameters']['1c_auth']['username'],
                $config['parameters']['1c_auth']['password'],
                $cache
        );
    }

}
