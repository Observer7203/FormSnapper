<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250226214345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form ADD is_scorable TINYINT(1) DEFAULT 0 NOT NULL, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
        $this->addSql('ALTER TABLE question ADD is_scorable TINYINT(1) DEFAULT 0 NOT NULL, ADD max_score INT DEFAULT NULL, CHANGE options options JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE response CHANGE answers answers JSON NOT NULL, CHANGE scores scores JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question DROP is_scorable, DROP max_score, CHANGE options options LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE form DROP is_scorable, CHANGE created_at created_at DATETIME DEFAULT \'current_timestamp()\' NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE response CHANGE answers answers LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE scores scores LONGTEXT DEFAULT NULL COLLATE `utf8mb4_bin`');
    }
}
