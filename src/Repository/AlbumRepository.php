<?php
namespace App\Repository;

class AlbumRepository extends BaseRepository
{
    protected string $table = 'albums';
    protected string $primaryKey = 'idAlbum';
    protected array  $columns = ['Title', 'Year', 'idArtist'];
}