<?php

// Application\src\Model\Repository\Factory\DeliveryRepositoryFactory.php

namespace Application\Model\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Application\Model\Entity\Delivery;
use Application\Model\Repository\DeliveryRepository;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Hydrator\ClassMethodsHydrator;

/**
 * Description of DeliveryRepositoryFactory
 *
 * @author alex
 */
class DeliveryRepositoryFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if ($requestedName instanceof DeliveryRepository) {
            throw new Exception("not instanceof DeliveryRepository");
        }
        
        $adapter = $container->get(AdapterInterface::class);
        
        $hydrator = new ClassMethodsHydrator();
        
        $composite = new \Laminas\Hydrator\Filter\FilterComposite();
        $composite->addFilter(
                'excludeid',
                new \Laminas\Hydrator\Filter\MethodMatchFilter('getId'),
                \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
        );
        $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
            
        $prototype = new Delivery;

        return new DeliveryRepository(
                $adapter,
                $hydrator,
                $prototype
        );
    }

}
