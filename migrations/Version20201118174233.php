<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118174233 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fmessage ADD CONSTRAINT FK_6EBCC29E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE fmessage ADD CONSTRAINT FK_6EBCC29E1F55203D FOREIGN KEY (topic_id) REFERENCES FTopic (id)');
        $this->addSql('CREATE INDEX IDX_6EBCC29E7E3C61F9 ON fmessage (owner_id)');
        $this->addSql('CREATE INDEX IDX_6EBCC29E1F55203D ON fmessage (topic_id)');
        $this->addSql('ALTER TABLE ftopic ADD CONSTRAINT FK_F2604ED07E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE ncomments ADD CONSTRAINT FK_3393E72E7E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE ncomments ADD CONSTRAINT FK_3393E72EB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)');
        $this->addSql('ALTER TABLE notification ADD readed TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_A765AD327E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE tmessage ADD CONSTRAINT FK_5022F4C87E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE tmessage ADD CONSTRAINT FK_5022F4C8700047D2 FOREIGN KEY (ticket_id) REFERENCES Ticket (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_900CA8957E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_900CA89512469DE2 FOREIGN KEY (category_id) REFERENCES TCategory (id)');
        $this->addSql('ALTER TABLE users CHANGE date date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE news ADD CONSTRAINT FK_1DD399507E3C61F9 FOREIGN KEY (owner_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE FMessage DROP FOREIGN KEY FK_6EBCC29E7E3C61F9');
        $this->addSql('ALTER TABLE FMessage DROP FOREIGN KEY FK_6EBCC29E1F55203D');
        $this->addSql('DROP INDEX IDX_6EBCC29E7E3C61F9 ON FMessage');
        $this->addSql('DROP INDEX IDX_6EBCC29E1F55203D ON FMessage');
        $this->addSql('ALTER TABLE FTopic DROP FOREIGN KEY FK_F2604ED07E3C61F9');
        $this->addSql('ALTER TABLE NComments DROP FOREIGN KEY FK_3393E72E7E3C61F9');
        $this->addSql('ALTER TABLE NComments DROP FOREIGN KEY FK_3393E72EB5A459A0');
        $this->addSql('ALTER TABLE news DROP FOREIGN KEY FK_1DD399507E3C61F9');
        $this->addSql('ALTER TABLE Notification DROP FOREIGN KEY FK_A765AD327E3C61F9');
        $this->addSql('ALTER TABLE Notification DROP readed');
        $this->addSql('ALTER TABLE Ticket DROP FOREIGN KEY FK_900CA8957E3C61F9');
        $this->addSql('ALTER TABLE Ticket DROP FOREIGN KEY FK_900CA89512469DE2');
        $this->addSql('ALTER TABLE TMessage DROP FOREIGN KEY FK_5022F4C87E3C61F9');
        $this->addSql('ALTER TABLE TMessage DROP FOREIGN KEY FK_5022F4C8700047D2');
        $this->addSql('ALTER TABLE Users CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
