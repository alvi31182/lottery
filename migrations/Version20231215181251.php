<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231215181251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE lottery (
                            id UUID NOT NULL, 
                            player_id UUID NOT NULL, 
                            game_id UUID NOT NULL, 
                            status VARCHAR(255) NOT NULL,
                            stake NUMERIC(10, 2) NOT NULL,
                            created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, 
                            updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            deleted_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX idx_lottery_player_game ON lottery (player_id, game_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE lottery');
    }
}
