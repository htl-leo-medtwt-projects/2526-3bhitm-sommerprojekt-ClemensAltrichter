<?php

require_once 'dbConnection.php';

if(!isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;
}



$answer = array(
    "code" => 404,
    "data" => []
);
$data = [];
$code;

$query = "SELECT * FROM list where userID = " . $_SESSION['userID']. ";";

if(isset($_GET['addMovie'])){
    $movieID = $_GET['addMovie'];
    $listID = $_SESSION['userID'];

    $query = "INSERT INTO list_movie (listID, movieID) VALUES ($listID, $movieID);";
}


if($conn -> query($query)){
    $result = $conn -> query($query);
    $code = 200;
    $data = $result -> fetch_all(MYSQLI_ASSOC); 
}else{
    $code = 405;
    
}

$answer["code"] = $code;
$answer["data"] = $data;

echo json_encode($answer);

$conn -> close();





?>