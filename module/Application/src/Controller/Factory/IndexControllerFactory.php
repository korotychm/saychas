<?php
namespace Application\Controller\Factory;

use Interop\Container\ContainerInterface;
//use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Controller\IndexController;

/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller.
 */
class IndexControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestName, array $options = null)
    {
        //$cont = null == $container->get(\Application\Model\TestRepositoryInterface::class) ? 'null' : 'not null';
        
        // $adapterManager = $container->get(Laminas\Db\Adapter\Adapter::class);
        //$adapter = $container->get('Application\Db\Writeable');
        
        // Instantiate the controller and inject dependencies
        $test = $container->get(/**\Application\Model\*/TestRepositoryInterface::class);
        $category = $container->get(CategoryRepositoryInterface::class);
        $provider = $container->get(ProviderRepositoryInterface::class);
        $store =    $container->get(StoreRepositoryInterface::class);
        return new IndexController($test, $category, $provider, $store);
    }
}

