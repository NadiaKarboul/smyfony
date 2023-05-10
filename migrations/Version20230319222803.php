<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319222803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event (id_e INT AUTO_INCREMENT NOT NULL, nom_event VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, lieu_event VARCHAR(255) NOT NULL, type_event VARCHAR(255) NOT NULL, image_event VARCHAR(255) NOT NULL, prix_event DOUBLE PRECISION NOT NULL, PRIMARY KEY(id_e)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE panier (id_panier INT NOT NULL, id_user INT NOT NULL, INDEX IDX_24CC0DF26B3CA4B (id_user), PRIMARY KEY(id_panier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ticket (id_ticket INT AUTO_INCREMENT NOT NULL, id_spectateur INT NOT NULL, id_e INT NOT NULL, num_ticket INT NOT NULL, image_qr VARCHAR(255) NOT NULL, INDEX IDX_97A0ADA3FB6B5FC1 (id_spectateur), INDEX IDX_97A0ADA3284FD025 (id_e), PRIMARY KEY(id_ticket)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF26B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3FB6B5FC1 FOREIGN KEY (id_spectateur) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3284FD025 FOREIGN KEY (id_e) REFERENCES event (id_e)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF26B3CA4B');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3FB6B5FC1');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3284FD025');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE panier');
        $this->addSql('DROP TABLE ticket');
    }
}
