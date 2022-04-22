<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220422114948 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE notification (
                id CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', 
                recipient VARCHAR(255) NOT NULL,
                channel ENUM(\'sms\', \'email\') NOT NULL, 
                body VARCHAR(500) NOT NULL, 
                dispatched TINYINT(1) NOT NULL, 
                created_at DATETIME NOT NULL, 
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification');
    }
}
