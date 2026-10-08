<?php
namespace App\Repository;

use App\Entity\Rating;
use PDO;

class RatingRepository extends BaseRepository
{
    protected string  $table = 'ratings';
    protected string  $primaryKey = 'idRating';
    protected array   $columns = ['idArtist', 'Note', 'Comment'];
    protected ?string $entityClass = Rating::class;

    // Exemple de méthode spécifique : toutes les notes d'un artiste
    public function findByArtist(int $idArtist): array
    {
        $sth = $this->db->prepare("SELECT * FROM `ratings` WHERE idArtist = :idArtist");
        $sth->execute(['idArtist' => $idArtist]);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }
}