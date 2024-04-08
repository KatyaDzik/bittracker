<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240407110729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create `torrent_file` table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE torrent_file_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE torrent_file (id INT NOT NULL, author_id INT DEFAULT NULL, category_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, file VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_77F5C74AF675F31B ON torrent_file (author_id)');
        $this->addSql('CREATE INDEX IDX_77F5C74A12469DE2 ON torrent_file (category_id)');
        $this->addSql('ALTER TABLE torrent_file ADD CONSTRAINT FK_77F5C74AF675F31B FOREIGN KEY (author_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE torrent_file ADD CONSTRAINT FK_77F5C74A12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE torrent_file_id_seq CASCADE');
        $this->addSql('ALTER TABLE torrent_file DROP CONSTRAINT FK_77F5C74AF675F31B');
        $this->addSql('ALTER TABLE torrent_file DROP CONSTRAINT FK_77F5C74A12469DE2');
        $this->addSql('DROP TABLE torrent_file');
    }
}
