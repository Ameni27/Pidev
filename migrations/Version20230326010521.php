<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230326010521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parapharmacie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, phone INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY fk_idmedecin');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY fk_idpatient');
        $this->addSql('ALTER TABLE appointment CHANGE idPatient idPatient INT DEFAULT NULL, CHANGE idMedecin idMedecin INT DEFAULT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844B633834 FOREIGN KEY (idMedecin) REFERENCES user (iduser)');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F844A63BC19 FOREIGN KEY (idPatient) REFERENCES user (iduser)');
        $this->addSql('ALTER TABLE appointment RENAME INDEX fk_idmedecin TO IDX_FE38F844B633834');
        $this->addSql('ALTER TABLE appointment RENAME INDEX fk_idpatient TO IDX_FE38F844A63BC19');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE parapharmacie');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844B633834');
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F844A63BC19');
        $this->addSql('ALTER TABLE appointment CHANGE idMedecin idMedecin INT NOT NULL, CHANGE idPatient idPatient INT NOT NULL');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT fk_idmedecin FOREIGN KEY (idMedecin) REFERENCES user (idUser) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT fk_idpatient FOREIGN KEY (idPatient) REFERENCES user (idUser) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE appointment RENAME INDEX idx_fe38f844a63bc19 TO fk_idpatient');
        $this->addSql('ALTER TABLE appointment RENAME INDEX idx_fe38f844b633834 TO fk_idmedecin');
    }
}
