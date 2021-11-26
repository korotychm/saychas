<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126055900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Column for new price';
    }

    public function up(Schema $schema): void
    {
        $tableBrand = $schema->getTable('price');
        $tableBrand->addColumn('new_price', 'integer', [
            'notnull' => true,
            'default' => 0
        ]);
    }

    public function down(Schema $schema): void
    {
        $tableBrand = $schema->getTable('price');
        $tableBrand->dropColumn('new_price');
    }
}
