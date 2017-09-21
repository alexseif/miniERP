<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170920063134 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE product_vendors (products_id INT NOT NULL, vendors_id INT NOT NULL, INDEX IDX_9EFCA1A16C8A81A9 (products_id), INDEX IDX_9EFCA1A12E8B9E4D (vendors_id), PRIMARY KEY(products_id, vendors_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE product_vendors ADD CONSTRAINT FK_9EFCA1A16C8A81A9 FOREIGN KEY (products_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE product_vendors ADD CONSTRAINT FK_9EFCA1A12E8B9E4D FOREIGN KEY (vendors_id) REFERENCES vendors (id)');
        $this->addSql('ALTER TABLE products DROP FOREIGN KEY FK_B3BA5A5AF603EE73');
        $this->addSql('DROP INDEX IDX_B3BA5A5AF603EE73 ON products');
        $this->addSql('ALTER TABLE products DROP vendor_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE product_vendors');
        $this->addSql('ALTER TABLE products ADD vendor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE products ADD CONSTRAINT FK_B3BA5A5AF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendors (id)');
        $this->addSql('CREATE INDEX IDX_B3BA5A5AF603EE73 ON products (vendor_id)');
    }
}
