<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117115624 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD souscategory_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD54094BF FOREIGN KEY (souscategory_id_id) REFERENCES category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD54094BF ON product (souscategory_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD54094BF');
        $this->addSql('DROP INDEX IDX_D34A04AD54094BF ON product');
        $this->addSql('ALTER TABLE product DROP souscategory_id_id');
    }
}
