<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230813151833 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users_subscription ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN users_subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
//        $this->addSql('ALTER INDEX idx_38236d677e3c61f9 RENAME TO IDX_F08242DF7E3C61F9');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
//        $this->addSql('CREATE SCHEMA public');
//        $this->addSql('ALTER TABLE users_subscription DROP created_at');
//        $this->addSql('ALTER INDEX idx_f08242df7e3c61f9 RENAME TO idx_38236d677e3c61f9');
    }
}
