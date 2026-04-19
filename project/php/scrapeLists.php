<?php

require_once 'dbConnection.php';

$answer = array(
    "code" => 404,
    "data" => []
);
$data = [];
$code;

$query = "SELECT * FROM list";


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