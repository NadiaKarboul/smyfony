<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319230342 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garagec (id_garage INT AUTO_INCREMENT NOT NULL, nom_garage VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, numero INT NOT NULL, panne_moteur INT NOT NULL, pompe_a_eau INT NOT NULL, patin INT NOT NULL, essuie_glace INT NOT NULL, radiateur INT NOT NULL, ventilateur INT NOT NULL, duride INT NOT NULL, fuite_d_huile INT NOT NULL, vidange INT NOT NULL, filtre INT NOT NULL, batterie INT NOT NULL, amortisseur INT NOT NULL, frein_main INT NOT NULL, feu_d_eclairage INT NOT NULL, taux_de_reduction INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id_garage)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande CHANGE date_commande date_commande DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE garagec');
        $this->addSql('ALTER TABLE commande CHANGE date_commande date_commande DATE DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL');
    }
}
