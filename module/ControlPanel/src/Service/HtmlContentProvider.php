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
            ['name' => 'Back to site', 'route' => 'home', 'type' => 'left',],
            ['name' => 'Вход для партнеров', 'route' => 'control-panel', 'type' => 'right',],
        ];
    }

}
