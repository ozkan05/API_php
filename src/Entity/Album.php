<?php
namespace App\Entity;

class Album implements \JsonSerializable
{
    public function __construct(
        private ?int $idAlbum = null,
        private string $title = '',
        private ?int $year = null,
        private ?int $idArtist = null,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idAlbum']) ? (int) $row['idAlbum'] : null,
            $row['Title'] ?? '',
            isset($row['Year']) ? (int) $row['Year'] : null,
            isset($row['idArtist']) ? (int) $row['idArtist'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'idAlbum'  => $this->idAlbum,
            'Title'    => $this->title,
            'Year'     => $this->year,
            'idArtist' => $this->idArtist,
        ];
    }

    public function jsonSerialize(): array { return $this->toArray(); }

    public function getId(): ?int { return $this->idAlbum; }
}