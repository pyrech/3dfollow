<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200229222654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add PrintObject entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE print_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE print_object (id INT NOT NULL, filament_id INT NOT NULL, user_id INT NOT NULL, print_request_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, file_name VARCHAR(255) DEFAULT NULL, quantity INT NOT NULL, length NUMERIC(10, 2) DEFAULT NULL, cost NUMERIC(10, 2) DEFAULT NULL, printed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A46AD55EB10541B1 ON print_object (filament_id)');
        $this->addSql('CREATE INDEX IDX_A46AD55EA76ED395 ON print_object (user_id)');
        $this->addSql('CREATE INDEX IDX_A46AD55E2667764C ON print_object (print_request_id)');
        $this->addSql('ALTER TABLE print_object ADD CONSTRAINT FK_A46AD55EB10541B1 FOREIGN KEY (filament_id) REFERENCES filament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_object ADD CONSTRAINT FK_A46AD55EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_object ADD CONSTRAINT FK_A46AD55E2667764C FOREIGN KEY (print_request_id) REFERENCES print_request (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_request DROP CONSTRAINT fk_1ef49a41b10541b1');
        $this->addSql('DROP INDEX idx_1ef49a41b10541b1');
        $this->addSql('ALTER TABLE print_request DROP filament_id');
        $this->addSql('ALTER TABLE print_request DROP weight');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE print_object_id_seq CASCADE');
        $this->addSql('DROP TABLE print_object');
        $this->addSql('ALTER TABLE print_request ADD filament_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE print_request ADD weight NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE print_request ADD CONSTRAINT fk_1ef49a41b10541b1 FOREIGN KEY (filament_id) REFERENCES filament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_1ef49a41b10541b1 ON print_request (filament_id)');
    }
}
