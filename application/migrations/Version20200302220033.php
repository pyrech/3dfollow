<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200302220033 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add g-code upload';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE print_object ADD uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE print_object ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object ADD g_code_original_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object ADD g_code_mime_type VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object ADD g_code_size INT DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object ADD g_code_dimensions TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object RENAME COLUMN file_name TO g_code_name');
        $this->addSql('COMMENT ON COLUMN print_object.g_code_dimensions IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE print_object ADD file_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE print_object DROP uuid');
        $this->addSql('ALTER TABLE print_object DROP updated_at');
        $this->addSql('ALTER TABLE print_object DROP g_code_name');
        $this->addSql('ALTER TABLE print_object DROP g_code_original_name');
        $this->addSql('ALTER TABLE print_object DROP g_code_mime_type');
        $this->addSql('ALTER TABLE print_object DROP g_code_size');
        $this->addSql('ALTER TABLE print_object DROP g_code_dimensions');
    }
}
