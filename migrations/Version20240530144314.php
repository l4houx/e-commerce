<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240530144314 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE add_product_history (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_EDEB7BDE4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE app_layout_setting (id INT AUTO_INCREMENT NOT NULL, logo_name VARCHAR(255) DEFAULT NULL, favicon_name VARCHAR(255) DEFAULT NULL, og_image_name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_1C52F9585E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_64C19C15E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE color (id INT AUTO_INCREMENT NOT NULL, hex VARCHAR(128) NOT NULL, display_in_search SMALLINT NOT NULL, name VARCHAR(128) NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_665648E95E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, post_id INT DEFAULT NULL, author_id INT NOT NULL, published_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ip VARCHAR(46) DEFAULT NULL, is_approved TINYINT(1) DEFAULT 0 NOT NULL, is_rgpd TINYINT(1) DEFAULT 0 NOT NULL, content LONGTEXT NOT NULL, INDEX IDX_9474526C727ACA70 (parent_id), INDEX IDX_9474526C4B89032C (post_id), INDEX IDX_9474526CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coupon (id INT AUTO_INCREMENT NOT NULL, coupon_type_id INT NOT NULL, code VARCHAR(10) NOT NULL, content LONGTEXT NOT NULL, discount INT NOT NULL, max_usage INT NOT NULL, validity DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_valid TINYINT(1) DEFAULT 1 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_64BF3F0277153098 (code), INDEX IDX_64BF3F022A24CF05 (coupon_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE coupon_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_CCE08B2B5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, ccy VARCHAR(3) NOT NULL, symbol VARCHAR(50) DEFAULT NULL, UNIQUE INDEX UNIQ_6956883FD2D95D97 (ccy), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feature_value (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, feature_id INT NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_D429523D4584665A (product_id), INDEX IDX_D429523D60E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE homepage_hero_setting (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) DEFAULT NULL, paragraph LONGTEXT DEFAULT NULL, content LONGTEXT NOT NULL, custom_background_name VARCHAR(50) DEFAULT NULL, custom_block_one_name VARCHAR(50) DEFAULT NULL, custom_block_two_name VARCHAR(50) DEFAULT NULL, custom_block_three_name VARCHAR(50) DEFAULT NULL, show_search_box TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, countrycode_id INT DEFAULT NULL, coupon_id INT DEFAULT NULL, ref VARCHAR(20) NOT NULL, status INT NOT NULL, firstname VARCHAR(20) NOT NULL, lastname VARCHAR(20) NOT NULL, phone VARCHAR(20) NOT NULL, email VARCHAR(180) NOT NULL, street VARCHAR(50) NOT NULL, street2 VARCHAR(50) DEFAULT NULL, postalcode VARCHAR(5) NOT NULL, city VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_F5299398146F3EA3 (ref), UNIQUE INDEX UNIQ_F5299398E7927C74 (email), INDEX IDX_F52993989E91F2E7 (countrycode_id), INDEX IDX_F529939866C5951B (coupon_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, views INT DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_140AB6205E237E06 (name), UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, type_id INT DEFAULT NULL, category_id INT DEFAULT NULL, image_name VARCHAR(50) DEFAULT NULL, readtime INT DEFAULT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, content LONGTEXT NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, views INT DEFAULT NULL, tags VARCHAR(500) DEFAULT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, published_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_5A8A6C8D5E237E06 (name), UNIQUE INDEX UNIQ_5A8A6C8D989D9B62 (slug), INDEX IDX_5A8A6C8DC54C8C93 (type_id), INDEX IDX_5A8A6C8D12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_category (id INT AUTO_INCREMENT NOT NULL, posts_count INT UNSIGNED NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_B9A190605E237E06 (name), UNIQUE INDEX UNIQ_B9A19060989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE post_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_458B30225E237E06 (name), UNIQUE INDEX UNIQ_458B3022989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, isonhomepageslider_id INT DEFAULT NULL, image_name VARCHAR(50) DEFAULT NULL, image_size INT DEFAULT NULL, image_mime_type VARCHAR(50) DEFAULT NULL, image_original_name TEXT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', content LONGTEXT DEFAULT NULL, ref VARCHAR(20) NOT NULL, discount DOUBLE PRECISION NOT NULL, discount_type VARCHAR(50) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, sale_price DOUBLE PRECISION NOT NULL, tax DOUBLE PRECISION NOT NULL, stock INT NOT NULL, is_on_sale TINYINT(1) DEFAULT 1 NOT NULL, is_featured_product TINYINT(1) DEFAULT 0 NOT NULL, is_best_selling TINYINT(1) DEFAULT 0 NOT NULL, is_new_arrival TINYINT(1) DEFAULT 0 NOT NULL, enablereviews TINYINT(1) DEFAULT 1 NOT NULL, name VARCHAR(128) NOT NULL, meta_title VARCHAR(255) DEFAULT NULL, meta_description VARCHAR(255) DEFAULT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, views INT DEFAULT NULL, externallink VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, email VARCHAR(50) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, youtubeurl VARCHAR(255) DEFAULT NULL, twitterurl VARCHAR(255) DEFAULT NULL, instagramurl VARCHAR(255) DEFAULT NULL, facebookurl VARCHAR(255) DEFAULT NULL, googleplusurl VARCHAR(255) DEFAULT NULL, linkedinurl VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_D34A04AD146F3EA3 (ref), UNIQUE INDEX UNIQ_D34A04AD5E237E06 (name), INDEX IDX_D34A04AD44F5D008 (brand_id), INDEX IDX_D34A04AD376C51EF (isonhomepageslider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_sub_category (product_id INT NOT NULL, sub_category_id INT NOT NULL, INDEX IDX_3147D5F34584665A (product_id), INDEX IDX_3147D5F3F7BFE87C (sub_category_id), PRIMARY KEY(product_id, sub_category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE favorites (product_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_E46960F54584665A (product_id), INDEX IDX_E46960F5A76ED395 (user_id), PRIMARY KEY(product_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_image (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, image_name VARCHAR(50) DEFAULT NULL, image_size INT UNSIGNED DEFAULT NULL, image_mime_type VARCHAR(50) DEFAULT NULL, image_original_name TEXT DEFAULT NULL, image_dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', position INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_64617F034584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9F74B8985E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shipping (id INT AUTO_INCREMENT NOT NULL, shipping_cost DOUBLE PRECISION NOT NULL, name VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_2D1C17245E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE size (id INT AUTO_INCREMENT NOT NULL, display_in_search SMALLINT NOT NULL, name VARCHAR(128) NOT NULL, is_online TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_F7C0246A5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sub_category (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(128) NOT NULL, color VARCHAR(12) DEFAULT \'dark\', UNIQUE INDEX UNIQ_BCE3F7985E237E06 (name), INDEX IDX_BCE3F79812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, firstname VARCHAR(20) NOT NULL, lastname VARCHAR(20) NOT NULL, username VARCHAR(30) NOT NULL, email VARCHAR(180) NOT NULL, avatar LONGTEXT DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_suspended TINYINT(1) DEFAULT 0 NOT NULL, is_verified TINYINT(1) DEFAULT 0 NOT NULL, is_agree_terms TINYINT(1) DEFAULT 0 NOT NULL, bill JSON DEFAULT NULL, last_login DATETIME DEFAULT NULL, last_login_ip VARCHAR(255) DEFAULT NULL, register_duration INT DEFAULT 0 NOT NULL, reset_token VARCHAR(255) DEFAULT NULL, about LONGTEXT DEFAULT NULL, designation VARCHAR(255) DEFAULT NULL, is_team TINYINT(1) DEFAULT 0 NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE add_product_history ADD CONSTRAINT FK_EDEB7BDE4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C727ACA70 FOREIGN KEY (parent_id) REFERENCES comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C4B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF675F31B FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F022A24CF05 FOREIGN KEY (coupon_type_id) REFERENCES coupon_type (id)');
        $this->addSql('ALTER TABLE feature_value ADD CONSTRAINT FK_D429523D4584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feature_value ADD CONSTRAINT FK_D429523D60E4B879 FOREIGN KEY (feature_id) REFERENCES feature (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F52993989E91F2E7 FOREIGN KEY (countrycode_id) REFERENCES shipping (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F529939866C5951B FOREIGN KEY (coupon_id) REFERENCES coupon (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DC54C8C93 FOREIGN KEY (type_id) REFERENCES post_type (id)');
        $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8D12469DE2 FOREIGN KEY (category_id) REFERENCES post_category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD376C51EF FOREIGN KEY (isonhomepageslider_id) REFERENCES homepage_hero_setting (id)');
        $this->addSql('ALTER TABLE product_sub_category ADD CONSTRAINT FK_3147D5F34584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product_sub_category ADD CONSTRAINT FK_3147D5F3F7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F54584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE favorites ADD CONSTRAINT FK_E46960F5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE product_image ADD CONSTRAINT FK_64617F034584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F79812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE add_product_history DROP FOREIGN KEY FK_EDEB7BDE4584665A');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C727ACA70');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C4B89032C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF675F31B');
        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F022A24CF05');
        $this->addSql('ALTER TABLE feature_value DROP FOREIGN KEY FK_D429523D4584665A');
        $this->addSql('ALTER TABLE feature_value DROP FOREIGN KEY FK_D429523D60E4B879');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F52993989E91F2E7');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F529939866C5951B');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8DC54C8C93');
        $this->addSql('ALTER TABLE post DROP FOREIGN KEY FK_5A8A6C8D12469DE2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD376C51EF');
        $this->addSql('ALTER TABLE product_sub_category DROP FOREIGN KEY FK_3147D5F34584665A');
        $this->addSql('ALTER TABLE product_sub_category DROP FOREIGN KEY FK_3147D5F3F7BFE87C');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F54584665A');
        $this->addSql('ALTER TABLE favorites DROP FOREIGN KEY FK_E46960F5A76ED395');
        $this->addSql('ALTER TABLE product_image DROP FOREIGN KEY FK_64617F034584665A');
        $this->addSql('ALTER TABLE sub_category DROP FOREIGN KEY FK_BCE3F79812469DE2');
        $this->addSql('DROP TABLE add_product_history');
        $this->addSql('DROP TABLE app_layout_setting');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE color');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE coupon');
        $this->addSql('DROP TABLE coupon_type');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE feature');
        $this->addSql('DROP TABLE feature_value');
        $this->addSql('DROP TABLE homepage_hero_setting');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE post');
        $this->addSql('DROP TABLE post_category');
        $this->addSql('DROP TABLE post_type');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_sub_category');
        $this->addSql('DROP TABLE favorites');
        $this->addSql('DROP TABLE product_image');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE shipping');
        $this->addSql('DROP TABLE size');
        $this->addSql('DROP TABLE sub_category');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}