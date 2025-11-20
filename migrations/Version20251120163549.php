<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251120163549 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE education_i18n ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE project DROP short_description');
        $this->addSql('ALTER TABLE "user" ADD datetime_immutable TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('ALTER TABLE "user" ADD datetime TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".datetime_immutable IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE project ADD short_description TEXT NOT NULL');
        $this->addSql('ALTER TABLE "user" DROP datetime_immutable');
        $this->addSql('ALTER TABLE "user" DROP datetime');
        $this->addSql('ALTER TABLE education_i18n DROP description');
    }
}
