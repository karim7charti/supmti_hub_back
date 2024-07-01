<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530153527 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAC54C8C93');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA158E0B66');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE55BD123');
        $this->addSql('ALTER TABLE notification ADD activity_id INT NOT NULL, ADD path VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAC54C8C93 FOREIGN KEY (type_id) REFERENCES notification_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA158E0B66 FOREIGN KEY (target_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE55BD123 FOREIGN KEY (notifier_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_BF5476CA81C06096 ON notification (activity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA81C06096');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA158E0B66');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAE55BD123');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAC54C8C93');
        $this->addSql('DROP INDEX IDX_BF5476CA81C06096 ON notification');
        $this->addSql('ALTER TABLE notification DROP activity_id, DROP path');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAE55BD123 FOREIGN KEY (notifier_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAC54C8C93 FOREIGN KEY (type_id) REFERENCES notification_type (id)');
    }
}
