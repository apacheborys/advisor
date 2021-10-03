<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20211002133951 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE `advisor` (
  `id` CHAR(36) NOT NULL,
  `name` VARCHAR(80) NOT NULL,
  `description` TEXT NULL,
  `availability` TINYINT NULL,
  `price_per_minute_amount` VARCHAR(12) NOT NULL,
  `price_per_minute_currency_code` CHAR(3) NOT NULL,
  PRIMARY KEY (`id`))");

        $this->addSql("CREATE TABLE `advisor_languages` (
  `advisor_id` CHAR(36) NOT NULL,
  `locale` VARCHAR(35) NOT NULL,
  PRIMARY KEY (`advisor_id`, `locale`))");

        $this->addSql("ALTER TABLE `advisor_languages` 
ADD INDEX `advisor_FK_idx` (`advisor_id` ASC) VISIBLE");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `advisor_languages` DROP INDEX `advisor_FK_idx`");
        $this->addSql("DROP TABLE `advisor_languages`");
        $this->addSql("DROP TABLE `advisor`");
    }
}
