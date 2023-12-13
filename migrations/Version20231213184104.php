<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231213184104 extends AbstractMigration
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
                            created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, 
                            updated_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            deleted_at TIMESTAMP(0) WITH TIME ZONE DEFAULT NULL, 
                            PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX btree_lottery_idx ON lottery (id)');
        $this->addSql('COMMENT ON COLUMN lottery.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lottery.player_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lottery.game_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN lottery.created_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN lottery.updated_at IS \'(DC2Type:datetimetz_immutable)\'');
        $this->addSql('COMMENT ON COLUMN lottery.deleted_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE lottery');
    }
}
