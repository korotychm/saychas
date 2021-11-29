<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112123853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `param_title` (
            `id` int NOT NULL,
            `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `sort_order` int NOT NULL DEFAULT '0',
            `filter` int NOT NULL DEFAULT '0',
            `category_id` int NOT NULL,
            `type` tinyint NOT NULL DEFAULT '1'
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE param_title");
    }
}
