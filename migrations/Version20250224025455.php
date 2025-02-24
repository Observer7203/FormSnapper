<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250224025455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form ADD is_public TINYINT(1) DEFAULT 0 NOT NULL, ADD created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE questions questions JSON NOT NULL');
        $this->addSql('ALTER TABLE response CHANGE answers answers JSON NOT NULL');
        $this->addSql('ALTER TABLE users ADD roles JSON NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE form DROP is_public, DROP created_at, CHANGE questions questions LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE users DROP roles, CHANGE last_login last_login DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE response CHANGE answers answers LONGTEXT NOT NULL COLLATE `utf8mb4_bin`');
    }
}
