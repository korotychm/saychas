<?php
// config/autoload/dependencies.global.php:

/* 
 * Here comes the text of your license
 * Each line should be prefixed with  * 
 */

return [
    'dependencies' => [
        'factories' => [
            \Application\Command\FetchImagesCommand::class => \Application\Command\Factory\FetchImagesCommandFactory::class,
        ],
    ],
];