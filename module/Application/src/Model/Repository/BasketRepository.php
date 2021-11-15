<?php

// src/Model/Repository/BasketRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Json\Json;
//use Laminas\Json\Exception\RuntimeException as LaminasJsonRuntimeException;
use Laminas\Db\Sql\Sql;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\Basket;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;

class BasketRepository extends Repository implements BasketRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "basket";

    /**
     * @var Basket
     */
    protected Basket $prototype;
    
    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param Basket $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            Basket $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }
    
    
      /**
     * Update basket
     * @param array $param 
     */
    public function update($param)
    {
        //$sql = new Sql($this->db);
        $sql = new Sql($this->db);
        $update = $sql->update()->table($this->tableName)->set($param["set"])->where($param["where"]); 
        $sqlString = $sql->buildSqlString($update);
        return $sqlString;

        try {
            $this->db->query( $sqlString, $this->db::QUERY_MODE_EXECUTE);
            return ['result' => true, 'description' => '', 'statusCode' => 200];
        } catch (InvalidQueryException $e) {
            return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
        }
        //return $sqlString;
    }

}
