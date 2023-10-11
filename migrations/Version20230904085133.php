<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904085133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_subscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_subscription_id_seq CASCADE');
        $this->addSql('ALTER TABLE user_subscription DROP CONSTRAINT fk_230a18d17e3c61f9');
        $this->addSql('DROP TABLE user_subscription');
        $this->addSql('DROP TABLE type_subscription');
        $this->addSql('ALTER TABLE "user" DROP subscription_level');
        $this->addSql('ALTER TABLE "user" DROP subscription_begin');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE user_subscription (id INT NOT NULL, owner_id INT NOT NULL, code VARCHAR(50) NOT NULL, active BOOLEAN NOT NULL, level INT NOT NULL, description JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_230a18d17e3c61f9 ON user_subscription (owner_id)');
        $this->addSql('COMMENT ON COLUMN user_subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_subscription.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE type_subscription (id INT NOT NULL, code VARCHAR(50) NOT NULL, description JSON NOT NULL, active BOOLEAN NOT NULL, level INT NOT NULL, is_default BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT fk_230a18d17e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD subscription_level VARCHAR(90) NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD subscription_begin TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }
}
