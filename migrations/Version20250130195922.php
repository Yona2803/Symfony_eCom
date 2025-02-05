<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130195922 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_items CHANGE cart_id cart_id INT NOT NULL, CHANGE item_id item_id INT NOT NULL');
        $this->addSql('ALTER TABLE carts DROP INDEX UNIQ_4E004AACA76ED395, ADD INDEX IDX_4E004AACA76ED395 (user_id)');
        $this->addSql('ALTER TABLE carts CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE items CHANGE tags tags JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carts DROP INDEX IDX_4E004AACA76ED395, ADD UNIQUE INDEX UNIQ_4E004AACA76ED395 (user_id)');
        $this->addSql('ALTER TABLE carts CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_items CHANGE cart_id cart_id INT DEFAULT NULL, CHANGE item_id item_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE items CHANGE tags tags JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
