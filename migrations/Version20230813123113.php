<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813123113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE users_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users_subscription (id INT NOT NULL, owner_id INT NOT NULL, code VARCHAR(50) NOT NULL, active BOOLEAN NOT NULL, level INT NOT NULL, description JSON NOT NULL, subscription_begin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_38236D677E3C61F9 ON users_subscription (owner_id)');
        $this->addSql('ALTER TABLE users_subscription ADD CONSTRAINT FK_38236D677E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE users_subscription_id_seq CASCADE');
        $this->addSql('ALTER TABLE users_subscription DROP CONSTRAINT FK_38236D677E3C61F9');
        $this->addSql('DROP TABLE users_subscription');
    }
}
