<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Json\Json;
use Laminas\View\Model\JsonModel;
//use Laminas\Session\Container; // as SessionContainer;
use Laminas\Authentication\AuthenticationService;
use Application\Resource\Resource;
use Application\Helper\ArrayHelper;
use Laminas\Escaper\Escaper;
use Application\Service\CommonHelperFunctionsService;
use Application\Service\ImageHelperFunctionsService;
use Application\Service\ExternalCommunicationService;
use Application\Model\Entity\Setting;
use Application\Model\Entity\Review;
use Application\Model\Entity\ReviewImage;
use Application\Model\Entity\User;
use Application\Model\Entity\ProductRating;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;

//use Laminas\Filter\StripTags;


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

        $reviewId = substr(md5(uniqid() . time()), 0, 36);

        $return = ["time_created" => time(), 'id' => $reviewId];

        if (empty($return['productId'] = $this->getRequest()->getPost()->productId)) {
            return $this->getResponse()->setStatusCode(404);
        }

        if (empty($user_id = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }


        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $user_id]));

        if (empty($return["user_name"] = $userInfo['name']) or empty($return["user_id"] = $userInfo['userid'])) {
            return $this->getResponse()->setStatusCode(403);
        }

        $return["rating"] = $this->getValidRating($this->getRequest()->getPost()->rating);
        $return['answer1c'] = $this->externalCommunicationService->sendReview($return);

        return new JsonModel($return);
    }

    /**
     * set product review
     *
     * @param POST and  FILES data
     * @return JSON
     */
    public function setProductReviewAction()
    {
        $reviewId = substr(md5(uniqid() . time()), 0, 36);
        $return = ["seller_name" => "", "seller_message" => "", "time_created" => time(), "time_modified" => null, 'id' => $reviewId];

        $escaper = new Escaper();

        if (empty($return['productId'] = $this->getRequest()->getPost()->productId)) {
            return $this->getResponse()->setStatusCode(404);
        }

        if (empty($user_id = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }

        if (!empty($return["user_message"] = $escaper->escapeHtml(trim($this->getRequest()->getPost()->reviewMessage))) and strlen($return["user_message"]) < Resource::REVIEW_MESSAGE_VALID_MIN_LENGHT) {
            return new JsonModel(["result" => false, "description" => Resource::REVIEW_MESSAGE_VALID_ERROR]);
        }

        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $user_id]));

        if (empty($return["user_name"] = $userInfo['name']) or empty($return["user_id"] = $userInfo['userid'])) {
            return $this->getResponse()->setStatusCode(403);
        }

        $return["rating"] = $this->getValidRating($this->getRequest()->getPost()->rating);
        //return new JsonModel($this->getRequest()->getPost());

        $files = $this->getRequest()->getFiles();

        if (!empty($files['files'])) {

            if (empty($return["user_message"] = $escaper->escapeHtml(trim($this->getRequest()->getPost()->reviewMessage))) or strlen($return["user_message"]) < Resource::REVIEW_MESSAGE_VALID_MIN_LENGHT) {
                return new JsonModel(["result" => false, "description" => Resource::REVIEW_MESSAGE_VALID_ERROR]);
            }

            if (!$this->imageHelperFuncions->getValidPostImage($files['files'])) {
                return new JsonModel(["result" => false, "description" => Resource::LEGAL_IMAGE_NOTICE . ": " . join(", ", Resource::LEGAL_IMAGE_TYPES)]);
            }

            $return['images'] = $this->addReviewImage($files['files']);
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
        if (empty($userId = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }

        if (empty($param['product_id'] = $this->getRequest()->getPost()->productId)) {
            return ['result' => false, 'description' => "product_id not set"];
        }

//        $page = !empty($this->getRequest()->getPost()->page) ? (int)$this->getRequest()->getPost()->page : 0;
//        $offset = $page * Resource::REVIEWS_PAGING_LIMIT;
//        $limit = $offset + Resource::REVIEWS_PAGING_LIMIT;
//
//        $sortPost = !empty($this->getRequest()->getPost()->sort) ?  (int)$this->getRequest()->getPost()->sort : 0;
//        $sortOrder = Resource::REVIEWS_SORT_ORDER_RATING;
//        $sort = !empty($sortOrder[$sortPost]) ? $sortOrder[$sortPost] : end($sortOrder);
        $reviewParams = $this->setReviewParams($param);

        //return new JsonModel($reviewPaging);
        //$res = Review::findAll(['where' => $param,  "order" => [$reviewParams['order'][0] , "time_created desc"], "limit" => $reviewParams['limit'], "offset" => $reviewParams['offset']])->toArray();
        $res = Review::findAll($reviewParams)->toArray();
        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $userId]));
        $param['user_id'] = $userInfo['userid'];
        $productRating = ProductRating::findFirstOrDefault(['product_id' => $param['product_id']]);
        $r = ["sort" => $reviewParams['order'], 'average_rating' => $productRating->getRating(), "reviews_count" => $productRating->getReviews(), 'images_path' => $this->imagePath("review_images"), 'thumbnails_path' => $this->imagePath("review_thumbnails")];
        $reviews = array_merge($r, ["images" => $this->getProductReviewImages($param['product_id']), "legalImageType" => Resource::LEGAL_IMAGE_TYPES, "limit" => ["limit" => $reviewParams['limit'], "offset" => $reviewParams['offset']], "reviewer" => $this->externalCommunicationService->getReviewer($param), 'statistic' => (!empty($productRating->getStatistic())) ? Json::decode($productRating->getStatistic()) : [], "reviews" => []]);

        foreach ($res as $review) {
            $review['time_created'] = date("Y-m-d H:i:s", (int) $review['time_created']);
            $review['images'] = $this->getReviewImages($review['id']);
            $reviews["reviews"][] = $review;
        }

        return new JsonModel($reviews);
    }

    /**
     * return parameters for SQL query
     *
     * @return array
     */
    private function setReviewParams($param)
    {
        $limit = Resource::REVIEWS_PAGING_LIMIT;
        $sortOrder = Resource::REVIEWS_SORT_ORDER_RATING;
        $post = $this->getRequest()->getPost();
        $page = $post->page ?? 0;
        $offset = $page * $limit;
        $sortPost = $post->sort ?? 0;
        $sort = $sortOrder[$sortPost] ?? current($sortOrder);

        return ['where' => $param, "order" => [$sort, "time_created desc"], "offset" => $offset, "limit" => $limit];
    }

    /**
     * get images of review
     *
     * @param int $reviewId
     * @return array
     */
    private function getReviewImages($reviewId)
    {
        $images = ReviewImage::findAll(['where' => ['review_id' => $reviewId], "order" => ["id desc"]]);
        foreach ($images as $image) {
            $return[] = $image->getFilename();
        }
        return $return;
    }

    /**
     * get images of product reviews
     *
     * @param string $productId
     * @return array
     */
    private function getProductReviewImages($productId)
    {
        $reviews = Review::findAll(["where" => ["product_id" => $productId], "columns" => ["id"]])->toArray();
        $reviewsId = ArrayHelper::extractId($reviews, "id");
        $images = ReviewImage::findAll(['where' => ['review_id' => $reviewsId], "order" => ["id desc"], "limit" => Resource::REVIEWS_IMAGE_GALLARY_LIMIT]);

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
        //$rating = (int)$rating;
        return !empty($patternRating[$rating]) ? $patternRating[$rating] : end($patternRating);
    }

    /**
     * Add user images for review.
     *
     * @param array $files
     * @param int $reviewId
     * @return array
     */
    private function addReviewImage($files)
    {
        
        $uploadPath = "public" . $this->imagePath("review_images") . "/";
        $uploadPathThumbs = "public" . $this->imagePath("review_thumbnails") . "/";
        $resizeParams = Resource::REVIEW_IMAGE_RESIZE;
        $thumpParams = Resource::REVIEW_IMAGE_THUMBNAILS;
        $funcView = ($resizeParams["crop"]) ? "cropImage" : "resizeImage";
        $funcThumb = ($thumpParams["crop"]) ? "cropImage" : "resizeImage";

        foreach ($files as $file) {
            $uuid = uniqid($this->identity() . "_" . time(), false);
            $filename = $uuid;

            if ($this->imageHelperFuncions->$funcView($file['tmp_name'], $uploadPath . $filename, $resizeParams['width'], $resizeParams['height'], $resizeParams['type'],)) {
                $this->imageHelperFuncions->$funcThumb($file['tmp_name'], $uploadPathThumbs . $filename, $thumpParams['width'], $thumpParams['height'], $thumpParams['type'],);
//                $reviewImage = ReviewImage::findFirstOrDefault(["id" => null]);
//                $reviewImage->setReviewId($reviewId)->setFilename($filename.".".$resizeParams['type'])->persist(["id" => null]);
                $images['view'][] = $this->imagePath("review_images") . "/" . $filename . "." . $resizeParams['type'];
                $images['thumbs'][] = $this->imagePath("review_thumbnails") . "/" . $filename . "." . $thumpParams['type'];
            }
        }

        return $images ?? [];
    }

    /**
     * return insert id
     *
     * @param array $param
     * @return int
     */
//    private function addReview($param)
//    {
//        $review = Review::findFirstOrDefault(["id" => $param['id']]);
//
//        return $review->setProductId($param['productId'])
//                        ->setRating($param["rating"])
//                        ->setUserId($param['user_id'])
//                        ->setReviewId($param['review_id'])
//                        ->setUserName($param["user_name"])
//                        ->setUserMessage($param['user_message'])
//                        ->setSellerName($param["seller_name"])
//                        ->setSellerMessage($param['seller_message'])
//                        ->setTimeCreated($param['time_created'])
//                        ->setTimeModified($param['time_modified'])
//                        ->persist(["id" => $param['id']]);
//    }
}
