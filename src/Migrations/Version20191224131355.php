<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191224131355 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, password VARCHAR(255) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , is_active BOOLEAN DEFAULT \'1\' NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE TABLE cat (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, race VARCHAR(255) NOT NULL, filename VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_9E5E43A8A76ED395 ON cat (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'sqlite', 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE cat');
    }
}
