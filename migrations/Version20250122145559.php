<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250122145559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE items_carts ADD PRIMARY KEY (items_id, carts_id)');
        $this->addSql('ALTER TABLE users ADD first_name VARCHAR(30) NOT NULL, ADD last_name VARCHAR(30) NOT NULL, ADD address VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE wish_list_items ADD PRIMARY KEY (wish_list_id, items_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users DROP first_name, DROP last_name, DROP address');
        $this->addSql('DROP INDEX `primary` ON wish_list_items');
        $this->addSql('DROP INDEX `primary` ON items_carts');
    }
}
