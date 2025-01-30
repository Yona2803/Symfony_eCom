<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250130133036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cart_items (id INT AUTO_INCREMENT NOT NULL, cart_id INT NOT NULL, item_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_BEF484451AD5CDBF (cart_id), INDEX IDX_BEF48445126F525E (item_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF484451AD5CDBF FOREIGN KEY (cart_id) REFERENCES carts (id)');
        $this->addSql('ALTER TABLE cart_items ADD CONSTRAINT FK_BEF48445126F525E FOREIGN KEY (item_id) REFERENCES items (id)');
        $this->addSql('ALTER TABLE items_carts DROP FOREIGN KEY FK_4ADC80176BB0AE84');
        $this->addSql('ALTER TABLE items_carts DROP FOREIGN KEY FK_4ADC8017BCB5C6F5');
        $this->addSql('DROP TABLE items_carts');
        $this->addSql('ALTER TABLE carts DROP INDEX UNIQ_4E004AACA76ED395, ADD INDEX IDX_4E004AACA76ED395 (user_id)');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(180) NOT NULL, CHANGE roles roles JSON NOT NULL, CHANGE password password VARCHAR(10000) NOT NULL, CHANGE username username VARCHAR(25) NOT NULL, CHANGE phone_number phone_number VARCHAR(10) DEFAULT NULL, CHANGE first_name first_name VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE items_carts (items_id INT NOT NULL, carts_id INT NOT NULL, INDEX IDX_4ADC80176BB0AE84 (items_id), INDEX IDX_4ADC8017BCB5C6F5 (carts_id), PRIMARY KEY(items_id, carts_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE items_carts ADD CONSTRAINT FK_4ADC80176BB0AE84 FOREIGN KEY (items_id) REFERENCES items (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE items_carts ADD CONSTRAINT FK_4ADC8017BCB5C6F5 FOREIGN KEY (carts_id) REFERENCES carts (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF484451AD5CDBF');
        $this->addSql('ALTER TABLE cart_items DROP FOREIGN KEY FK_BEF48445126F525E');
        $this->addSql('DROP TABLE cart_items');
        $this->addSql('ALTER TABLE carts DROP INDEX IDX_4E004AACA76ED395, ADD UNIQUE INDEX UNIQ_4E004AACA76ED395 (user_id)');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(180) DEFAULT NULL, CHANGE roles roles JSON DEFAULT NULL, CHANGE password password VARCHAR(255) DEFAULT NULL, CHANGE username username VARCHAR(50) DEFAULT NULL, CHANGE phone_number phone_number VARCHAR(30) DEFAULT NULL, CHANGE first_name first_name VARCHAR(30) DEFAULT NULL');
    }
}
