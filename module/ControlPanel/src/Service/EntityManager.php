<?php

// ControlPanel\src\Service\EntityManager.php

namespace ControlPanel\Service;

use Interop\Container\ContainerInterface;

/**
 * Description of EntityManager
 *
 * @author alex
 */
final class EntityManager
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRepository(string $entityClassName)
    {
        if ( !( get_parent_class($entityClassName) == \Application\Model\Entity\Entity::class ) ) {
            throw new \Exception("not instanceof Entity. ".'Cannot find repository for '.$entityClassName.' Perhaps you need to record it in your module.config.php');
        }
        $repository = $this->container->get($entityClassName);
        
        return $repository;
    }

    public function initRepository(string $entityClassName)
    {
        if ( !( get_parent_class($entityClassName) == \Application\Model\Entity\Entity::class ) ) {
            throw new \Exception("not instanceof Entity. ".'Cannot find repository for '.$entityClassName.' Perhaps you need to record it in your module.config.php');
        }
        $entityClassName::$repository = $this->container->get($entityClassName);
    }
}
