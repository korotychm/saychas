<?php

// ControlPanel/src\Service\HtmlContentProvider.php

namespace ControlPanel\Service;

use Interop\Container\ContainerInterface;

/**
 * Description of HtmlContentProvider
 *
 * @author alex
 */
class HtmlContentProvider
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Control panel menu items
     *
     * @return array
     */
    public function getMenuItems(): array
    {
        return [
            ['id' => 'item1', 'name' => 'Back to site', 'route' => 'home', 'type' => 'left', 'active' => true,],
            ['id' => 'item2', 'name' => 'Вход для партнеров', 'route' => 'control-panel', 'type' => 'right', 'active' => false,],
        ];
    }

    /**
     * Control panel sidebar menu items
     *
     * @return array
     */
    public function getSidebarMenuItems(): array
    {
        return [
            ['id' => 'profileId', 'url' => '#', 'name' => 'Данные профиля', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
            ['id' => 'userManagementId', 'url' => '#', 'name' => 'Управление сотрудниками', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
            ['id' => 'accountManagementId', 'url' => '#', 'name' => 'Управление аккаунтом', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
            ['id' => 'storesId', 'url' => '#', 'name' => 'Адреса магазинов', 'route' => 'control-panel', 'type' => 'left', 'active' => true,],
            ['id' => 'actionAndDiscountId', 'url' => '#', 'name' => 'Акции и скидки', 'route' => 'control-panel', 'type' => 'left', 'active' => true,],
            ['id' => 'respondingToReviewsId', 'url' => '#', 'name' => 'Ответы на отзывы', 'route' => 'control-panel', 'type' => 'left', 'active' => true,],
            ['id' => 'productsId', 'url' => '#', 'name' => 'Карточки товаров', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
            ['id' => 'exitId', 'url' => '/control-panel/logout', 'name' => 'Выход', 'route' => 'exit-control-panel', 'type' => 'left', 'active' => false,],
            ['id' => 'backToSiteId', 'url' => '/', 'name' => 'Перейти на сайт', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
        ];
    }

}
