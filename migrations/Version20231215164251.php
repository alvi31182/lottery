<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20231215164251 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Lottery award table for use with main table lottery';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE lottery_award (
                            id UUID NOT NULL, 
                            lottery_id UUID NOT NULL, 
                            status VARCHAR(255) NOT NULL, 
                            win_sum NUMERIC(10, 2) NOT NULL,
                            created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, 
                            updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            deleted_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            PRIMARY KEY(id)
                           )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_407276EBCFAA77DD ON lottery_award (lottery_id)');
        $this->addSql('CREATE INDEX btree_lottery_award_idx ON lottery_award (id)');
        $this->addSql('DROP INDEX idx_lottery_player_game');
        $this->addSql('CREATE INDEX btree_lottery_idx ON lottery (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE lottery_award');
        $this->addSql('DROP INDEX btree_lottery_idx');
        $this->addSql('CREATE INDEX idx_lottery_player_game ON lottery (player_id, game_id)');
    }
}
