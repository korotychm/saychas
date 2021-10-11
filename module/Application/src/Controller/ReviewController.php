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
use Application\Model\Entity\User;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Laminas\Filter\StripTags;
//use Application\Model\Repository\ProductRepository;
//use Application\Model\Entity\HandbookRelatedProduct;
//use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Service\CommonHelperFunctionsService;
use Application\Service\ExternalCommunicationService;
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
            $entityManager, $config, AuthenticationService $authService, CommonHelperFunctionsService $commonHelperFuncions,
            ExternalCommunicationService $externalCommunicationService)
    {
        // $this->productRatingRepository = $productRatingRepository;
        $this->productRepository = $productRepository;
        //  $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->authService = $authService;
        $this->commonHelperFuncions = $commonHelperFuncions;
        $this->externalCommunicationService = $externalCommunicationService;
        $this->entityManager->initRepository(Setting::class);
//        $this->entityManager->initRepository(ProductCharacteristic::class);
//        $this->entityManager->initRepository(StockBalance::class);
//        $this->entityManager->initRepository(ProductHistory::class);
//        $this->entityManager->initRepository(ProductFavorites::class);
        $this->entityManager->initRepository(Review::class);
        $this->entityManager->initRepository(ReviewImage::class);
        $this->entityManager->initRepository(User::class);
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
        $return = ["result" => true, "seller_name" => "", "seller_message" => ""];

        if (empty($return['productId'] = $this->getRequest()->getPost()->productId)) {
            return new JsonModel(["result" => false, "description" => "Product Id error"]);
        }

        if (empty($return['user_id'] = $this->identity())) {
            return $this->getResponse()->setStatusCode(403);
        }

        if (empty($return["user_message"] = $stripTags->filter(trim($this->getRequest()->getPost()->reviewMessage))) or strlen($return["user_message"]) < 4) {
            return new JsonModel(["result" => false, "description" => "Напиши отзыв больше трех символов!"]);
        }

        $userInfo = $this->commonHelperFuncions->getUserInfo(User::find(["id" => $return['user_id']]));

        if (empty($return["user_name"] = $userInfo['name'])) {
            return $this->getResponse()->setStatusCode(403);
        }



        $stripTags = new StripTags();
        $return["rating"] = $this->getValidRating($this->getRequest()->getPost()->rating);
        $reviewId = $this->addReview($return);
        $files = $this->getRequest()->getFiles();

        if (!empty($return["files"] = $files['files'])) {

            if (!$this->getValidPostImage($return["files"])) {
                return new JsonModel(["result" => false, "description" => "Допустимые форматы загружаемых файлов: " . join(", ", Resource::LEGAL_IMAGE_TYPES)]);
            }

            $return['images'] = $this->addReviewImage($return["files"], $reviewId);
        }

        $return['answer1c'] = $this->externalCommunicationService->sendReview($return);

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
            $review['time_created'] = date("Y-m-d H:i:s", (int) $review['time_created']);
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
        foreach ($images as $image) {
            $return[] = $image['filename'];
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
    private function getValidPostImage($files)
    {
        foreach ($files as $file) {
            if (!in_array($file['type'], Resource::LEGAL_IMAGE_TYPES)) {
                return false;
            }
        }
        return true;
    }

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
        foreach ($files as $file) {
            $uuid = uniqid($this->identity() . "_" . time(), false);
            $ext = explode('/', $file['type']);
            $filename = $uuid . "." . end($ext);
            //$return['uploadFiles'][] = ["from" => $file['tmp_name'], "to" => $return["uploadPath"] . $filename];
            
            if (move_uploaded_file($file['tmp_name'], $uploadPath . $filename)) {
                $reviewImage = ReviewImage::findFirstOrDefault(["id" => null]);
                $reviewImage->setReviewId($reviewId)->setFilename($filename)->persist(["id" => null]);
                $images[] = $filename;
            }
        }

        return $images;
    }

    /**
     * return insert id
     *
     * @param array $return
     * @return int
     */
    private function addReview($return)
    {
        $review = Review::findFirstOrDefault(["id" => null]);
        
        return $review->setProductId($return['productId'])
                        ->setRating($return["rating"])
                        ->setUserId($return['user_id'])
                        ->setUserName($return["user_name"])
                        ->setUserMessage($return['user_message'])
                        ->setSellerName($return["seller_name"])
                        ->setSellerMessage($return['seller_message'])
                        ->setTimeCreated(time())
                        ->persist(["id" => null]);
    }

}
 