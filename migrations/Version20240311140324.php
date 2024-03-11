<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240311140324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE blason (id INT AUTO_INCREMENT NOT NULL, nom_blason VARCHAR(255) NOT NULL, montant_achats DOUBLE PRECISION NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, le_user_id INT DEFAULT NULL, date_commande DATETIME NOT NULL, INDEX IDX_6EEAA67D88A1A5E2 (le_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commander (id INT AUTO_INCREMENT NOT NULL, le_user_id INT DEFAULT NULL, la_commande_id INT DEFAULT NULL, quantite INT NOT NULL, INDEX IDX_42D318BA88A1A5E2 (le_user_id), INDEX IDX_42D318BA3743EDD (la_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE palier (id INT AUTO_INCREMENT NOT NULL, palier_bas INT NOT NULL, palier_haut INT NOT NULL, nom_palier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, le_user_id INT DEFAULT NULL, nom_produit VARCHAR(255) NOT NULL, prix_produit DOUBLE PRECISION NOT NULL, points_fidelite INT NOT NULL, INDEX IDX_29A5EC2788A1A5E2 (le_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recompense (id INT AUTO_INCREMENT NOT NULL, le_palier_id INT DEFAULT NULL, nom_recompense VARCHAR(255) NOT NULL, points_necessaires INT NOT NULL, INDEX IDX_1E9BC0DEB9FA97A2 (le_palier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utiliser (id INT AUTO_INCREMENT NOT NULL, la_recompense_id INT DEFAULT NULL, le_user_id INT DEFAULT NULL, date_utiliser DATETIME NOT NULL, INDEX IDX_5C949109BA87F26E (la_recompense_id), INDEX IDX_5C94910988A1A5E2 (le_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D88A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commander ADD CONSTRAINT FK_42D318BA88A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE commander ADD CONSTRAINT FK_42D318BA3743EDD FOREIGN KEY (la_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC2788A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recompense ADD CONSTRAINT FK_1E9BC0DEB9FA97A2 FOREIGN KEY (le_palier_id) REFERENCES palier (id)');
        $this->addSql('ALTER TABLE utiliser ADD CONSTRAINT FK_5C949109BA87F26E FOREIGN KEY (la_recompense_id) REFERENCES recompense (id)');
        $this->addSql('ALTER TABLE utiliser ADD CONSTRAINT FK_5C94910988A1A5E2 FOREIGN KEY (le_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD le_blason_id INT DEFAULT NULL, ADD stock_points_fidelite INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649332C583F FOREIGN KEY (le_blason_id) REFERENCES blason (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649332C583F ON user (le_blason_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649332C583F');
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D88A1A5E2');
        $this->addSql('ALTER TABLE commander DROP FOREIGN KEY FK_42D318BA88A1A5E2');
        $this->addSql('ALTER TABLE commander DROP FOREIGN KEY FK_42D318BA3743EDD');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC2788A1A5E2');
        $this->addSql('ALTER TABLE recompense DROP FOREIGN KEY FK_1E9BC0DEB9FA97A2');
        $this->addSql('ALTER TABLE utiliser DROP FOREIGN KEY FK_5C949109BA87F26E');
        $this->addSql('ALTER TABLE utiliser DROP FOREIGN KEY FK_5C94910988A1A5E2');
        $this->addSql('DROP TABLE blason');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE commander');
        $this->addSql('DROP TABLE palier');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE recompense');
        $this->addSql('DROP TABLE utiliser');
        $this->addSql('DROP INDEX IDX_8D93D649332C583F ON user');
        $this->addSql('ALTER TABLE user DROP le_blason_id, DROP stock_points_fidelite');
    }
}
