<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127190515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE access_token (id SERIAL NOT NULL, user_id INT NOT NULL, token VARCHAR(128) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B6A2DD685F37A13B ON access_token (token)');
        $this->addSql('CREATE INDEX IDX_B6A2DD68A76ED395 ON access_token (user_id)');
        $this->addSql('COMMENT ON COLUMN access_token.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE access_token ADD CONSTRAINT FK_B6A2DD68A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD role JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE access_token DROP CONSTRAINT FK_B6A2DD68A76ED395');
        $this->addSql('DROP TABLE access_token');
        $this->addSql('ALTER TABLE "user" DROP role');
    }
}
