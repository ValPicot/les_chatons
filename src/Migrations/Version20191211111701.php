<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191211111701 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('ALTER TABLE user ADD COLUMN is_active BOOLEAN DEFAULT \'1\' NOT NULL');
        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, user_id, name, color, race, filename, updated_at, created_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, race VARCHAR(255) NOT NULL COLLATE BINARY, filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, updated_at DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, CONSTRAINT FK_9E5E43A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cat (id, user_id, name, color, race, filename, updated_at, created_at) SELECT id, user_id, name, color, race, filename, updated_at, created_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE ext_translations (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, locale VARCHAR(8) NOT NULL COLLATE BINARY, object_class VARCHAR(255) NOT NULL COLLATE BINARY, field VARCHAR(32) NOT NULL COLLATE BINARY, foreign_key VARCHAR(64) NOT NULL COLLATE BINARY, content CLOB DEFAULT NULL COLLATE BINARY)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, user_id, name, color, race, filename, created_at, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, race VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO cat (id, user_id, name, color, race, filename, created_at, updated_at) SELECT id, user_id, name, color, race, filename, created_at, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, username, password, roles, created_at, updated_at FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, username, password, roles, created_at, updated_at) SELECT id, username, password, roles, created_at, updated_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
    }
}
