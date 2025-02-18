<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218131634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_state (id INT AUTO_INCREMENT NOT NULL, ord_state_id INT NOT NULL, state_status_id INT NOT NULL, state_id INT NOT NULL, INDEX IDX_200DA60692401721 (ord_state_id), INDEX IDX_200DA606237AC590 (state_status_id), INDEX IDX_200DA6065D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state_status (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_state ADD CONSTRAINT FK_200DA60692401721 FOREIGN KEY (ord_state_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_state ADD CONSTRAINT FK_200DA606237AC590 FOREIGN KEY (state_status_id) REFERENCES state_status (id)');
        $this->addSql('ALTER TABLE order_state ADD CONSTRAINT FK_200DA6065D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_state DROP FOREIGN KEY FK_200DA60692401721');
        $this->addSql('ALTER TABLE order_state DROP FOREIGN KEY FK_200DA606237AC590');
        $this->addSql('ALTER TABLE order_state DROP FOREIGN KEY FK_200DA6065D83CC1');
        $this->addSql('DROP TABLE order_state');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE state_status');
    }
}
