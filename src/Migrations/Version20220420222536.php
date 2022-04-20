<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220420222536 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification ADD is_expired TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE verification DROP is_expired');
    }
}
