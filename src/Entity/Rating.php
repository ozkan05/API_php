<?php
namespace App\Entity;

class Rating implements \JsonSerializable
{
    public function __construct(
        private ?int $idRating = null,
        private ?int $idArtist = null,
        private ?int $note = null,
        private ?string $comment = null,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idRating']) ? (int) $row['idRating'] : null,
            isset($row['idArtist']) ? (int) $row['idArtist'] : null,
            isset($row['Note']) ? (int) $row['Note'] : null,
            $row['Comment'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'idRating' => $this->idRating,
            'idArtist' => $this->idArtist,
            'Note'     => $this->note,
            'Comment'  => $this->comment,
        ];
    }

    public function jsonSerialize(): array { return $this->toArray(); }

    public function getId(): ?int { return $this->idRating; }
}