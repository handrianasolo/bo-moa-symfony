<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211122101602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticketsibm CHANGE dateAffectation dateAffectation VARCHAR(255) NOT NULL, CHANGE etatTicket etatTicket VARCHAR(255) DEFAULT \'\'\'NON_RESOLU\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE ticketsreseau CHANGE etatTicket etatTicket VARCHAR(255) DEFAULT \'\'\'Ticket_ouvert\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, CHANGE email email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticketsibm CHANGE dateAffectation dateAffectation DATETIME NOT NULL, CHANGE etatTicket etatTicket VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NON_RESOLU\' NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE ticketsreseau CHANGE etatTicket etatTicket VARCHAR(255) CHARACTER SET latin1 DEFAULT \'Ticket_ouvert\' COLLATE `latin1_swedish_ci`');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user DROP roles, DROP password, CHANGE email email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
