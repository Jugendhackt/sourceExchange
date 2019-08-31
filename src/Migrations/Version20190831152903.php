<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190831152903 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic_user CHANGE topic_id topic_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE topic_user ADD CONSTRAINT FK_B578B7FCC4773235 FOREIGN KEY (topic_id_id) REFERENCES topic (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B578B7FCC4773235 ON topic_user (topic_id_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE topic_user DROP FOREIGN KEY FK_B578B7FCC4773235');
        $this->addSql('DROP INDEX UNIQ_B578B7FCC4773235 ON topic_user');
        $this->addSql('ALTER TABLE topic_user CHANGE topic_id_id topic_id INT NOT NULL');
    }
}
