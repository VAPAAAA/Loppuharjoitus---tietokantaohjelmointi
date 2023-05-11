<?php

require "dbconnection.php";
$dbcon = createDbConnection();

$artist_id = $_GET["id"];

$sql = "SELECT artists.Name AS artist, albums.Title AS album, tracks.Name AS track
        FROM artists
        JOIN albums ON artists.ArtistId = albums.ArtistId
        JOIN tracks ON albums.AlbumId = tracks.AlbumId
        WHERE artists.ArtistId = :id";

$statement = $dbcon->prepare($sql);
$statement->bindParam(':id', $artist_id);
$statement->execute();

$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

$data = array(
    'artist' => '',
    'albums' => array()
);

foreach ($rows as $row) {
    $album = $row['album'];
    $track = $row['track'];

    if ($data['artist'] == '') {
        $data['artist'] = $row['artist'];
    }

    $album_key = array_search($album, array_column($data['albums'], 'title'));
    if ($album_key === false) {
        $data['albums'][] = array(
            'title' => $album,
            'tracks' => array($track)
        );
    } else {
        $data['albums'][$album_key]['tracks'][] = $track;
    }
}

$json = json_encode($data);

header('Content-type: application/json');

echo $json;
