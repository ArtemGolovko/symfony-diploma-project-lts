<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230728140024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168F675F31B');
        $this->addSql('ALTER TABLE user RENAME users');
        $this->addSql(
            'CREATE TABLE modules (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, name VARCHAR(255) NOT NULL, template LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2EB743D7F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE modules ADD CONSTRAINT FK_2EB743D7F675F31B FOREIGN KEY (author_id) REFERENCES users (id)'
        );
        $this->addSql('ALTER TABLE articles DROP description');
        $this->addSql(
            'ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F675F31B FOREIGN KEY (author_id) REFERENCES users (id)'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.'
        );

        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168F675F31B');
        $this->addSql('ALTER TABLE modules DROP FOREIGN KEY FK_2EB743D7F675F31B');
        $this->addSql('ALTER TABLE users RENAME user');
        $this->addSql('DROP TABLE modules');
        $this->addSql(
            'ALTER TABLE articles ADD description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`'
        );
        $this->addSql(
            'ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168F675F31B FOREIGN KEY (author_id) REFERENCES user (id)'
        );
    }
}
