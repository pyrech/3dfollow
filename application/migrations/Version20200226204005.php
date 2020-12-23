<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226204005 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE users (id INT NOT NULL, username VARCHAR(180) NOT NULL, is_admin BOOLEAN NOT NULL, is_printer BOOLEAN NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_1483a5e9f85e0677 ON users (username)');

        $this->addSql('CREATE TABLE print_item (id INT NOT NULL, user_id INT NOT NULL, filament_id INT DEFAULT NULL, team_id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, comment TEXT DEFAULT NULL, is_printed BOOLEAN NOT NULL, weight NUMERIC(10, 2) DEFAULT NULL, quantity INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_cb14675a296cd8ae ON print_item (team_id)');
        $this->addSql('CREATE INDEX idx_cb14675ab10541b1 ON print_item (filament_id)');
        $this->addSql('CREATE INDEX idx_cb14675aa76ed395 ON print_item (user_id)');

        $this->addSql('CREATE TABLE filament (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, weight NUMERIC(10, 0) NOT NULL, price NUMERIC(10, 2) NOT NULL, density NUMERIC(10, 2) NOT NULL, diameter NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_9daa3ba67e3c61f9 ON filament (owner_id)');

        $this->addSql('CREATE TABLE team (id INT NOT NULL, creator_id INT NOT NULL, join_token VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_c4e0a61f61220ea6 ON team (creator_id)');

        $this->addSql('CREATE TABLE team_user (team_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(team_id, user_id))');
        $this->addSql('CREATE INDEX idx_5c722232a76ed395 ON team_user (user_id)');
        $this->addSql('CREATE INDEX idx_5c722232296cd8ae ON team_user (team_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE print_item');
        $this->addSql('DROP TABLE filament');
        $this->addSql('DROP TABLE team');
        $this->addSql('DROP TABLE team_user');
    }
}
