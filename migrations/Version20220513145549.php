<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513145549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_room (id INT AUTO_INCREMENT NOT NULL, last_message_id INT NOT NULL, UNIQUE INDEX UNIQ_D403CCDABA0E79C3 (last_message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_room_members (id INT AUTO_INCREMENT NOT NULL, chat_room_id INT NOT NULL, member_id INT NOT NULL, INDEX IDX_5769079E1819BCFA (chat_room_id), INDEX IDX_5769079E7597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat_room ADD CONSTRAINT FK_D403CCDABA0E79C3 FOREIGN KEY (last_message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE chat_room_members ADD CONSTRAINT FK_5769079E1819BCFA FOREIGN KEY (chat_room_id) REFERENCES chat_room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_room_members ADD CONSTRAINT FK_5769079E7597D3FE FOREIGN KEY (member_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD chat_room_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1819BCFA FOREIGN KEY (chat_room_id) REFERENCES chat_room (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B6BD307F1819BCFA ON message (chat_room_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_room_members DROP FOREIGN KEY FK_5769079E1819BCFA');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1819BCFA');
        $this->addSql('DROP TABLE chat_room');
        $this->addSql('DROP TABLE chat_room_members');
        $this->addSql('DROP INDEX IDX_B6BD307F1819BCFA ON message');
        $this->addSql('ALTER TABLE message DROP chat_room_id');
    }
}
