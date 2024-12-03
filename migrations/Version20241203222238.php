<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203222238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal ADD pictures_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FBC415685 FOREIGN KEY (pictures_id) REFERENCES photo (id)');
        $this->addSql('CREATE INDEX IDX_6AAB231FBC415685 ON animal (pictures_id)');
        $this->addSql('ALTER TABLE breed ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE breed ADD CONSTRAINT FK_F8AF884FC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('CREATE INDEX IDX_F8AF884FC54C8C93 ON breed (type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FBC415685');
        $this->addSql('DROP INDEX IDX_6AAB231FBC415685 ON animal');
        $this->addSql('ALTER TABLE animal DROP pictures_id');
        $this->addSql('ALTER TABLE breed DROP FOREIGN KEY FK_F8AF884FC54C8C93');
        $this->addSql('DROP INDEX IDX_F8AF884FC54C8C93 ON breed');
        $this->addSql('ALTER TABLE breed DROP type_id');
    }
}
