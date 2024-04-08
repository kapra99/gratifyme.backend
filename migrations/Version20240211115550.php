<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240211115550 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip_method ADD user_id uuid DEFAULT NULL');
        $this->addSql('ALTER TABLE tip_method ADD CONSTRAINT FK_7ABEE966A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('CREATE INDEX IDX_7ABEE966A76ED395 ON tip_method (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649897EA4F6');
        $this->addSql('DROP INDEX IDX_8D93D649897EA4F6 ON user');
        $this->addSql('ALTER TABLE user DROP tip_method_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tip_method DROP FOREIGN KEY FK_7ABEE966A76ED395');
        $this->addSql('DROP INDEX IDX_7ABEE966A76ED395 ON tip_method');
        $this->addSql('ALTER TABLE tip_method DROP user_id');
        $this->addSql('ALTER TABLE `user` ADD tip_method_id uuid DEFAULT NULL');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649897EA4F6 FOREIGN KEY (tip_method_id) REFERENCES tip_method (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649897EA4F6 ON `user` (tip_method_id)');
    }
}
