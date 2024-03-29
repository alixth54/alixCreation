<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240117120827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product ADD souscate_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD541C1D91 FOREIGN KEY (souscate_id) REFERENCES sous_category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD541C1D91 ON product (souscate_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD541C1D91');
        $this->addSql('DROP INDEX IDX_D34A04AD541C1D91 ON product');
        $this->addSql('ALTER TABLE product DROP souscate_id');
    }
}
