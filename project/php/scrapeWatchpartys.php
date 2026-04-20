<?php
require_once 'dbConnection.php';

$answer = array(
    "code" => 404,
    "data" => []
);

$query = "SELECT *,
 (SELECT poster from movie where movieID = chosenMovieID) poster,
 (SELECT title from movie where movieID = chosenMovieID) title
  FROM watchparty";

if($conn -> query($query)){
    $result = $conn -> query($query);
    $watchpartys = $result -> fetch_all(MYSQLI_ASSOC);

    $answer['code'] = 200;
    $answer['data'] = $watchpartys;

}

echo json_encode($answer);



?>