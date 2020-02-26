<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226224607 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Fix SQL init';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 20');
        $this->addSql('CREATE SEQUENCE team_id_seq INCREMENT BY 1 MINVALUE 1 START 5');
        $this->addSql('CREATE SEQUENCE print_request_id_seq INCREMENT BY 1 MINVALUE 1 START 20');
        $this->addSql('CREATE SEQUENCE filament_id_seq INCREMENT BY 1 MINVALUE 1 START 5');
        $this->addSql('ALTER TABLE team ADD CONSTRAINT FK_C4E0A61F61220EA6 FOREIGN KEY (creator_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE team_user ADD CONSTRAINT FK_5C722232A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_request ADD CONSTRAINT FK_1EF49A41A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_request ADD CONSTRAINT FK_1EF49A41B10541B1 FOREIGN KEY (filament_id) REFERENCES filament (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE print_request ADD CONSTRAINT FK_1EF49A41296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER INDEX idx_cb14675aa76ed395 RENAME TO IDX_1EF49A41A76ED395');
        $this->addSql('ALTER INDEX idx_cb14675ab10541b1 RENAME TO IDX_1EF49A41B10541B1');
        $this->addSql('ALTER INDEX idx_cb14675a296cd8ae RENAME TO IDX_1EF49A41296CD8AE');
        $this->addSql('ALTER TABLE filament ADD CONSTRAINT FK_9DAA3BA67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE team_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE print_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE filament_id_seq CASCADE');
        $this->addSql('ALTER TABLE filament DROP CONSTRAINT FK_9DAA3BA67E3C61F9');
        $this->addSql('ALTER TABLE team DROP CONSTRAINT FK_C4E0A61F61220EA6');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232296CD8AE');
        $this->addSql('ALTER TABLE team_user DROP CONSTRAINT FK_5C722232A76ED395');
        $this->addSql('ALTER TABLE print_request DROP CONSTRAINT FK_1EF49A41A76ED395');
        $this->addSql('ALTER TABLE print_request DROP CONSTRAINT FK_1EF49A41B10541B1');
        $this->addSql('ALTER TABLE print_request DROP CONSTRAINT FK_1EF49A41296CD8AE');
        $this->addSql('ALTER INDEX idx_1ef49a41a76ed395 RENAME TO idx_cb14675aa76ed395');
        $this->addSql('ALTER INDEX idx_1ef49a41b10541b1 RENAME TO idx_cb14675ab10541b1');
        $this->addSql('ALTER INDEX idx_1ef49a41296cd8ae RENAME TO idx_cb14675a296cd8ae');
    }
}
