<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200530121210 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE snippet_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE snippet (id INT NOT NULL, hash VARCHAR(255) NOT NULL, code TEXT NOT NULL, cyclomatic_complexity INT NOT NULL, cognitive_complexity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_961C8CD5D1B862B8 ON snippet (hash)');
        $this->addSql('ALTER TABLE diff ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN diff.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE diff ADD snippet_left_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE diff ADD snippet_right_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE diff DROP code_snippet_left');
        $this->addSql('ALTER TABLE diff DROP code_snippet_right');
        $this->addSql('ALTER TABLE diff ADD CONSTRAINT FK_457047ABBD3DC7D1 FOREIGN KEY (snippet_left_id) REFERENCES snippet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE diff ADD CONSTRAINT FK_457047AB4B12BA52 FOREIGN KEY (snippet_right_id) REFERENCES snippet (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_457047ABBD3DC7D1 ON diff (snippet_left_id)');
        $this->addSql('CREATE INDEX IDX_457047AB4B12BA52 ON diff (snippet_right_id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE diff DROP CONSTRAINT FK_457047ABBD3DC7D1');
        $this->addSql('ALTER TABLE diff DROP CONSTRAINT FK_457047AB4B12BA52');
        $this->addSql('DROP SEQUENCE snippet_id_seq CASCADE');
        $this->addSql('DROP TABLE snippet');
        $this->addSql('DROP INDEX IDX_457047ABBD3DC7D1');
        $this->addSql('DROP INDEX IDX_457047AB4B12BA52');
        $this->addSql('ALTER TABLE diff DROP created_at');
        $this->addSql('ALTER TABLE diff ADD code_snippet_left TEXT NOT NULL');
        $this->addSql('ALTER TABLE diff ADD code_snippet_right TEXT NOT NULL');
        $this->addSql('ALTER TABLE diff DROP snippet_left_id');
        $this->addSql('ALTER TABLE diff DROP snippet_right_id');
    }
}
