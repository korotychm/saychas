<?php

declare(strict_types=1);

namespace Saychas\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119093617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $tableProvider = $schema->getTable('provider');
        $tableProvider->addColumn('url', 'string', [
            'length' => 150,
        ]);
    }

    public function down(Schema $schema): void
    {
        $tableProvider = $schema->getTable('provider');
        $tableProvider->dropColumn('url');
    }
}
