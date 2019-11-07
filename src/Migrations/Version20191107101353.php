<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191107101353 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, name, color, race, owner, filename, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, race VARCHAR(255) NOT NULL COLLATE BINARY, owner VARCHAR(255) NOT NULL COLLATE BINARY, filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, updated_at DATETIME NOT NULL)');
        $this->addSql('INSERT INTO cat (id, name, color, race, owner, filename, updated_at) SELECT id, name, color, race, owner, filename, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, name, color, race, filename, owner, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, race VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, owner VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO cat (id, name, color, race, filename, owner, updated_at) SELECT id, name, color, race, filename, owner, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
    }
}
