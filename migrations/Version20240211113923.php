<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211113923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id uuid NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goal (id uuid NOT NULL, user_id uuid DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, end_goal_sum DOUBLE PRECISION DEFAULT NULL, current_goal_sum DOUBLE PRECISION DEFAULT NULL, start_date VARCHAR(255) DEFAULT NULL, priority INT DEFAULT NULL, INDEX IDX_FCDCEB2EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id uuid NOT NULL, evaluated_user_id uuid DEFAULT NULL, message VARCHAR(255) DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, subreview TINYINT(1) DEFAULT NULL, INDEX IDX_794381C6452C2C51 (evaluated_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tip_method (id uuid NOT NULL, name VARCHAR(255) DEFAULT NULL, tip_method_url VARCHAR(255) DEFAULT NULL, tip_method_static_url VARCHAR(255) DEFAULT NULL, qr_code_img_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id uuid NOT NULL, tip_method_id uuid DEFAULT NULL, work_place_id uuid DEFAULT NULL, working_position_id uuid DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, sur_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, nick_name VARCHAR(255) DEFAULT NULL, date_of_birth VARCHAR(255) DEFAULT NULL, rating INT DEFAULT NULL, avatar_img_path VARCHAR(255) DEFAULT NULL, is_deleted TINYINT(1) DEFAULT 0 NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649897EA4F6 (tip_method_id), INDEX IDX_8D93D649D8132845 (work_place_id), INDEX IDX_8D93D649D2387950 (working_position_id), INDEX IDX_8D93D649FD07C8FB (is_deleted), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE work_place (id uuid NOT NULL, city_id uuid DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, worker_capacity INT DEFAULT NULL, INDEX IDX_5CE628E28BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE working_position (id uuid NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2EA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6452C2C51 FOREIGN KEY (evaluated_user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649897EA4F6 FOREIGN KEY (tip_method_id) REFERENCES tip_method (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649D8132845 FOREIGN KEY (work_place_id) REFERENCES work_place (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649D2387950 FOREIGN KEY (working_position_id) REFERENCES working_position (id)');
        $this->addSql('ALTER TABLE work_place ADD CONSTRAINT FK_5CE628E28BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2EA76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6452C2C51');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649897EA4F6');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649D8132845');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649D2387950');
        $this->addSql('ALTER TABLE work_place DROP FOREIGN KEY FK_5CE628E28BAC62AF');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tip_method');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE work_place');
        $this->addSql('DROP TABLE working_position');
    }
}
