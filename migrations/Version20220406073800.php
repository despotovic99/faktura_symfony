<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220406073800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fakture (id INT AUTO_INCREMENT NOT NULL, organizacija_id INT DEFAULT NULL, broj_racuna VARCHAR(30) NOT NULL, datum_izdavanja DATE NOT NULL, UNIQUE INDEX UNIQ_CEF62FD1DE35B76E (broj_racuna), INDEX IDX_CEF62FD1329EB8 (organizacija_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jedinica_mere (id INT AUTO_INCREMENT NOT NULL, naziv VARCHAR(50) NOT NULL, oznaka VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organizacija (id INT AUTO_INCREMENT NOT NULL, naziv VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE proizvod (id INT AUTO_INCREMENT NOT NULL, jedinica_mere_id INT NOT NULL, naziv_proizvoda VARCHAR(255) NOT NULL, cena_po_jedinici NUMERIC(10, 2) NOT NULL, INDEX IDX_2341A17BFF619037 (jedinica_mere_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stavka_fakture (id INT AUTO_INCREMENT NOT NULL, proizvod_id INT NOT NULL, faktura_id INT NOT NULL, kolicina INT NOT NULL, INDEX IDX_251B3A398822BE0C (proizvod_id), INDEX IDX_251B3A3923AA62EA (faktura_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fakture ADD CONSTRAINT FK_CEF62FD1329EB8 FOREIGN KEY (organizacija_id) REFERENCES organizacija (id)');
        $this->addSql('ALTER TABLE proizvod ADD CONSTRAINT FK_2341A17BFF619037 FOREIGN KEY (jedinica_mere_id) REFERENCES jedinica_mere (id)');
        $this->addSql('ALTER TABLE stavka_fakture ADD CONSTRAINT FK_251B3A398822BE0C FOREIGN KEY (proizvod_id) REFERENCES proizvod (id)');
        $this->addSql('ALTER TABLE stavka_fakture ADD CONSTRAINT FK_251B3A3923AA62EA FOREIGN KEY (faktura_id) REFERENCES fakture (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stavka_fakture DROP FOREIGN KEY FK_251B3A3923AA62EA');
        $this->addSql('ALTER TABLE proizvod DROP FOREIGN KEY FK_2341A17BFF619037');
        $this->addSql('ALTER TABLE fakture DROP FOREIGN KEY FK_CEF62FD1329EB8');
        $this->addSql('ALTER TABLE stavka_fakture DROP FOREIGN KEY FK_251B3A398822BE0C');
        $this->addSql('DROP TABLE fakture');
        $this->addSql('DROP TABLE jedinica_mere');
        $this->addSql('DROP TABLE organizacija');
        $this->addSql('DROP TABLE proizvod');
        $this->addSql('DROP TABLE stavka_fakture');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
