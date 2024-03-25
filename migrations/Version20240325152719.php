<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240325152719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categorie ADD url_image VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE recompense ADD le_user_id INT DEFAULT NULL, ADD le_produit_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE recompense ADD CONSTRAINT FK_1E9BC0DE88A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recompense ADD CONSTRAINT FK_1E9BC0DE2C340150 FOREIGN KEY (le_produit_id) REFERENCES produit (id)');
        $this->addSql('CREATE INDEX IDX_1E9BC0DE88A1A5E2 ON recompense (le_user_id)');
        $this->addSql('CREATE INDEX IDX_1E9BC0DE2C340150 ON recompense (le_produit_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recompense DROP FOREIGN KEY FK_1E9BC0DE88A1A5E2');
        $this->addSql('ALTER TABLE recompense DROP FOREIGN KEY FK_1E9BC0DE2C340150');
        $this->addSql('DROP INDEX IDX_1E9BC0DE88A1A5E2 ON recompense');
        $this->addSql('DROP INDEX IDX_1E9BC0DE2C340150 ON recompense');
        $this->addSql('ALTER TABLE recompense DROP le_user_id, DROP le_produit_id');
        $this->addSql('ALTER TABLE categorie DROP url_image');
    }
}
