<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230904081346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_a3c664d37e3c61f9');
        $this->addSql('ALTER TABLE subscription ALTER updated_at DROP NOT NULL');
        $this->addSql('CREATE INDEX IDX_A3C664D37E3C61F9 ON subscription (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_A3C664D37E3C61F9');
        $this->addSql('ALTER TABLE subscription ALTER updated_at SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_a3c664d37e3c61f9 ON subscription (owner_id)');
    }
}
