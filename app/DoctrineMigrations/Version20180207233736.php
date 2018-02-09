<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180207233736 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $page = $schema->createTable('page');

        $page->addColumn('id', Type::INTEGER)
            ->setAutoincrement(true);
        $page->setPrimaryKey(['id']);

        $page->addColumn('name', Type::STRING)
            ->setNotnull(true);

        $page->addColumn('parent_id', Type::INTEGER)
            ->setNotnull(false);
        $page->addForeignKeyConstraint(
            'page',
            ['parent_id'],
            ['id']
        );

        $page->addUniqueIndex(['name', 'parent_id']);

        $page->addColumn('header', Type::STRING)
            ->setNotnull(true);

        $page->addColumn('content', Type::TEXT)
            ->setNotnull(true);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('page');
    }
}
