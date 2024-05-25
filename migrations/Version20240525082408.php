<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240525082408 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD enablereviews TINYINT(1) DEFAULT 1 NOT NULL, ADD externallink VARCHAR(255) DEFAULT NULL, ADD website VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(50) DEFAULT NULL, ADD phone VARCHAR(50) DEFAULT NULL, ADD youtubeurl VARCHAR(255) DEFAULT NULL, ADD twitterurl VARCHAR(255) DEFAULT NULL, ADD instagramurl VARCHAR(255) DEFAULT NULL, ADD facebookurl VARCHAR(255) DEFAULT NULL, ADD googleplusurl VARCHAR(255) DEFAULT NULL, ADD linkedinurl VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP enablereviews, DROP externallink, DROP website, DROP email, DROP phone, DROP youtubeurl, DROP twitterurl, DROP instagramurl, DROP facebookurl, DROP googleplusurl, DROP linkedinurl');
    }
}
