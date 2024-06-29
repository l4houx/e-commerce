<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240629142852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rules (id INT AUTO_INCREMENT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', content LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules_agreement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rules_id INT NOT NULL, accepted TINYINT(1) DEFAULT 0 NOT NULL, agreed_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D4CE6AF2A76ED395 (user_id), INDEX IDX_D4CE6AF2FB699244 (rules_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE rules_agreement ADD CONSTRAINT FK_D4CE6AF2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE rules_agreement ADD CONSTRAINT FK_D4CE6AF2FB699244 FOREIGN KEY (rules_id) REFERENCES rules (id)');
        $this->addSql('ALTER TABLE user_post_like DROP FOREIGN KEY FK_65D6AA5C4B89032C');
        $this->addSql('ALTER TABLE user_post_like DROP FOREIGN KEY FK_65D6AA5CA76ED395');
        $this->addSql('DROP TABLE user_post_like');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_post_like (post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_65D6AA5C4B89032C (post_id), INDEX IDX_65D6AA5CA76ED395 (user_id), PRIMARY KEY(post_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_post_like ADD CONSTRAINT FK_65D6AA5C4B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_post_like ADD CONSTRAINT FK_65D6AA5CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE rules_agreement DROP FOREIGN KEY FK_D4CE6AF2A76ED395');
        $this->addSql('ALTER TABLE rules_agreement DROP FOREIGN KEY FK_D4CE6AF2FB699244');
        $this->addSql('DROP TABLE rules');
        $this->addSql('DROP TABLE rules_agreement');
    }
}
