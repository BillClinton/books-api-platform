<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200420200800 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
        $this->addSql('ALTER TABLE book ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A3317E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CBE5A3317E3C61F9 ON book (owner_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A3317E3C61F9');
        $this->addSql('DROP INDEX IDX_CBE5A3317E3C61F9 ON book');
        $this->addSql('ALTER TABLE book DROP owner_id');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
    }
}
