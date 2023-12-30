<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231219094850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Outbox (
                            id UUID NOT NULL, 
                            event_name VARCHAR(255) NOT NULL, 
                            event_data JSONB NOT NULL, 
                            is_send BOOLEAN DEFAULT false NOT NULL, 
                            created_at timestamp NOT NULL, 
                            PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Outbox');
    }
}
