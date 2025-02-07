<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250205111647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carts DROP INDEX IDX_4E004AACA76ED395, ADD UNIQUE INDEX UNIQ_4E004AACA76ED395 (user_id)');
        $this->addSql('ALTER TABLE categories ADD category_image LONGBLOB DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)');
        $this->addSql('ALTER TABLE users ADD google_id VARCHAR(255) DEFAULT NULL, CHANGE password password LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE carts DROP INDEX UNIQ_4E004AACA76ED395, ADD INDEX IDX_4E004AACA76ED395 (user_id)');
        $this->addSql('DROP INDEX UNIQ_3AF346685E237E06 ON categories');
        $this->addSql('ALTER TABLE categories DROP category_image');
        $this->addSql('ALTER TABLE users DROP google_id, CHANGE password password VARCHAR(10000) NOT NULL');
    }
}
