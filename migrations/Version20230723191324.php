<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230723191324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ADD theme VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE article ADD keyword JSON NOT NULL');
        $this->addSql('ALTER TABLE article ADD size JSON NOT NULL');
        $this->addSql('ALTER TABLE module ALTER title SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "module" ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE article DROP theme');
        $this->addSql('ALTER TABLE article DROP title');
        $this->addSql('ALTER TABLE article DROP keyword');
        $this->addSql('ALTER TABLE article DROP size');
    }
}
