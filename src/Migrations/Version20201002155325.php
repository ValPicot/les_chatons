<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201002155325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('ALTER TABLE user ADD COLUMN dark_mode BOOLEAN DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('DROP INDEX IDX_9E5E43A86E59D40D');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, user_id, race_id, name, color, filename, created_at, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, race_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, color VARCHAR(255) DEFAULT NULL COLLATE BINARY, filename VARCHAR(255) DEFAULT NULL COLLATE BINARY, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, CONSTRAINT FK_9E5E43A86E59D40D FOREIGN KEY (race_id) REFERENCES race (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_9E5E43A8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO cat (id, user_id, race_id, name, color, filename, created_at, updated_at) SELECT id, user_id, race_id, name, color, filename, created_at, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
        $this->addSql('CREATE INDEX IDX_9E5E43A86E59D40D ON cat (race_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP INDEX IDX_9E5E43A86E59D40D');
        $this->addSql('DROP INDEX IDX_9E5E43A8A76ED395');
        $this->addSql('CREATE TEMPORARY TABLE __temp__cat AS SELECT id, race_id, user_id, name, color, filename, created_at, updated_at FROM cat');
        $this->addSql('DROP TABLE cat');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, race_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO cat (id, race_id, user_id, name, color, filename, created_at, updated_at) SELECT id, race_id, user_id, name, color, filename, created_at, updated_at FROM __temp__cat');
        $this->addSql('DROP TABLE __temp__cat');
        $this->addSql('CREATE INDEX IDX_9E5E43A86E59D40D ON cat (race_id)');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
        $this->addSql('DROP INDEX UNIQ_8D93D6497BA2F5EB');
        $this->addSql('CREATE TEMPORARY TABLE __temp__user AS SELECT id, password, roles, is_active, email, name, lastname, reset_token, api_token, created_at, updated_at FROM user');
        $this->addSql('DROP TABLE user');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , is_active BOOLEAN DEFAULT \'1\' NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, api_token VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO user (id, password, roles, is_active, email, name, lastname, reset_token, api_token, created_at, updated_at) SELECT id, password, roles, is_active, email, name, lastname, reset_token, api_token, created_at, updated_at FROM __temp__user');
        $this->addSql('DROP TABLE __temp__user');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6497BA2F5EB ON user (api_token)');
    }
}
