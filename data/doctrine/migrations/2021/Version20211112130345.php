<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112130345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `product_user_rating` (
            `product_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
            `user_id` bigint NOT NULL,
            `rating` tinyint NOT NULL DEFAULT '47',
            PRIMARY KEY (`product_id`,`user_id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DROP TABLE product_user_rating");
    }
}
