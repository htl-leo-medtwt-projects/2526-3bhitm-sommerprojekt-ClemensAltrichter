<?php
require_once 'dbConnection.php';

if(!isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;
}


if(isset($_GET['listID']) && ! empty($_GET['listID'])){
    $listID = $_GET['listID'];

    $query = "SELECT * FROM list WHERE listID = '$listID'";

    if($conn -> query($query)){
        $result = $conn -> query($query);
        $data = $result -> fetch_assoc();

        $listName = $data['name'];

        $query = "SELECT * FROM movie where movieID IN (SELECT movieID FROM listmovie WHERE listID = '$listID')";
        if($conn -> query($query)){
            $result = $conn -> query($query);
            $movies = $result -> fetch_all(MYSQLI_ASSOC);

            //displayAllMovies();
        }


    if(isset($_GET['delete']) && $_GET['delete'] == "true"){
        
            $query = "DELETE FROM list WHERE listID = '$listID'";

            if($conn -> query($query)){
                header("Location: ../pages/lists.html");
            }else{
                echo "Error deleting List";
            }
    
    }






    }else{

    }
    }

    function displayAllMovies(){
        global $movies;

        for($i = 0; $i < count($movies); $i++){
            echo "<div class='movieBox'>";
            echo "<img src='" . $movies[$i]['poster'] . "' alt='Poster'>";
            echo "<h2>" . $movies[$i]['title'] . "</h2>";
           // echo "<p>" . $movies[$i]['overview'] . "</p>";
            echo "</div>";
        }

    }



?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>cineMatch</title>

    <link rel="stylesheet" href="../style/global.css">
    <link rel="stylesheet" href="../style/listDetail.css">

</head>
<body>
    

<a href="../pages/lists.html">
     <div id="header">
        <h1><?php  echo $listName;?></h1>
    </div>
</a>
    <div class="hl" ></div>

    <div id="confBox">

    <a href="listDetail.php?listID=<?php echo $listID;?>&delete=true">
        <div id="deleteBTN">
            Delete List
        </div>
    </a>
    </div>

    <div class="hl " ></div>

    <div id="contentBox">

    <?php displayAllMovies();?>

        </div>
    

</body>
</html>