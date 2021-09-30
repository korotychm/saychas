<?php

// Application\src\Model\Repository\Factory\ClientOrderRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\Entity\ClientOrder;
use Application\Model\Repository\ClientOrderRepository;
use Application\Service\AcquiringCommunicationService;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Description of ClientOrderRepositoryFactory
 *
 * @author alex
 */
class ClientOrderRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof ClientOrderRepository) {
            throw new Exception("not instanceof ClientOrderRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        $hydrator = new ClassMethodsHydrator();
        
        $composite = new \Laminas\Hydrator\Filter\FilterComposite();
        $composite->addFilter(
                'excludeid',
                new \Laminas\Hydrator\Filter\MethodMatchFilter('getId'),
                \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
        );
//        $composite->addFilter(
//                'excludechildroles',
//                new \Laminas\Hydrator\Filter\MethodMatchFilter('getChildRoles'),
//                \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
//        );
        $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
            
        $prototype = new ClientOrder;
        
        $acquiringService = $container->get(AcquiringCommunicationService::class);

        return new ClientOrderRepository(
                $adapter,
                $hydrator,
                $prototype,
                $acquiringService
        );
    }

}
