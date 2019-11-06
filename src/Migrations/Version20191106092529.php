<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191106092529 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, name, color, race, image, owner FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, race VARCHAR(255) NOT NULL COLLATE BINARY, owner VARCHAR(255) NOT NULL COLLATE BINARY, filename VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO cat (id, name, color, race, filename, owner) SELECT id, name, color, race, image, owner FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, name, color, race, filename, owner FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, race VARCHAR(255) NOT NULL, owner VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO cat (id, name, color, race, image, owner) SELECT id, name, color, race, filename, owner FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
    }
}
