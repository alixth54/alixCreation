<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121162158 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE orders (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, adress_id INT NOT NULL, date DATE NOT NULL, payment_type VARCHAR(50) DEFAULT NULL, statut VARCHAR(50) NOT NULL, quantity INT NOT NULL, INDEX IDX_E52FFDEEA76ED395 (user_id), UNIQUE INDEX UNIQ_E52FFDEE8486F9AC (adress_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE8486F9AC FOREIGN KEY (adress_id) REFERENCES adress (id)');
        $this->addSql('ALTER TABLE product ADD orders_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADCFFE9AD6 ON product (orders_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADCFFE9AD6');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE8486F9AC');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP INDEX IDX_D34A04ADCFFE9AD6 ON product');
        $this->addSql('ALTER TABLE product DROP orders_id');
    }
}
