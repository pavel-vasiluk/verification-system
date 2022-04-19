<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220419141058 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE verification (
                id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', 
                subject JSON NOT NULL, 
                confirmed TINYINT(1) NOT NULL, 
                code VARCHAR(8) NOT NULL, 
                user_info JSON NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE verification');
    }
}
