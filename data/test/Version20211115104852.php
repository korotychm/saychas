<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115104852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('migration_test')->setPrimaryKey(['test1']);
        $table->getColumn('test1')->setUnsigned(true)->setAutoincrement(true)->setNotnull(true);
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('migration_test');
        $table->dropPrimaryKey();
        $table->getColumn('test1')->setUnsigned(false)->setAutoincrement(false)->setNotnull(false);
    }
}
