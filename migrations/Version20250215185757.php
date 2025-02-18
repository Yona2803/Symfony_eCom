<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250215185757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, item_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_BEF484451AD5CDBF (cart_id), INDEX IDX_BEF48445126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE carts (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_4E004AACA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) NOT NULL, category_image LONGBLOB DEFAULT NULL, UNIQUE INDEX UNIQ_3AF346685E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE items (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(200) NOT NULL, description LONGTEXT NOT NULL, price DOUBLE PRECISION NOT NULL, stock INT NOT NULL, item_image LONGBLOB DEFAULT NULL, tags JSON DEFAULT NULL, INDEX IDX_E11EE94D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_details (id INT AUTO_INCREMENT NOT NULL, item_id INT NOT NULL, order_fk_id INT NOT NULL, total_price DOUBLE PRECISION NOT NULL, quantity INT NOT NULL, INDEX IDX_845CA2C1126F525E (item_id), INDEX IDX_845CA2C126E96D2D (order_fk_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_status (id INT AUTO_INCREMENT NOT NULL, status_name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, order_status_id INT NOT NULL, order_date DATE NOT NULL, total_amount DOUBLE PRECISION NOT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), INDEX IDX_E52FFDEED7707B45 (order_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password LONGTEXT DEFAULT NULL, username VARCHAR(25) NOT NULL, phone_number VARCHAR(10) DEFAULT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) DEFAULT NULL, address VARCHAR(60) DEFAULT NULL, google_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wish_list (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, UNIQUE INDEX UNIQ_5B8739BDA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wish_list_items (wish_list_id INT NOT NULL, items_id INT NOT NULL, INDEX IDX_7124905ED69F3311 (wish_list_id), INDEX IDX_7124905E6BB0AE84 (items_id), PRIMARY KEY(wish_list_id, items_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('ALTER TABLE carts ADD CONSTRAINT FK_4E004AACA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE items ADD CONSTRAINT FK_E11EE94D12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C1126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('ALTER TABLE order_details ADD CONSTRAINT FK_845CA2C126E96D2D FOREIGN KEY (order_fk_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEED7707B45 FOREIGN KEY (order_status_id) REFERENCES order_status (id)');
        $this->addSql('ALTER TABLE wish_list ADD CONSTRAINT FK_5B8739BDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE wish_list_items ADD CONSTRAINT FK_7124905ED69F3311 FOREIGN KEY (wish_list_id) REFERENCES wish_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wish_list_items ADD CONSTRAINT FK_7124905E6BB0AE84 FOREIGN KEY (items_id) REFERENCES items (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484451AD5CDBF');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445126F525E');
        $this->addSql('ALTER TABLE carts DROP FOREIGN KEY FK_4E004AACA76ED395');
        $this->addSql('ALTER TABLE items DROP FOREIGN KEY FK_E11EE94D12469DE2');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C1126F525E');
        $this->addSql('ALTER TABLE order_details DROP FOREIGN KEY FK_845CA2C126E96D2D');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEED7707B45');
        $this->addSql('ALTER TABLE wish_list DROP FOREIGN KEY FK_5B8739BDA76ED395');
        $this->addSql('ALTER TABLE wish_list_items DROP FOREIGN KEY FK_7124905ED69F3311');
        $this->addSql('ALTER TABLE wish_list_items DROP FOREIGN KEY FK_7124905E6BB0AE84');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('DROP TABLE carts');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE items');
        $this->addSql('DROP TABLE order_details');
        $this->addSql('DROP TABLE order_status');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE wish_list');
        $this->addSql('DROP TABLE wish_list_items');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
