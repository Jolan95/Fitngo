<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220724232017 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE franchise (id INT SERIAL NOT NULL, permit_id INT NOT NULL, is_active TINYINT(1) NOT NULL, last_connection DATE NOT NULL, UNIQUE INDEX UNIQ_66F6CE2AA8439AF7 (permit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permit (id INT SERIAL NOT NULL, newsletter TINYINT(1) NOT NULL, payment_online TINYINT(1) NOT NULL, team_schedule TINYINT(1) NOT NULL, live_chat TINYINT(1) NOT NULL, virtual_training TINYINT(1) NOT NULL, boolean TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE structure (id INT SERIAL NOT NULL, franchise_id INT NOT NULL, permit_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, last_connection DATE DEFAULT NULL, INDEX IDX_6F0137EA523CAB89 (franchise_id), UNIQUE INDEX UNIQ_6F0137EAA8439AF7 (permit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT SERIAL NOT NULL, franchise_id INT DEFAULT NULL, structure_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(191) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649523CAB89 (franchise_id), UNIQUE INDEX UNIQ_8D93D6492534008B (structure_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE franchise ADD CONSTRAINT FK_66F6CE2AA8439AF7 FOREIGN KEY (permit_id) REFERENCES permit (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EA523CAB89 FOREIGN KEY (franchise_id) REFERENCES franchise (id)');
        $this->addSql('ALTER TABLE structure ADD CONSTRAINT FK_6F0137EAA8439AF7 FOREIGN KEY (permit_id) REFERENCES permit (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D649523CAB89 FOREIGN KEY (franchise_id) REFERENCES franchise (id)');
        $this->addSql('ALTER TABLE `user` ADD CONSTRAINT FK_8D93D6492534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EA523CAB89');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649523CAB89');
        $this->addSql('ALTER TABLE franchise DROP FOREIGN KEY FK_66F6CE2AA8439AF7');
        $this->addSql('ALTER TABLE structure DROP FOREIGN KEY FK_6F0137EAA8439AF7');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6492534008B');
        $this->addSql('DROP TABLE franchise');
        $this->addSql('DROP TABLE permit');
        $this->addSql('DROP TABLE structure');
        $this->addSql('DROP TABLE `user`');
    }
}
