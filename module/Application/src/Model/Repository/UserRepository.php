<?php

// src/Model/Repository/UserRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\User;
use Application\Model\RepositoryInterface\RepositoryInterface;
//use Laminas\Db\Sql\Sql;

class UserRepository extends Repository implements RepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "user";

    /**
     * @var User
     */
    protected User $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param User $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            User $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }
    
    public function persist($entity, $params, $hydrator = null)
    {
        if (null == $hydrator) {
            $hydrator = new \Laminas\Hydrator\ClassMethodsHydrator();

            $composite = new \Laminas\Hydrator\Filter\FilterComposite();
            $composite->addFilter(
                    'excludeuserdata',
                    new \Laminas\Hydrator\Filter\MethodMatchFilter('getUserData'),
                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
            );
//            $composite->addFilter(
//                    'excludesettimestamp',
//                    new \Laminas\Hydrator\Filter\MethodMatchFilter('setTimestamp'),
//                    \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND
//            );

            $hydrator->addFilter('excludes', $composite, \Laminas\Hydrator\Filter\FilterComposite::CONDITION_AND);
        }

        return parent::persist($entity, $params, $hydrator);
    }
    

}

/**
     * Persists User
     *
     * $params must be set at all times
     *
     * @param User $user
     * @param array $params
     * @return void
     */
//    public function persist1($user, $params)
//    {
//        $u = $this->find($params);
//        $pars = $this->reflect($user);
//        $parameters = $pars['params'];
//        if (empty($u)) {
//            $parameters = array_diff($parameters, $params);
//            $statement = $this->db->createStatement("insert INTO `{$this->tableName}`( `name`, `phone`, `email`, `address`, `geodata`) VALUES ( :name, :phone, :email, :address, :geodata )");
//        } else {
//            $parameters[':id'] = $user->getId();
//            $statement = $this->db->createStatement("update `{$this->tableName}` set name = :name, phone = :phone, email = :email, address = :address, geodata = :geodata where id = :id ");
//        }
//        try {
//            $statement->prepare();
//            $statement->execute($parameters);
//        } catch (InvalidQueryException $e) {
//            echo $e->getMessage();
//            return ['result' => false, 'description' => "error executing statement. " . ' ' . $e->getMessage(), 'statusCode' => 418];
//        }
//    }
//    private function valueFoundAsKey($value_array, $key_array)
//    {
//        $result = [];
//        foreach ($value_array as $key => $value) {
//            if(array_key_exists($value, $key_array)) {
//                continue;
//            }
//            $result[$key] = $value;
//        }
//        return $result;
//    }







        //$pars = $this->reflect($user);
        //$values = array_values($pars['assoc']);// $pars['values'];
        //$names = $pars['names'];





    /**
     * Converts Entity to associative array
     *
     * @param Entity $entity
     * @return array
     */
//    protected function reflect($entity)
//    {
//        $params = [];
//        $names = [];
//        $assoc = [];
//        $values = [];
//        $reflect = new ReflectionClass($entity);
//        foreach($reflect->getProperties() as $prop) {
//            $p = $reflect->getProperty($prop->getName());
//            $p->setAccessible(true);
//            $methodName = 'set'.$entity->camelize($prop->getName());
//            if(method_exists($entity, $methodName)) {
//                $names[] = $prop->getName();
//                $values[] = $p->getValue($entity);
//                $assoc[$prop->getName()] = $p->getValue($entity);
//            }
//        }
//        return ['params' => $params, 'names' => $names, 'assoc' => $assoc, 'values' => $values];
//    }

