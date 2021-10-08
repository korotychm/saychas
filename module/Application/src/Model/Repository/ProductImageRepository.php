<?php

// src/Model/Repository/StockBalanceRepository.php

namespace Application\Model\Repository;

// Replace the import of the Reflection hydrator with this:
use Laminas\Hydrator\HydratorInterface;
use Laminas\Db\Adapter\AdapterInterface;
//use Laminas\Json\Json;
//use Laminas\Db\Adapter\Exception\InvalidQueryException;
//use Laminas\Db\Sql\Select;
//use Laminas\Db\Sql\Where;
use Application\Model\Entity\ProductImage;
use Application\Model\RepositoryInterface\ProductImageRepositoryInterface;
use Exception;

class ProductImageRepository extends Repository implements ProductImageRepositoryInterface
{

    /**
     * @var string
     */
    protected $tableName = "product_image";

    /**
     * @var ProductImage
     */
    protected ProductImage $prototype;

    /**
     * @param AdapterInterface $db
     * @param HydratorInterface $hydrator
     * @param ProductImage $prototype
     */
    public function __construct(
            AdapterInterface $db,
            HydratorInterface $hydrator,
            ProductImage $prototype
    )
    {
        $this->db = $db;
        $this->hydrator = $hydrator;
        $this->prototype = $prototype;
    }

    /**
     * Fetches images from remote ftp server
     * @param array $images
     * @throws Exception
     */
    public function fetch(array $images)
    {
        $ftp_server = "nas01.saychas.office";
        $username = "1C";
        $password = "ree7EC2A";

        // perform connection
        $conn_id = ftp_connect($ftp_server);
        $login_result = ftp_login($conn_id, $username, $password);
        if ((!$conn_id) || (!$login_result)) {
            throw new \Exception('FTP connection has failed! Attempted to connect to nas01.saychas.office for user ' . $username . '.');
        }

        foreach ($images as $image) {
            $local_file = realpath($this->catalogToSaveImages) . "/" . $image;
            $server_file = "/1CMEDIA/PhotoTovarov/" . $image;

            // trying to download $server_file and save it to $local_file
            if (!ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
                throw new \Exception('Could not complete the operation');
            }
        }
        // close connection
        ftp_close($conn_id);
    }

    /**
     * Fetches all images for array of products
     * @param array $products
     * @return array
     */
    public function fetchAll(array $products): array
    {
        /** @var Product[] */
        foreach ($products as $p) {
            try {
                /** returns array of successfully downloaded images */
                $this->fetch($p->images);
            } catch (\Exception $e) {
                return ['result' => false, 'description' => $e->getMessage(), 'statusCode' => 400];
            }
        }
        return [];
    }

    /**
     * Adds given product image into it's repository
     *
     * @param json
     */
    public function replace($data)
    {
        foreach ($data as $product) {
            $s = sprintf("delete from `product_image` where product_id='%s'", $product->id);
            try {
                $q = $this->db->query($s);
                $q->execute();
            } catch (Exception $ex) {
                return ['result' => false, 'description' => "error executing sql statement", 'statusCode' => 418];
            }
                
            foreach ($product->images as $image) {
                $sql = sprintf("replace INTO `product_image`(`product_id`, `ftp_url`, `http_url`, `sort_order`) VALUES ( '%s', '%s', '%s', %u )",
                        $product->id, $image, $image, 0);
                try {
                    $query = $this->db->query($sql);
                    $query->execute();
                } catch (Exception $e) {
                    return ['result' => false, 'description' => "error executing sql statement", 'statusCode' => 418];
                }
            }
        }
        return ['result' => true, 'description' => '', 'statusCode' => 200];
    }

    /**
     * Delete product images specified by json array of objects
     * @param json
     */
    public function delete($json)
    {
        return ['result' => false, 'description' => 'Method is not supported: cannot delete product image', 'statusCode' => 405];
    }

}
