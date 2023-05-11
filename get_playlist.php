<?php

require "dbconnection.php";
$dbcon = createDbConnection();

$playlist_id = $_GET["id"];

$sql = "SELECT tracks.Name as N, tracks.Composer as C
    FROM playlists, playlist_track, tracks 
    WHERE playlists.PlaylistId = ? 
    AND playlists.PlaylistId = playlist_track.PlaylistId 
    AND playlist_track.TrackId = tracks.TrackId";

$statement = $dbcon->prepare($sql);
$statement->execute(array($playlist_id));

$rows = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($rows as $row){
    $name = htmlspecialchars($row["N"]);
    $composer = htmlspecialchars($row["C"]);
    echo "<h3>".$name."</h3>"."<p>".$composer."</p>"."</br>";
}

?>