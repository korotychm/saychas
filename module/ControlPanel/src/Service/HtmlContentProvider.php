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
            ['id' => 'storesId', 'url' => '#', 'name' => 'Магазины', 'route' => 'control-panel', 'type' => 'left', 'active' => true,],
            ['id' => 'productsId', 'url' => '#', 'name' => 'Номенклатура', 'route' => 'control-panel', 'type' => 'left', 'active' => false,],
        ];
    }
}
