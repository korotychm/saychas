<?php

declare(strict_types=1);

namespace Application\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use Laminas\View\Model\JsonModel;
use Laminas\Mvc\MvcEvent;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Application\Model\TestRepositoryInterface;
use Application\Model\RepositoryInterface\CategoryRepositoryInterface;
use Application\Model\RepositoryInterface\ProviderRepositoryInterface;
use Application\Model\RepositoryInterface\StoreRepositoryInterface;
use Application\Model\RepositoryInterface\ProductRepositoryInterface;
use Application\Model\RepositoryInterface\FilteredProductRepositoryInterface;
use Application\Model\RepositoryInterface\BrandRepositoryInterface;
use Application\Model\RepositoryInterface\BasketRepositoryInterface;
use Application\Model\RepositoryInterface\ColorRepositoryInterface;
use Application\Model\RepositoryInterface\SettingRepositoryInterface;
use Application\Model\RepositoryInterface\CharacteristicRepositoryInterface;
use Application\Model\RepositoryInterface\PriceRepositoryInterface;
use Application\Model\RepositoryInterface\StockBalanceRepositoryInterface;
use Application\Model\RepositoryInterface\HandbookRelatedProductRepositoryInterface;
use Application\Model\Entity\ProductCharacteristic;
use Application\Model\RepositoryInterface\ProductCharacteristicRepositoryInterface;
use Application\Model\Repository\UserRepository;
use Application\Model\Repository\CharacteristicRepository;
use Application\Model\Entity\HandbookRelatedProduct;
use Application\Model\Entity\Provider;
use Application\Model\Entity\Setting;
use Application\Model\Entity\ClientOrder;
use Application\Model\Entity\Delivery;
use Application\Model\Entity\Basket;
use Laminas\Json\Json;
use Application\Service\HtmlProviderService;
use Application\Service\AcquiringCommunicationService;
use Application\Resource\Resource;
use Laminas\Session\Container; // as SessionContainer;
use Laminas\Session\SessionManager;
use Application\Adapter\Auth\UserAuthAdapter;
use Laminas\Db\Sql\Where;
use Application\Model\Entity\User;
use Application\Model\Entity\UserData;
use Application\Helper\StringHelper;
use Application\Model\Entity\ProductFavorites;
use Application\Model\Entity\ProductHistory;

class AcquiringController extends AbstractActionController
{

    /**
     * @var TestRepositoryInterface
     */
    private $testRepository;
    private $categoryRepository;
    private $providerRepository;
    private $storeRepository;
    private $productRepository;
    private $filteredProductRepository;
    private $brandRepository;
    private $settingRepository;
    private $characteristicRepository;
    private $priceRepository;
    private $stockBalanceRepository;
    private $handBookRelatedProductRepository;
    private $entityManager;
    private $config;
    private $htmlProvider;
    private $acquiringCommunication;
    private $userRepository;
    private $authService;
    private $productCharacteristicRepository;
    private $colorRepository;
    private $basketRepository;
    //private $sessionContainer;
    private $sessionManager;

    public function __construct(TestRepositoryInterface $testRepository, CategoryRepositoryInterface $categoryRepository,
            ProviderRepositoryInterface $providerRepository, StoreRepositoryInterface $storeRepository,
            ProductRepositoryInterface $productRepository, FilteredProductRepositoryInterface $filteredProductRepository,
            BrandRepositoryInterface $brandRepository, ColorRepositoryInterface $colorRepository, SettingRepositoryInterface $settingRepository,
            CharacteristicRepositoryInterface $characteristicRepository,
            PriceRepositoryInterface $priceRepository, StockBalanceRepositoryInterface $stockBalanceRepository,
            HandbookRelatedProductRepositoryInterface $handBookProduct,
            $entityManager, $config, HtmlProviderService $htmlProvider, AcquiringCommunicationService $acquiringCommunication, UserRepository $userRepository, AuthenticationService $authService,
            ProductCharacteristicRepositoryInterface $productCharacteristicRepository, BasketRepositoryInterface $basketRepository/* , $sessionContainer */, $sessionManager)
    {
        $this->testRepository = $testRepository;
        $this->categoryRepository = $categoryRepository;
        $this->providerRepository = $providerRepository;
        $this->storeRepository = $storeRepository;
        $this->productRepository = $productRepository;
        $this->filteredProductRepository = $filteredProductRepository;
        $this->brandRepository = $brandRepository;
        $this->colorRepository = $colorRepository;
        $this->settingRepository = $settingRepository;
        $this->characteristicRepository = $characteristicRepository;
        $this->priceRepository = $priceRepository;
        $this->stockBalanceRepository = $stockBalanceRepository;
        $this->handBookRelatedProductRepository = $handBookProduct;
        $this->entityManager = $entityManager;
        $this->config = $config;
        $this->htmlProvider = $htmlProvider;
        $this->acquiringCommunication = $acquiringCommunication;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
        $this->productCharacteristicRepository = $productCharacteristicRepository;
        $this->basketRepository = $basketRepository;
//        $this->sessionContainer = $sessionContainer;
        $this->sessionManager = $sessionManager;

        $this->entityManager->initRepository(ClientOrder::class);
        $this->entityManager->initRepository(Setting::class);
        $this->entityManager->initRepository(Delivery::class);
    }

    public function onDispatch(MvcEvent $e)
    {
        $userAuthAdapter = new UserAuthAdapter(/* $this->userRepository *//* $this->sessionContainer */);
        $result = $this->authService->authenticate($userAuthAdapter);
        $code = $result->getCode();
        if ($code != \Application\Adapter\Auth\UserAuthResult::SUCCESS) {
            throw new \Exception('Unknown error in AcquiringController');
        }
        // Call the base class' onDispatch() first and grab the response
        $response = parent::onDispatch($e);
        return $response;
    }
    public function tinkoffPaymentAction() 
    {
        $container = new Container(Resource::SESSION_NAMESPACE);
        $return['userId'] = $userId = $container->userIdentity;
        $return['text'] = "тест тинькофф";
        $return['order_id'] = $this->params()->fromRoute('order', '');
        $return['apiconfig'] = $this->config['parameters']['TinkoffMerchantAPI'];
        $basket = Basket::findAll(["where" => ['user_id' => $userId, 'order_id' => $return['order_id']]]);
        if (null === $basket) {
            return new JsonModel(["result" => false, "message" => "error order products" ]);
        }
        $return['basket'] = $this->acquiringCommunication->initTinkoff($basket);
        
        return new JsonModel($return);
    }
  
}
