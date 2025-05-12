<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326103117 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE artist_song (artist_id INT NOT NULL, song_id INT NOT NULL, INDEX IDX_8F53683EB7970CF8 (artist_id), INDEX IDX_8F53683EA0BDB2F3 (song_id), PRIMARY KEY(artist_id, song_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE artist_song ADD CONSTRAINT FK_8F53683EB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE artist_song ADD CONSTRAINT FK_8F53683EA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_artist MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `primary` ON song_artist');
        $this->addSql('ALTER TABLE song_artist DROP id');
        $this->addSql('ALTER TABLE song_artist ADD CONSTRAINT FK_722870DA0BDB2F3 FOREIGN KEY (song_id) REFERENCES song (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE song_artist ADD CONSTRAINT FK_722870DB7970CF8 FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_722870DA0BDB2F3 ON song_artist (song_id)');
        $this->addSql('CREATE INDEX IDX_722870DB7970CF8 ON song_artist (artist_id)');
        $this->addSql('ALTER TABLE song_artist ADD PRIMARY KEY (song_id, artist_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE artist_song DROP FOREIGN KEY FK_8F53683EB7970CF8');
        $this->addSql('ALTER TABLE artist_song DROP FOREIGN KEY FK_8F53683EA0BDB2F3');
        $this->addSql('DROP TABLE artist_song');
        $this->addSql('ALTER TABLE song_artist DROP FOREIGN KEY FK_722870DA0BDB2F3');
        $this->addSql('ALTER TABLE song_artist DROP FOREIGN KEY FK_722870DB7970CF8');
        $this->addSql('DROP INDEX IDX_722870DA0BDB2F3 ON song_artist');
        $this->addSql('DROP INDEX IDX_722870DB7970CF8 ON song_artist');
        $this->addSql('ALTER TABLE song_artist ADD id INT AUTO_INCREMENT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }
}
