<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230508183815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create participants table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE participants (
                id UUID NOT NULL,
                group_id UUID NOT NULL,
                name VARCHAR NOT NULL,
                created_at TIMESTAMP NOT NULL,
                PRIMARY KEY(id),
                CONSTRAINT fk_participant_groups
                    FOREIGN KEY (group_id) REFERENCES participant_groups(id)
                        ON DELETE CASCADE
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE participants');
    }
}
