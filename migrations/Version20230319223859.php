<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319223859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produit (id_prod INT AUTO_INCREMENT NOT NULL, id_categorie INT NOT NULL, nom_prod VARCHAR(30) NOT NULL, prix_prod DOUBLE PRECISION NOT NULL, description_prod VARCHAR(100) NOT NULL, quantite_prod INT NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_29A5EC27C9486A13 (id_categorie), PRIMARY KEY(id_prod)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27C9486A13 FOREIGN KEY (id_categorie) REFERENCES categorie (id_categorie)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27C9486A13');
        $this->addSql('DROP TABLE produit');
    }
}
