<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318083014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD le_user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE categorie ADD CONSTRAINT FK_497DD63488A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_497DD63488A1A5E2 ON categorie (le_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie DROP FOREIGN KEY FK_497DD63488A1A5E2');
        $this->addSql('DROP INDEX IDX_497DD63488A1A5E2 ON categorie');
        $this->addSql('ALTER TABLE categorie DROP le_user_id');
    }
}
