<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112115953 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `characteristic` (
            `id` varchar(19) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `category_id` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
            `type` tinyint(1) NOT NULL DEFAULT '1',
            `filter` tinyint(1) NOT NULL DEFAULT '0',
            `group` tinyint(1) NOT NULL DEFAULT '0',
            `sort_order` int NOT NULL DEFAULT '1',
            `unit` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
            `is_main` tinyint(1) NOT NULL DEFAULT '0',
            `is_mandatory` tinyint(1) NOT NULL DEFAULT '0',
            `is_list` tinyint(1) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE characteristic");
    }
}
