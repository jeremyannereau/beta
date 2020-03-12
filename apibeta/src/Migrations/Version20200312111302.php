<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200312111302 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE formation_users (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_users_formation (formation_users_id INT NOT NULL, formation_id INT NOT NULL, INDEX IDX_B5E3F0B3A021E3CC (formation_users_id), INDEX IDX_B5E3F0B35200282E (formation_id), PRIMARY KEY(formation_users_id, formation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_users_user (formation_users_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_BAB24B7EA021E3CC (formation_users_id), INDEX IDX_BAB24B7EA76ED395 (user_id), PRIMARY KEY(formation_users_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE formation_users_formation ADD CONSTRAINT FK_B5E3F0B3A021E3CC FOREIGN KEY (formation_users_id) REFERENCES formation_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_users_formation ADD CONSTRAINT FK_B5E3F0B35200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_users_user ADD CONSTRAINT FK_BAB24B7EA021E3CC FOREIGN KEY (formation_users_id) REFERENCES formation_users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_users_user ADD CONSTRAINT FK_BAB24B7EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE formation_users_formation DROP FOREIGN KEY FK_B5E3F0B3A021E3CC');
        $this->addSql('ALTER TABLE formation_users_user DROP FOREIGN KEY FK_BAB24B7EA021E3CC');
        $this->addSql('DROP TABLE formation_users');
        $this->addSql('DROP TABLE formation_users_formation');
        $this->addSql('DROP TABLE formation_users_user');
    }
}
