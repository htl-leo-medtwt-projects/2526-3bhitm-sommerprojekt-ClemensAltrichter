<?php
require_once 'dbConnection.php';


$answer = array(
    "code" => 404,
    "data" => []
);


if (isset($_GET['search'])) {
    $search = '%' . $_GET['search'] . '%';
    $stmt = $conn->prepare("
        SELECT * FROM movie 
        WHERE title LIKE ? 
        ORDER BY voteAVG DESC
        LIMIT 20
    ");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
    $answer['data'] = $result->fetch_all(MYSQLI_ASSOC);
    $answer['code'] = 200;
    echo json_encode($answer);
    exit;
}

$query = "SELECT * FROM movie";

if($conn -> query($query)){
    $result = $conn -> query($query);
    $watchpartys = $result -> fetch_all(MYSQLI_ASSOC);

    $answer['code'] = 200;
    $answer['data'] = $watchpartys;


}

echo json_encode($answer);


?>