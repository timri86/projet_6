<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200403130904 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, source VARCHAR(255) NOT NULL, alternatif VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_video (tricks_id INT NOT NULL, video_id INT NOT NULL, INDEX IDX_A5F7E4453B153154 (tricks_id), INDEX IDX_A5F7E44529C1004E (video_id), PRIMARY KEY(tricks_id, video_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tricks_image (tricks_id INT NOT NULL, image_id INT NOT NULL, INDEX IDX_1C0D3A363B153154 (tricks_id), INDEX IDX_1C0D3A363DA5256D (image_id), PRIMARY KEY(tricks_id, image_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE video (id INT AUTO_INCREMENT NOT NULL, source VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tricks_video ADD CONSTRAINT FK_A5F7E4453B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tricks_video ADD CONSTRAINT FK_A5F7E44529C1004E FOREIGN KEY (video_id) REFERENCES video (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tricks_image ADD CONSTRAINT FK_1C0D3A363B153154 FOREIGN KEY (tricks_id) REFERENCES tricks (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tricks_image ADD CONSTRAINT FK_1C0D3A363DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE tricks_image DROP FOREIGN KEY FK_1C0D3A363DA5256D');
        $this->addSql('ALTER TABLE tricks_video DROP FOREIGN KEY FK_A5F7E44529C1004E');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE tricks_video');
        $this->addSql('DROP TABLE tricks_image');
        $this->addSql('DROP TABLE video');
    }
}
