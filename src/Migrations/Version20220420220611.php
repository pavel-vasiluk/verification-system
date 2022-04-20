<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220420220611 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification ADD confirmation_attempts INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification DROP confirmation_attempts');
    }
}
