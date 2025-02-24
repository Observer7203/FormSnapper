<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223162035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE form (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, questions JSON NOT NULL, INDEX IDX_5288FD4FF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE response (id INT AUTO_INCREMENT NOT NULL, form_id INT NOT NULL, user_id INT NOT NULL, answers JSON NOT NULL, INDEX IDX_3E7B0BFB5FF69B7D (form_id), INDEX IDX_3E7B0BFBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE form ADD CONSTRAINT FK_5288FD4FF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFB5FF69B7D FOREIGN KEY (form_id) REFERENCES form (id)');
        $this->addSql('ALTER TABLE response ADD CONSTRAINT FK_3E7B0BFBA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users CHANGE status status VARCHAR(255) NOT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE form DROP FOREIGN KEY FK_5288FD4FF675F31B');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFB5FF69B7D');
        $this->addSql('ALTER TABLE response DROP FOREIGN KEY FK_3E7B0BFBA76ED395');
        $this->addSql('DROP TABLE form');
        $this->addSql('DROP TABLE response');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users CHANGE status status VARCHAR(50) NOT NULL, CHANGE last_login last_login DATETIME DEFAULT \'NULL\'');
    }
}
