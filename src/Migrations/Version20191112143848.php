<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191112143848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, user_id, name, color, race, filename, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, race VARCHAR(255) NOT NULL COLLATE BINARY, filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, updated_at DATETIME NOT NULL, CONSTRAINT FK_9E5E43A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cat (id, user_id, name, color, race, filename, updated_at) SELECT id, user_id, name, color, race, filename, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, user_id, name, color, race, filename, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, race VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, user_id INTEGER NOT NULL, owner VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO cat (id, user_id, name, color, race, filename, updated_at) SELECT id, user_id, name, color, race, filename, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
    }
}
