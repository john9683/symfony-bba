<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230910135449 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP images');
        $this->addSql('ALTER TABLE images DROP CONSTRAINT fk_e01fbe6a7294869c');
        $this->addSql('DROP INDEX uniq_e01fbe6a7294869c');
        $this->addSql('ALTER TABLE images DROP article_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE images ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT fk_e01fbe6a7294869c FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_e01fbe6a7294869c ON images (article_id)');
        $this->addSql('ALTER TABLE article ADD images JSON DEFAULT NULL');
    }
}
