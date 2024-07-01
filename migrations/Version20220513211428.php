<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220513211428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_views DROP FOREIGN KEY FK_EE56636C537A1329');
        $this->addSql('ALTER TABLE message_views DROP FOREIGN KEY FK_EE56636CA76ED395');
        $this->addSql('ALTER TABLE message_views ADD CONSTRAINT FK_EE56636C537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_views ADD CONSTRAINT FK_EE56636CA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_views DROP FOREIGN KEY FK_EE56636CA76ED395');
        $this->addSql('ALTER TABLE message_views DROP FOREIGN KEY FK_EE56636C537A1329');
        $this->addSql('ALTER TABLE message_views ADD CONSTRAINT FK_EE56636CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message_views ADD CONSTRAINT FK_EE56636C537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
    }
}
