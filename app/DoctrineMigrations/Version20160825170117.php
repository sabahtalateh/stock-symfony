<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160825170117 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE portfolio_quote (id INT AUTO_INCREMENT NOT NULL, portfolio_id INT DEFAULT NULL, quote_id INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_3629E1B1B96B5643 (portfolio_id), INDEX IDX_3629E1B1DB805178 (quote_id), UNIQUE INDEX u_portfolio_quote (portfolio_id, quote_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE portfolio_quote ADD CONSTRAINT FK_3629E1B1B96B5643 FOREIGN KEY (portfolio_id) REFERENCES portfolio (id)');
        $this->addSql('ALTER TABLE portfolio_quote ADD CONSTRAINT FK_3629E1B1DB805178 FOREIGN KEY (quote_id) REFERENCES quote (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE portfolio_quote');
    }
}
