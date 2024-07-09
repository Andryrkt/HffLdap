<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240709042841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE statut_demande (id INT IDENTITY NOT NULL, code_application NVARCHAR(3), code_statut NVARCHAR(3) NOT NULL, date_creation DATETIME2(6) NOT NULL, date_modification DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'statut_demande\', N\'COLUMN\', \'date_creation\'');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'statut_demande\', N\'COLUMN\', \'date_modification\'');
        $this->addSql('DROP TABLE sous_type_document');
        $this->addSql('DROP TABLE idemnity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('CREATE TABLE sous_type_document (id INT IDENTITY NOT NULL, code_document NVARCHAR(3) COLLATE French_CI_AS NOT NULL, code_sous_type NVARCHAR(50) COLLATE French_CI_AS NOT NULL, created_at DATETIME2(6) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:datetime_immutable)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'sous_type_document\', N\'COLUMN\', \'created_at\'');
        $this->addSql('CREATE TABLE idemnity (id INT IDENTITY NOT NULL, categorie NVARCHAR(255) COLLATE French_CI_AS NOT NULL, destination NVARCHAR(255) COLLATE French_CI_AS NOT NULL, rmq NVARCHAR(10) COLLATE French_CI_AS NOT NULL, montant_idemnitie INT, PRIMARY KEY (id))');
        $this->addSql('DROP TABLE statut_demande');
    }
}
