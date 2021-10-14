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
use Application\Model\Entity\Setting;
use Application\Model\Entity\Review;
use Application\Model\Entity\ReviewImage;
use Application\Model\Entity\User;
use Application\Model\Entity\ProductRating;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
//use Laminas\Filter\StripTags;
use Laminas\Escaper\Escaper;
use Application\Service\CommonHelperFunctionsService;
use Application\Service\ImageHelperFunctionsService;
use Application\Service\ExternalCommunicationService;
use Laminas\Authentication\AuthenticationService;
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
    private $imageHelperFuncions;

    public function __construct(
            //ProductRatingRepositoryInterface $productRatingRepository,
            ProductRepositoryInterface $productRepository, /**/
            //HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager, $config, AuthenticationService $authService, 
            CommonHelperFunctionsService $commonHelperFuncions,
            ImageHelperFunctionsService $imageHelperFuncions,
            ExternalCommunicationService $externalCommunicationService)
    {
        // $this->productRatingRepository = $productRatingRepository;
        $this->productRepository = $productRepository;
        //  $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->authService = $authService;
        $this->commonHelperFuncions = $commonHelperFuncions;
        $this->imageHelperFuncions = $imageHelperFuncions;
        $this->externalCommunicationService = $externalCommunicationService;
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(Review::class);
        $this->entityManager->initRepository(ReviewImage::class);
        $this->entityManager->initRepository(User::class);
        $this->entityManager->initRepository(ProductRating::class);
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

       $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $param['user_id']]));

        if (empty($userInfo['phone'])) {
            return $this->getResponse()->setStatusCode(403);
        }
 
        $param['rating'] = $this->getValidRating($this->getRequest()->getPost()->rating);

        return new JsonModel($this->productRepository->setProductRating($param));
    }

    /**
     * set product review
     *
     * @param POST and  FILES data
     * @return JSON
     */
    public function setProductReviewAction()
    {
        $reviewId = substr(md5(uniqid().time()), 0 , 36);
        $return = [ "seller_name" => "", "seller_message" => "", "time_created" => time(), "time_modified" => null, 'id' => $reviewId ];
        $escaper = new Escaper();

        if (empty($return['productId'] = $this->getRequest()->getPost()->productId)) {
            return $this->getResponse()->setStatusCode(404);
        }

        if (empty($user_id = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }

        if (empty($return["user_message"] = $escaper->escapeHtml(trim($this->getRequest()->getPost()->reviewMessage))) or strlen($return["user_message"]) < Resource::REVIEW_MESSAGE_VALID_MIN_LENGHT) {
            return new JsonModel(["result" => false, "description" => Resource::REVIEW_MESSAGE_VALID_ERROR ]);
        }

        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $user_id ]));

        if (empty($return["user_name"] = $userInfo['name']) or empty($return["user_id"] = $userInfo['userid'])) {
            return $this->getResponse()->setStatusCode(403);
        }
        
        $return["rating"] = $this->getValidRating($this->getRequest()->getPost()->rating);
        //$return['id'] = $reviewId = $this->addReview($return);
        $files = $this->getRequest()->getFiles();

        if (!empty($files['files'])) {

            if (!$this->imageHelperFuncions->getValidPostImage($files['files'])) {
                return new JsonModel(["result" => false, "description" => Resource::LEGAL_IMAGE_NOTICE . ": " . join(", ", Resource::LEGAL_IMAGE_TYPES)]);
            }

            $return['images'] = $this->addReviewImage($files['files'], $reviewId);
        }

        $return['answer1c'] = $this->externalCommunicationService->sendReview($return);

        return new JsonModel($return);
    }

    /**
     * get product reviews
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
  
        $res = Review::findAll(['where' => $param, 'order' => 'time_created desc', "limit" => "10" ])->toArray();
        $reviews['statistic'] = $this->productRepository->getCountsProductRating($param['product_id']);
        $reviews['overage_rating'] = ProductRating::findFirstOrDefault(['product_id'=>$param['product_id']])->getRating();
        $reviews['images_path'] = $this->imagePath("review_images");
        $reviews['thumbnails_path'] = $this->imagePath("review_thumbnails");
        $reviews["reviews"] = [];
        
        foreach ($res as $review) {
            $review['time_created'] = date("Y-m-d H:i:s", (int) $review['time_created']);
            $review['images'] = $this->getReviewImages($review['id']);
            $reviews["reviews"][] = $review;
        }
        
        return new JsonModel($reviews);
    }

    /**
     * receive product reviews from 1c
     *
     * @param input JSON
     * @return JSON
     */
    public function receiveReviewAction ()
    {
        $json = file_get_contents('php://input');
        $return = (!empty($json)) ? Json::decode($json, Json::TYPE_ARRAY) : [];
        
         mail("d.sizov@saychas.ru", "1C.Review.log", print_r($return, true)); // лог на почту*/
        
        return new JsonModel($return);
    }
    
    /**
     * receive product rating from 1c
     *
     * @param input JSON
     * @return JSON
     */
    public function receiveRatingAction ()
    {
        $json = file_get_contents('php://input');
        $return = (!empty($json)) ? Json::decode($json, Json::TYPE_ARRAY) : [];
        
         mail("d.sizov@saychas.ru", "1C.Rating.log", print_r($return, true)); // лог на почту*/
        
        return new JsonModel($return);
    }
    
    
    
    /**
     * get images of review
     *
     * @param int $reviewId
     * @return array
     */
    private function getReviewImages($reviewId)
    {
        $images = ReviewImage::findAll(['where' => ['review_id' => $reviewId]]);
        foreach ($images as $image) {
            $return[] = $image->getFilename();
        }
        return $return;
    }

    /**
     *
     * @param int $rating
     * @return int
     */
    private function getValidRating($rating)
    {
        $patternRating = Resource::PRODUCT_RATING_VALUES;
        //$rating = $this->getRequest()->getPost()->rating;
        return !empty($patternRating[$rating]) ? $patternRating[$rating] : end($patternRating);
    }

    /**
     * check valid post image files
     *
     * @param array $files
     * @return boolean
     */
//    
//   /***  moved to ImagesHelperService */
//    
//    private function getValidPostImage($files)
//    {
//        foreach ($files as $file) {
//            if (!in_array($file['type'], Resource::LEGAL_IMAGE_TYPES)) {
//                return false;
//            }
//        }
//        return true;
//    }
//    

    /**
     *
     * @param array $files
     * @param int $reviewId
     * @return array
     */
    private function addReviewImage($files, $reviewId)
    {
        $images = [];
        $uploadPath = "public" . $this->imagePath("review_images") . "/";
        $uploadPathThumbs = "public" . $this->imagePath("review_thumbnails") . "/";
        $resizeParams = Resource::REVIEW_IMAGE_RESIZE;  
        $thumpParams = Resource::REVIEW_IMAGE_THUMBNAILS;  
        $funcView = ($resizeParams["crop"]) ? "cropImage" : "resizeImage";
        $funcThumb = ($thumpParams["crop"]) ? "cropImage" : "resizeImage";
        foreach ($files as $file) {
            $uuid = uniqid($this->identity() . "_" . time(), false);
            $filename = $uuid ;
             
            //if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
            if ($this->imageHelperFuncions->$funcView($file['tmp_name'],  $uploadPath . $filename, $resizeParams['width'], $resizeParams['height'], $resizeParams['type'],)){
                $this->imageHelperFuncions->$funcThumb($file['tmp_name'],  $uploadPathThumbs . $filename, $thumpParams['width'], $thumpParams['height'], $thumpParams['type'],);
//                $reviewImage = ReviewImage::findFirstOrDefault(["id" => null]);
//                $reviewImage->setReviewId($reviewId)->setFilename($filename.".".$resizeParams['type'])->persist(["id" => null]);
                $images['view'][] = $this->imagePath("review_images") . "/". $filename.".".$resizeParams['type'];
                $images['thumbs'][] = $this->imagePath("review_thumbnails") . "/". $filename.".".$thumpParams['type'];
            }
        }

        return $images;
    }

    /**
     * return insert id
     *
     * @param array $param
     * @return int
     */
    private function addReview($param)
    {
        $review = Review::findFirstOrDefault(["id" => $param['id']]);
        
        return $review->setProductId($param['productId'])
                        ->setRating($param["rating"])
                        ->setUserId($param['user_id'])
                        ->setReviewId($param['review_id'])
                        ->setUserName($param["user_name"])
                        ->setUserMessage($param['user_message'])
                        ->setSellerName($param["seller_name"])
                        ->setSellerMessage($param['seller_message'])
                        ->setTimeCreated($param['time_created'])
                        ->setTimeModified($param['time_modified'])
                        ->persist(["id" => $param['id']]);
    }

}
 
