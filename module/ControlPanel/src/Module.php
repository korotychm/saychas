<?php

namespace ControlPanel;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Description of Module
 *
 * @author alex
 */


class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}