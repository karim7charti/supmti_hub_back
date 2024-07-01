<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404145735 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, target_class_id INT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, title VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_169E6FB930ABDB79 (target_class_id), INDEX IDX_169E6FB9A76ED395 (user_id), UNIQUE INDEX UNIQ_169E6FB981C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, file_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8C9F361081C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE password_resets (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(100) NOT NULL, token VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poll (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, question VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_84BCFA45A76ED395 (user_id), UNIQUE INDEX UNIQ_84BCFA4581C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE poll_answer (id INT AUTO_INCREMENT NOT NULL, poll_id INT NOT NULL, answer VARCHAR(255) NOT NULL, votes INT NOT NULL, INDEX IDX_36D8097E3C947C0F (poll_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, activity_id INT NOT NULL, is_approved TINYINT(1) NOT NULL, INDEX IDX_5A8A6C8DA76ED395 (user_id), UNIQUE INDEX UNIQ_5A8A6C8D81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE target_class (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(30) NOT NULL, last_name VARCHAR(30) NOT NULL, profile_image_path VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_poll_votes (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, poll_id INT NOT NULL, poll_answer_id INT NOT NULL, INDEX IDX_905DC1E2A76ED395 (user_id), INDEX IDX_905DC1E23C947C0F (poll_id), INDEX IDX_905DC1E261E461F3 (poll_answer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB930ABDB79 FOREIGN KEY (target_class_id) REFERENCES target_class (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F361081C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE poll ADD CONSTRAINT FK_84BCFA45A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE poll ADD CONSTRAINT FK_84BCFA4581C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE poll_answer ADD CONSTRAINT FK_36D8097E3C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE user_poll_votes ADD CONSTRAINT FK_905DC1E2A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_poll_votes ADD CONSTRAINT FK_905DC1E23C947C0F FOREIGN KEY (poll_id) REFERENCES poll (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_poll_votes ADD CONSTRAINT FK_905DC1E261E461F3 FOREIGN KEY (poll_answer_id) REFERENCES poll_answer (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB981C06096');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F361081C06096');
        $this->addSql('ALTER TABLE poll DROP FOREIGN KEY FK_84BCFA4581C06096');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D81C06096');
        $this->addSql('ALTER TABLE poll_answer DROP FOREIGN KEY FK_36D8097E3C947C0F');
        $this->addSql('ALTER TABLE user_poll_votes DROP FOREIGN KEY FK_905DC1E23C947C0F');
        $this->addSql('ALTER TABLE user_poll_votes DROP FOREIGN KEY FK_905DC1E261E461F3');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB930ABDB79');
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9A76ED395');
        $this->addSql('ALTER TABLE poll DROP FOREIGN KEY FK_84BCFA45A76ED395');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DA76ED395');
        $this->addSql('ALTER TABLE user_poll_votes DROP FOREIGN KEY FK_905DC1E2A76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE password_resets');
        $this->addSql('DROP TABLE poll');
        $this->addSql('DROP TABLE poll_answer');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE target_class');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_poll_votes');
    }
}
