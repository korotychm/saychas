<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
//use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\Entity\Setting;
use Application\Model\Entity\Review;
use Application\Model\Entity\ReviewImage;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Laminas\Filter\StripTags;
//use Application\Model\Repository\ProductRepository;
//use Application\Model\Entity\HandbookRelatedProduct;
//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Service\CommonHelperFunctionsService;
//use Application\Model\Entity\ProductCharacteristic;
//use Application\Model\Entity\StockBalance;
//use Application\Model\Entity\ProductHistory;
//use Application\Model\Entity\ProductFavorites;
use Laminas\Authentication\AuthenticationService;
//use Laminas\Json\Json;
//use Laminas\Http\Response;
//use Laminas\Db\Sql\Where;
//use Application\Helper\ArrayHelper;
use Application\Resource\Resource;

class ReviewController extends AbstractActionController
{

    //private $categoryRepository;
    private $productRepository;
    //private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    private $authService;
    private $commonHelperFuncions;

    public function __construct(
            //ProductRatingRepositoryInterface $productRatingRepository,
            ProductRepositoryInterface $productRepository, /**/
            //HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager, $config, AuthenticationService $authService, CommonHelperFunctionsService $commonHelperFuncions)
    {
       // $this->productRatingRepository = $productRatingRepository;
        $this->productRepository = $productRepository;
        //  $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->authService = $authService;
        $this->commonHelperFuncions = $commonHelperFuncions;
        $this->entityManager->initRepository(Setting::class);
//        $this->entityManager->initRepository(ProductCharacteristic::class);
//        $this->entityManager->initRepository(StockBalance::class);
//        $this->entityManager->initRepository(ProductHistory::class);
//        $this->entityManager->initRepository(ProductFavorites::class);
        $this->entityManager->initRepository(Review::class);
        $this->entityManager->initRepository(ReviewImage::class);
    }

    /**
     * set product_rating and product_user_rating
     *
     * @param POST productId  rating
     * @return JSON
     */
    public function setProductRatingAction()
    {
        if (empty($param['user_id'] = $this->identity()) or empty($param['product_id'] = $this->getRequest()->getPost()->productId)) {
            return $this->getResponse()->setStatusCode(403);
        }

        $patternRating = Resource::PRODUCT_RATING_VALUES;
        $rating = $this->getRequest()->getPost()->rating;
        $param['rating'] = !empty($patternRating[$rating]) ? $patternRating[$rating] : end($patternRating);

        return new JsonModel($this->productRepository->setProductRating($param));
    }

    /**
     * set product review
     *
     * @param POST and  FILES data
     * @return JSON
     */
    public function  setProductReviewAction()
    {
        $return = ["result"=>false, "description" => "Post error"];
        $htmlfilter = new HtmlEntities();
        $return["post"] = $this->getRequest()->getPost();
        $return["post"]["reviewMessage"]  =   $htmlfilter->filter($return["post"]["reviewMessage"] );
        
        $return["files"] = (!empty($files = $this->getRequest()->getFiles()))  ? $files['files']: [];
        return new JsonModel($return);
    }
    
    /**
     * get product review
     *
     * @param POST productId
     * @return JSON
     */
    public function getProductReviewAction()
    {
        if (empty($this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }
      
        if (empty($param['product_id'] = $this->getRequest()->getPost()->productId)) {
            return ['result' => false, 'description' => "product_id not set"];
        }
        
        $reviews["reviews"] = [];
        $res = Review::findAll(['where' => $param])->toArray();
        $reviews['statistic'] = $this->productRepository->getCountsProductRating($param['product_id']); 

        foreach ($res as $review) {
            $review['time_created'] = date("Y-m-d H:i:s", (int)$review['time_created']);
            $review['images'] = $this->getReviewImages($review['id']); 
            $reviews["reviews"][] = $review;
        }
        
        return new JsonModel($reviews);
    }
    
    /**
     * get images of review
     *
     * @param int $reviewId
     * @return array
     */
    private function getReviewImages($reviewId) 
    {
        $images = ReviewImage::findAll(['where' => ['review_id' => $reviewId]])->toArray();
        foreach ($images as $image){
            $return[] = $image['filename'];
        }
        return $return;
    }

}
