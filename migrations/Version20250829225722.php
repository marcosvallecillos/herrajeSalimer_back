<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250829225722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE herrajes (id INT AUTO_INCREMENT NOT NULL, mueble_id_id INT DEFAULT NULL, tipo VARCHAR(255) NOT NULL, cantidad INT NOT NULL, INDEX IDX_6D0679B41D7236F0 (mueble_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mueble (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL, num_pieces INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE herrajes ADD CONSTRAINT FK_6D0679B41D7236F0 FOREIGN KEY (mueble_id_id) REFERENCES mueble (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE herrajes DROP FOREIGN KEY FK_6D0679B41D7236F0');
        $this->addSql('DROP TABLE herrajes');
        $this->addSql('DROP TABLE mueble');
    }
}
