<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319214205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bonplan (id_bonplan INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, nom_bonplan VARCHAR(50) NOT NULL, gouvernorat VARCHAR(50) NOT NULL, ville VARCHAR(50) NOT NULL, rue VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, type VARCHAR(50) NOT NULL, etat VARCHAR(20) NOT NULL, image VARCHAR(255) NOT NULL, INDEX IDX_A2FB72706B3CA4B (id_user), PRIMARY KEY(id_bonplan)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feedback (id_feedback INT AUTO_INCREMENT NOT NULL, id_user INT NOT NULL, id_bonplan INT NOT NULL, rate INT NOT NULL, commentaire VARCHAR(100) NOT NULL, report TINYINT(1) NOT NULL, INDEX IDX_D22944586B3CA4B (id_user), INDEX IDX_D22944587C66DB00 (id_bonplan), PRIMARY KEY(id_feedback)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bonplan ADD CONSTRAINT FK_A2FB72706B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944586B3CA4B FOREIGN KEY (id_user) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE feedback ADD CONSTRAINT FK_D22944587C66DB00 FOREIGN KEY (id_bonplan) REFERENCES bonplan (id_bonplan)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bonplan DROP FOREIGN KEY FK_A2FB72706B3CA4B');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944586B3CA4B');
        $this->addSql('ALTER TABLE feedback DROP FOREIGN KEY FK_D22944587C66DB00');
        $this->addSql('DROP TABLE bonplan');
        $this->addSql('DROP TABLE feedback');
    }
}
