<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160823193600 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE quote_char (id INT AUTO_INCREMENT NOT NULL, quote_id INT DEFAULT NULL, last_trade_price_only NUMERIC(10, 2) DEFAULT NULL, date DATE NOT NULL, change_price NUMERIC(10, 2) DEFAULT NULL, open_price NUMERIC(10, 2) NOT NULL, close_price NUMERIC(10, 2) DEFAULT NULL, days_high NUMERIC(10, 2) NOT NULL, days_low NUMERIC(10, 2) NOT NULL, volume INT NOT NULL, adj_close NUMERIC(10, 2) DEFAULT NULL, INDEX IDX_1E190815DB805178 (quote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quote_char ADD CONSTRAINT FK_1E190815DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE quote_char');
    }
}
