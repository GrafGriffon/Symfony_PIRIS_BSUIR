<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230302214353 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE type_deposit_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE currency (id INT NOT NULL, index INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE type_deposit (id INT NOT NULL, name VARCHAR(255) NOT NULL, percent INT NOT NULL, is_returnable BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE account ADD currency INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD type_deposit INT DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD number_declaration VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD start_date_deposit DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD end_date_deposit DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD is_main_account BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A46956883F FOREIGN KEY (currency) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE account ADD CONSTRAINT FK_7D3656A4BDFAF2CC FOREIGN KEY (type_deposit) REFERENCES type_deposit (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7D3656A46956883F ON account (currency)');
        $this->addSql('CREATE INDEX IDX_7D3656A4BDFAF2CC ON account (type_deposit)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A46956883F');
        $this->addSql('ALTER TABLE account DROP CONSTRAINT FK_7D3656A4BDFAF2CC');
        $this->addSql('DROP SEQUENCE currency_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE type_deposit_id_seq CASCADE');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE type_deposit');
        $this->addSql('DROP INDEX IDX_7D3656A46956883F');
        $this->addSql('DROP INDEX IDX_7D3656A4BDFAF2CC');
        $this->addSql('ALTER TABLE account DROP currency');
        $this->addSql('ALTER TABLE account DROP type_deposit');
        $this->addSql('ALTER TABLE account DROP number_declaration');
        $this->addSql('ALTER TABLE account DROP start_date_deposit');
        $this->addSql('ALTER TABLE account DROP end_date_deposit');
        $this->addSql('ALTER TABLE account DROP is_main_account');
    }
}
