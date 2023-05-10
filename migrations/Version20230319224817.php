<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319224817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lignepanier (id_ligne INT AUTO_INCREMENT NOT NULL, id_produit INT NOT NULL, id_panier INT NOT NULL, quantite INT NOT NULL, prix DOUBLE PRECISION NOT NULL, nom_produit VARCHAR(30) NOT NULL, description_prod LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_AD580B5EF7384557 (id_produit), INDEX IDX_AD580B5E2FBB81F (id_panier), PRIMARY KEY(id_ligne)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lignepanier ADD CONSTRAINT FK_AD580B5EF7384557 FOREIGN KEY (id_produit) REFERENCES produit (id_prod)');
        $this->addSql('ALTER TABLE lignepanier ADD CONSTRAINT FK_AD580B5E2FBB81F FOREIGN KEY (id_panier) REFERENCES panier (id_panier)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignepanier DROP FOREIGN KEY FK_AD580B5EF7384557');
        $this->addSql('ALTER TABLE lignepanier DROP FOREIGN KEY FK_AD580B5E2FBB81F');
        $this->addSql('DROP TABLE lignepanier');
    }
}
