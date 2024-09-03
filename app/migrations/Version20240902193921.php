<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240902193921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create `size` option for torrent';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE torrent_file ADD size DOUBLE PRECISION DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE torrent_file DROP size');
    }
}
