<?php

require 'vendor/autoload.php';

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\DependencyFactory;

$config = new PhpFile('bootstrap.php'); // Or use one of the Doctrine\Migrations\Configuration\Configuration\* loaders


$paths = [__DIR__.'/lib/MyProject/Entities'];
$isDevMode = true;

$ORMconfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null, false);
$entityManager = EntityManager::create(['driver' => 'pdo_mysql', 'memory' => true], $ORMconfig);
print_r($entityManager);

return DependencyFactory::fromEntityManager($config, new ExistingEntityManager($entityManager));
