<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117120230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sous_category (id INT AUTO_INCREMENT NOT NULL, id_souscategory_id INT DEFAULT NULL, souscategory_name VARCHAR(255) DEFAULT NULL, colour VARCHAR(255) DEFAULT NULL, INDEX IDX_E022D947E2BFBF8 (id_souscategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sous_category ADD CONSTRAINT FK_E022D947E2BFBF8 FOREIGN KEY (id_souscategory_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sous_category DROP FOREIGN KEY FK_E022D947E2BFBF8');
        $this->addSql('DROP TABLE sous_category');
    }
}
