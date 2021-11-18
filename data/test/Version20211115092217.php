<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211115092217 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable( 'migration_test' );
        $table->addColumn('test1', 'integer');
        $table->addColumn('test2', 'string');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('migration_test');
    }
}
