<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319225721 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commande (id_commande INT AUTO_INCREMENT NOT NULL, id_panier INT NOT NULL, montant DOUBLE PRECISION NOT NULL, etat_commande VARCHAR(200) NOT NULL, gouvernorat VARCHAR(200) NOT NULL, ville VARCHAR(100) NOT NULL, rue VARCHAR(200) NOT NULL, code_postal INT NOT NULL, date_commande DATE NOT NULL, INDEX IDX_6EEAA67D2FBB81F (id_panier), PRIMARY KEY(id_commande)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D2FBB81F FOREIGN KEY (id_panier) REFERENCES panier (id_panier)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D2FBB81F');
        $this->addSql('DROP TABLE commande');
    }
}
