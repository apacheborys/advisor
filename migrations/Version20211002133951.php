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
  `price_per_minute_currency` CHAR(3) NOT NULL,
  PRIMARY KEY (`id`))");

        $this->addSql("CREATE TABLE `advisor_languages` (
  `id_advisor` CHAR(36) NOT NULL,
  `locale` VARCHAR(35) NOT NULL,
  PRIMARY KEY (`id_advisor`, `locale`))");

        $this->addSql("ALTER TABLE `advisor_languages` 
ADD INDEX `advisor_FK_idx` (`id_advisor` ASC) VISIBLE");

        $this->addSql("ALTER TABLE `advisor_languages` 
ADD CONSTRAINT `advisor_FK`
  FOREIGN KEY (`id_advisor`)
  REFERENCES `symfony_docker`.`advisor` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("ALTER TABLE `advisor_languages` DROP FOREIGN KEY `advisor_FK`");
        $this->addSql("ALTER TABLE `advisor_languages` DROP INDEX `advisor_FK_idx`");
        $this->addSql("DROP TABLE `advisor_languages`");
        $this->addSql("DROP TABLE `advisor`");
    }
}
