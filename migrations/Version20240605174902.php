<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240605174902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP enabletestimonials');
        $this->addSql('ALTER TABLE testimonial DROP FOREIGN KEY FK_E6BDCDF74584665A');
        $this->addSql('DROP INDEX IDX_E6BDCDF74584665A ON testimonial');
        $this->addSql('ALTER TABLE testimonial DROP product_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD enabletestimonials TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE testimonial ADD product_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE testimonial ADD CONSTRAINT FK_E6BDCDF74584665A FOREIGN KEY (product_id) REFERENCES product (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E6BDCDF74584665A ON testimonial (product_id)');
    }
}
