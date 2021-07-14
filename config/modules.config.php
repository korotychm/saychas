<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

/**
 * List of enabled modules for this application.
 *
 * This should be an array of module namespaces used in the application.
 */
return [
    'Laminas\Serializer',
    'Laminas\Log',
    'Laminas\I18n',
    'Laminas\Mvc\Plugin\Identity',
    'Laminas\Session',
    'Laminas\Cache',
    'Laminas\Form',
    'Laminas\InputFilter',
    'Laminas\Filter',
    'Laminas\Paginator',
    'Laminas\Hydrator',
    'Laminas\Db',
    'Laminas\Router',
    'Laminas\Validator',
    'Laminas\Json\Json',
    'DoctrineModule',
    'DoctrineORMModule',
    'Laminas\Diactoros',
    'Application',
    'ControlPanel',
];
