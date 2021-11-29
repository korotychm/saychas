<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112123546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `customer` (
            `id` int NOT NULL AUTO_INCREMENT,
            `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `phone` int NOT NULL,
            `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `password` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE customer");
    }
}
