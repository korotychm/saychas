<?php

// ControlPanel/src/Controller/AbstractControlPanelActionController.php

/**
 * @author alex;
 * @copyright (c) 2021, Alex
 */

namespace ControlPanel\Controller;

use Laminas\Mvc\Controller\AbstractActionController;

/**
 * Basic control panel action controller
 */
abstract class AbstractControlPanelActionController extends AbstractActionController
{

    protected function getManagerInfo($routeMatch)
    {
        $routeName = $routeMatch->getMatchedRouteName();

        list($leftName, $rightName) = explode('/', $routeName);

        //$params = $routeMatch->getParams();

        $config = $this->container->get('Config');

        $managerName = $config['router']['routes'][$leftName]['child_routes'][$rightName]['options']['repository'];
        
        $isTest = $config['router']['routes'][$leftName]['child_routes'][$rightName]['options']['is_test'];
        
        $prefix = $config['router']['routes'][$leftName]['child_routes'][$rightName]['options']['prefix'];

        return ['manager' => $this->container->get($managerName), 'manager_name' => $managerName, 'is_test' => $isTest, 'prefix' => $prefix];
    }

}
