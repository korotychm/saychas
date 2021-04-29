<?php

// src/Model/Repository/BrandRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
//use Laminas\Db\Adapter\Driver\ResultInterface;
//use Laminas\Db\ResultSet\HydratingResultSet;
//use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\User;
//use Application\Model\Entity\Post;
use Application\Model\RepositoryInterface\RepositoryInterface;

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

    /**
     * Persists User
     * 
     * @param User $user
     * @return void
     */
    public function persist($user)
    {
        $u = $this->find(['id' => $user->getId()]);
        $parameters = [':name' => $user->getName(), ':phone' => $user->getPhone(), ':email' => $user->getEmail(), ':address' => $user->getAddress(), ':geodata' => $user->getGeodata()/*, ':id'=>$user->getId()*/];
        if(empty($u)) {
            $statement = $this->db->createStatement("insert INTO `{$this->tableName}`( `name`, `phone`, `email`, `address`, `geodata`) VALUES ( :name, :phone, :email, :address, :geodata )");
        }else{
            $parameters[':id'] = $user->getId();
            $statement = $this->db->createStatement("update `{$this->tableName}` set name = :name, phone = :phone, email = :email, address = :address, geodata = :geodata where id = :id ");
        }
        try {
            $statement->prepare();
            $statement->execute($parameters);
        }catch(InvalidQueryException $e){
            print_r($e->getMessage());
            return ['result' => false, 'description' => "error executing $sql".' '.$e->getMessage(), 'statusCode' => 418];
        }
    }

//    /**
//     * Persists User
//     * 
//     * @param User $user
//     * @return void
//     */
//    public function persist($user)
//    {
//        $u = $this->find(['id' => $user->getId()]);
//        if(empty($u)) {
//            $sql = sprintf("insert INTO `{$this->tableName}`( `name`, `phone`, `email`, `address`, `geodata`) VALUES ( '%s', %u, '%s', '%s', '%s' )",
//                    $user->getName(), $user->getPhone(), $user->getEmail(), $user->getAddress(), $user->getGeodata());
//
//        $statement = $this->db->createStatement("insert INTO `{$this->tableName}`( `name`, `phone`, `email`, `address`, `geodata`) VALUES ( :name, :phone, :email, :address, :geodata )");
//        $statement->prepare();
//        $result = $statement->execute([':name' => $user->getName(), ':phone' => $user->getPhone(), ':email' => $user->getEmail(), ':address' => $user->getAddress(), ':geodata' => $user->getGeodata()]);
//        
//        print_r($result);
//        exit;
//            
//        }else{
//            $sql = sprintf("update `{$this->tableName}` set name='%s', phone=%u, email='%s', address='%s', geodata='%s' where id={$user->getId()}",
//                   $user->getName(), $user->getPhone(), $user->getEmail(), $user->getAddress(), $user->getGeodata());
//        }
//        try {
//            $query = $this->db->query($sql);
//            $query->execute();
//        }catch(InvalidQueryException $e){
//            return ['result' => false, 'description' => "error executing $sql".' '.$e->getMessage(), 'statusCode' => 418];
//        }
//    }

}
