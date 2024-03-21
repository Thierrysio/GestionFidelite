<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319162058 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commander ADD le_produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE commander ADD CONSTRAINT FK_42D318BA2C340150 FOREIGN KEY (le_produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_42D318BA2C340150 ON commander (le_produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commander DROP FOREIGN KEY FK_42D318BA2C340150');
        $this->addSql('DROP INDEX IDX_42D318BA2C340150 ON commander');
        $this->addSql('ALTER TABLE commander DROP le_produit_id');
    }
}
