<?php

require_once 'dbConnection.php';

if(isset($_GET['action']) && ! empty($_GET['action'])){

}else{
    if(isset($_POST['listName']) && ! empty($_POST['listName'])){
        $listName = $_POST['listName'];
        $userID = 1;
      //  $userID = $_SESSION['userID']; // KI

        $query = "INSERT INTO list (name, userID) VALUES ('$listName', '$userID')";

        if($conn -> query($query)){
            header("Location: ../pages/lists.html");
        }else{
           
            header("Location: ../pages/lists.html");
             echo "Error creating List";
            //TODO: Fehlermeldung
        }
    }

    $conn -> close();


}



?>