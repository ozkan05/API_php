<?php
namespace App\Repository;

use App\Entity\Artist;
use PDO;

class ArtistRepository extends BaseRepository
{
    protected string  $table = 'artists';
    protected string  $primaryKey = 'idArtist';
    protected array   $columns = ['Name', 'Annee', 'Description'];
    protected ?string $entityClass = Artist::class;

    // Méthode spécifique 1 : artistes d'une année donnée
    public function findByYear(int $annee): array
    {
        $sth = $this->db->prepare("SELECT * FROM `artists` WHERE Annee = :annee");
        $sth->execute(['annee' => $annee]);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }

    // Méthode spécifique 2 : recherche par nom (partielle)
    public function findByName(string $name): array
    {
        $sth = $this->db->prepare("SELECT * FROM `artists` WHERE Name LIKE :name");
        $sth->execute(['name' => '%' . $name . '%']);
        return array_map([$this, 'hydrate'], $sth->fetchAll(PDO::FETCH_ASSOC));
    }
}