<?php
/**
 * Description of ConfigProvider
 *
 * @author alex
 */
namespace Application;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),
            'dependencies' => $this->getDependencyConfig(),
        ];
    }

    public function getCliConfig() : array
    {
        return [
            'commands' => [
                'package:command-name' => Command\FetchImagesCommand::class,
                'package:fetch-images' => Command\FetchImagesCommand::class,
            ],
        ];
    }

    public function getDependencyConfig() : array
    {
        return [
            'factories' => [
                Command\FetchImagesCommand::class => Command\Factory\CommandFactory::class,
            ],
        ];
    }
}