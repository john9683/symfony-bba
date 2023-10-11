<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813152330 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE users_subscription_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_subscription (id INT NOT NULL, owner_id INT NOT NULL, code VARCHAR(50) NOT NULL, active BOOLEAN NOT NULL, level INT NOT NULL, description JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_230A18D17E3C61F9 ON user_subscription (owner_id)');
        $this->addSql('COMMENT ON COLUMN user_subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_subscription DROP CONSTRAINT fk_38236d677e3c61f9');
        $this->addSql('DROP TABLE users_subscription');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE user_subscription_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE users_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE users_subscription (id INT NOT NULL, owner_id INT NOT NULL, code VARCHAR(50) NOT NULL, active BOOLEAN NOT NULL, level INT NOT NULL, description JSON NOT NULL, subscription_begin TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f08242df7e3c61f9 ON users_subscription (owner_id)');
        $this->addSql('COMMENT ON COLUMN users_subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users_subscription ADD CONSTRAINT fk_38236d677e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_subscription DROP CONSTRAINT FK_230A18D17E3C61F9');
        $this->addSql('DROP TABLE user_subscription');
    }
}
