<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241205092938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE breed ADD animal_id INT NOT NULL');
        $this->addSql('ALTER TABLE breed ADD CONSTRAINT FK_F8AF884F8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_F8AF884F8E962C16 ON breed (animal_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE breed DROP FOREIGN KEY FK_F8AF884F8E962C16');
        $this->addSql('DROP INDEX IDX_F8AF884F8E962C16 ON breed');
        $this->addSql('ALTER TABLE breed DROP animal_id');
    }
}
