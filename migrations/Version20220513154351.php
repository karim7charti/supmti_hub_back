<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513154351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_room ADD last_message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE chat_room ADD CONSTRAINT FK_D403CCDABA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D403CCDABA0E79C3 ON chat_room (last_message_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_room DROP FOREIGN KEY FK_D403CCDABA0E79C3');
        $this->addSql('DROP INDEX UNIQ_D403CCDABA0E79C3 ON chat_room');
        $this->addSql('ALTER TABLE chat_room DROP last_message_id');
    }
}
