<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112131228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `user_data` (
            `user_id` int DEFAULT NULL,
            `address` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            `geodata` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            `fias_id` varchar(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
            `fias_level` int DEFAULT NULL,
            `time` int NOT NULL,
            `id` int NOT NULL AUTO_INCREMENT,
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE user_data");
    }
}
