<?php

require "dbconnection.php";
$dbcon = createDbConnection();

$body = file_get_contents("php://input");
$data = json_decode($body);

$artist_id = filter_var($data->id, FILTER_SANITIZE_NUMBER_INT);

try{
    $dbcon->beginTransaction();

    $stmt = $dbcon->prepare("DELETE FROM invoice_items 
    WHERE TrackId IN (SELECT TrackId FROM tracks 
    WHERE AlbumId IN(SELECT AlbumId FROM albums 
    WHERE ArtistId = ?))");
    $stmt->execute(array($artist_id));

    $stmt = $dbcon->prepare("DELETE FROM playlist_track 
    WHERE TrackId IN (SELECT TrackId FROM tracks 
    WHERE AlbumId IN(SELECT AlbumId FROM albums 
    WHERE ArtistId = ?))");
    $stmt->execute(array($artist_id));

    $stmt = $dbcon->prepare("DELETE FROM tracks
    WHERE AlbumId IN (SELECT AlbumId FROM albums
    WHERE ArtistId = ?)");
    $stmt->execute(array($artist_id));

    $stmt = $dbcon->prepare("DELETE FROM albums
    WHERE ArtistId = ?");
    $stmt->execute(array($artist_id));

    $stmt = $dbcon->prepare("DELETE FROM artists
    WHERE ArtistId = ?");
    $stmt->execute(array($artist_id));

    $dbcon->commit();
} catch(Exception $e){
    $dbcon->rollBack();
    echo "Failed:".$e->getMessage();
}