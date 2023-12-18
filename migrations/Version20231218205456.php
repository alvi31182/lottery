<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218205456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE Outbox (
                            id UUID NOT NULL, 
                            eventName VARCHAR(255) NOT NULL, 
                            eventData JSONB NOT NULL, 
                            is_send BOOLEAN DEFAULT false NOT NULL, 
                            createdAt TIMESTAMP(0) WITH TIME ZONE NOT NULL, 
                            PRIMARY KEY(id))'
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE Outbox');
    }
}
