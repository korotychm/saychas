<?php

// src/Model/Repository/StockBalanceRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Json\Json;
use Laminas\Db\Adapter\Exception\InvalidQueryException;
use Application\Model\Entity\StockBalance;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;

class StockBalanceRepository extends Repository implements StockBalanceRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "stock_balance";

    /**
     * @var StockBalance
     */
    protected StockBalance $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param StockBalance $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            StockBalance $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
        
        parent::__construct();
    }

    /**
     * Adds given stock balance into it's repository
     *
     * @param json
     */
    public function replace($content)
    {
        try {
            $result = Json::decode($content, \Laminas\Json\Json::TYPE_ARRAY);
        } catch (\Laminas\Json\Exception\RuntimeException $e) {
            return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
        }

        //$this->mclient->saychas->stock_balance->drop();
        $this->mclient->saychas->stock_balance->insertMany($result);
        
        foreach ($result/*['data']*/ as $row) {
            $sql = sprintf("replace INTO `stock_balance`(`product_id`, `size`, `store_id`, `rest`) VALUES ( '%s', '%s', '%s', %u)",
                    $row['product_id'], $row['size'], $row['store_id'], $row['rest']);
            try {
                $query = $this->db->query($sql);
                $query->execute();
            } catch (InvalidQueryException $e) {
                return ['result' => false, 'description' => "error executing $sql", 'statusCode' => 418];
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    /**
     * Delete stock balances specified by json array of objects
     * @param json
     */
    public function delete($json)
    {
        return ['result' => false, 'description' => 'Method is not supported: cannot delete stock balance', 'statusCode' => 405];
    }

}
