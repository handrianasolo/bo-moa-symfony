<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211119175027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticketsnoincident (id INT AUTO_INCREMENT NOT NULL, nomMagasin VARCHAR(100) DEFAULT NULL, codeMagasin VARCHAR(20) DEFAULT NULL, ville VARCHAR(100) DEFAULT NULL, codeGeode VARCHAR(20) DEFAULT NULL, nKit VARCHAR(20) DEFAULT NULL, dateInstall DATETIME DEFAULT NULL, commentaire LONGTEXT DEFAULT NULL, arsRecup VARCHAR(50) DEFAULT NULL, etat VARCHAR(255) DEFAULT \'\'\'traité\'\'\' NOT NULL, dateArchive DATETIME DEFAULT NULL, dateMaj DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE users');
        $this->addSql('ALTER TABLE ticketsibm CHANGE dateMaj dateMaj DATETIME DEFAULT NULL, CHANGE etatTicket etatTicket VARCHAR(255) DEFAULT \'\'\'NON_RESOLU\'\'\' NOT NULL');
        $this->addSql('ALTER TABLE ticketsreseau CHANGE etatTicket etatTicket VARCHAR(255) DEFAULT \'\'\'Ticket_ouvert\'\'\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users (u_user VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, lname_user VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, fname_user VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, type_user VARCHAR(255) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, user_name VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, password VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, mail_user VARCHAR(50) CHARACTER SET latin1 NOT NULL COLLATE `latin1_swedish_ci`, PRIMARY KEY(u_user)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE ticketsnoincident');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE ticketsibm CHANGE dateMaj dateMaj DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE etatTicket etatTicket VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NON_RESOLU\' NOT NULL COLLATE `utf8mb4_general_ci`');
        $this->addSql('ALTER TABLE ticketsreseau CHANGE etatTicket etatTicket VARCHAR(255) CHARACTER SET latin1 DEFAULT \'Ticket_ouvert\' COLLATE `latin1_swedish_ci`');
    }
}
