<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319231159 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE maintenance (id_maintenance INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, date_maintenance DATE NOT NULL, panne_moteur TINYINT(1) NOT NULL, pompe_a_eau TINYINT(1) NOT NULL, patin TINYINT(1) NOT NULL, essuie_glace TINYINT(1) NOT NULL, radiateur TINYINT(1) NOT NULL, ventilateur TINYINT(1) NOT NULL, duride TINYINT(1) NOT NULL, fuite_d_huile TINYINT(1) NOT NULL, vidange TINYINT(1) NOT NULL, filtre TINYINT(1) NOT NULL, batterie TINYINT(1) NOT NULL, amortisseur TINYINT(1) NOT NULL, frein_main TINYINT(1) NOT NULL, feu_d_eclairage TINYINT(1) NOT NULL, autre VARCHAR(255) NOT NULL, INDEX IDX_2F84F8E96B3CA4B (id_user), PRIMARY KEY(id_maintenance)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE maintenance ADD CONSTRAINT FK_2F84F8E96B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE maintenance DROP FOREIGN KEY FK_2F84F8E96B3CA4B');
        $this->addSql('DROP TABLE maintenance');
    }
}
