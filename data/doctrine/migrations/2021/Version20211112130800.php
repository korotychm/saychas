<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112130800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `role_hierarchy` (
            `id` int NOT NULL AUTO_INCREMENT,
            `parent_role_id` int NOT NULL,
            `child_role_id` int NOT NULL,
            `terminal` int NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
          ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb3 COLLATE=utf8_unicode_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE role_hierarchy");
    }
}
