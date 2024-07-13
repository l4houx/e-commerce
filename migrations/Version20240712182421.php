<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712182421 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE help_center_article DROP featuredorder');
        $this->addSql('ALTER TABLE `order` ADD note LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE product ADD is_featured TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE help_center_article ADD featuredorder INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `order` DROP note');
        $this->addSql('ALTER TABLE product DROP is_featured');
    }
}
