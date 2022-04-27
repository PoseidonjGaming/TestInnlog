<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220427085109 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE parcour (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, type_sortie_id INT NOT NULL, duree INT DEFAULT NULL, commentaire VARCHAR(255) DEFAULT NULL, heure_debut DATETIME DEFAULT NULL, distance DOUBLE PRECISION DEFAULT NULL, INDEX IDX_B7E52956A76ED395 (user_id), INDEX IDX_B7E5295678B49843 (type_sortie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type_sortie (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parcour ADD CONSTRAINT FK_B7E52956A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE parcour ADD CONSTRAINT FK_B7E5295678B49843 FOREIGN KEY (type_sortie_id) REFERENCES type_sortie (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE parcour DROP FOREIGN KEY FK_B7E5295678B49843');
        $this->addSql('ALTER TABLE parcour DROP FOREIGN KEY FK_B7E52956A76ED395');
        $this->addSql('DROP TABLE parcour');
        $this->addSql('DROP TABLE type_sortie');
        $this->addSql('DROP TABLE `user`');
    }
}
