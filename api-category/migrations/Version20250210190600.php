<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250210190600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create category table with id and name fields';
    }

    public function up(Schema $schema): void
    {
        // Création de la table "category" avec les champs nécessaires
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // Suppression de la table "category"
        $this->addSql('DROP TABLE category');
    }
}
