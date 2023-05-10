<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319205748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id_user INT AUTO_INCREMENT NOT NULL, id_role INT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, mdp VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, num_tel INT NOT NULL, cin INT NOT NULL, quota DOUBLE PRECISION NOT NULL, img VARCHAR(255) NOT NULL, etat INT NOT NULL, INDEX IDX_8D93D649DC499668 (id_role), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649DC499668 FOREIGN KEY (id_role) REFERENCES role (id_role)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649DC499668');
        $this->addSql('DROP TABLE user');
    }
}
