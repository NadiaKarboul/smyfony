<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321114640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignepanier CHANGE description_prod description_prod LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE maintenance CHANGE date_maintenance date_maintenance DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lignepanier CHANGE description_prod description_prod TEXT NOT NULL');
        $this->addSql('ALTER TABLE maintenance CHANGE date_maintenance date_maintenance DATE DEFAULT \'CURRENT_TIMESTAMP\' NOT NULL');
    }
}
