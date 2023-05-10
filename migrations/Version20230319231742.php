<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319231742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE devis (id_devis INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_garage INT NOT NULL, id_maintenance INT NOT NULL, TVA INT NOT NULL, total DOUBLE PRECISION NOT NULL, INDEX IDX_8B27C52B6B3CA4B (id_user), INDEX IDX_8B27C52BB911D4E6 (id_garage), INDEX IDX_8B27C52B3796351E (id_maintenance), PRIMARY KEY(id_devis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BB911D4E6 FOREIGN KEY (id_garage) REFERENCES garagec (id_garage)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52B3796351E FOREIGN KEY (id_maintenance) REFERENCES maintenance (id_maintenance)');
        $this->addSql('ALTER TABLE maintenance CHANGE date_maintenance date_maintenance DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B6B3CA4B');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BB911D4E6');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52B3796351E');
        $this->addSql('DROP TABLE devis');
        $this->addSql('ALTER TABLE maintenance CHANGE date_maintenance date_maintenance DATE DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL');
    }
}
