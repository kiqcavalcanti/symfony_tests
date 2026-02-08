<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260205145355 extends AbstractMigration
{
  public function getDescription(): string
  {
    return 'Migração de criação de usuário';
  }

  public function up(Schema $schema): void
  {
    $this->addSql('
        CREATE TABLE "users" (
            id BLOB NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(180) NOT NULL,
            password VARCHAR(255) NOT NULL,
            PRIMARY KEY (id)
        )
    ');

    $this->addSql('
        CREATE UNIQUE INDEX UNIQ_user_email ON "users" (email)
    ');
  }

  public function down(Schema $schema): void
  {
    $this->addSql('DROP TABLE "users"');

  }
}
