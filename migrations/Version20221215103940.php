<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221215103940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, uuid VARCHAR(20) DEFAULT NULL, status VARCHAR(40) DEFAULT NULL, issued_at DATETIME DEFAULT NULL, due_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_906517449395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoice_item (id INT AUTO_INCREMENT NOT NULL, invoice_id_id INT DEFAULT NULL, name VARCHAR(60) DEFAULT NULL, description LONGTEXT DEFAULT NULL, amount DOUBLE PRECISION DEFAULT NULL, unit_price DOUBLE PRECISION DEFAULT NULL, INDEX IDX_1DDE477B429ECEE2 (invoice_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(40) NOT NULL, email VARCHAR(60) NOT NULL, tel VARCHAR(20) DEFAULT NULL, name VARCHAR(40) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, token VARCHAR(255) DEFAULT NULL, siret VARCHAR(20) DEFAULT NULL, vat DOUBLE PRECISION DEFAULT NULL, updated_at DATETIME NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_log (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, target_id INT DEFAULT NULL, type VARCHAR(40) DEFAULT NULL, content LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_6429094EF675F31B (author_id), INDEX IDX_6429094E158E0B66 (target_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449395C3F3 FOREIGN KEY (customer_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE invoice_item ADD CONSTRAINT FK_1DDE477B429ECEE2 FOREIGN KEY (invoice_id_id) REFERENCES invoice (id)');
        $this->addSql('ALTER TABLE user_log ADD CONSTRAINT FK_6429094EF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_log ADD CONSTRAINT FK_6429094E158E0B66 FOREIGN KEY (target_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_906517449395C3F3');
        $this->addSql('ALTER TABLE invoice_item DROP FOREIGN KEY FK_1DDE477B429ECEE2');
        $this->addSql('ALTER TABLE user_log DROP FOREIGN KEY FK_6429094EF675F31B');
        $this->addSql('ALTER TABLE user_log DROP FOREIGN KEY FK_6429094E158E0B66');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP TABLE invoice_item');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_log');
    }
}
