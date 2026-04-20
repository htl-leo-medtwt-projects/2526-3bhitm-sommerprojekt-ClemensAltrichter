<?php
require_once 'dbConnection.php';

if(isset($_GET['watchpartyID']) && ! empty($_GET['watchpartyID'])){
    $watchpartyID = $_GET['watchpartyID'];

    $query = "SELECT *,
 (SELECT poster from movie where movieID = chosenMovieID) poster,
 (SELECT title from movie where movieID = chosenMovieID) title
  FROM watchparty
  where watchpartyID = '$watchpartyID'";

    if($conn -> query($query)){
        $result = $conn -> query($query);
        $data = $result -> fetch_assoc();

        $watchpartyName = $data['name'];
        $chosenMovieID = $data['chosenMovieID'];
    }
//alle user die in der party waren aus db holen 
 $query = "SELECT * FROM user where userID IN (SELECT userID FROM partymember WHERE watchpartyID = '$watchpartyID')";
        if($conn -> query($query)){
            $result = $conn -> query($query);
            $users = $result -> fetch_all(MYSQLI_ASSOC);

            
        }



    if(isset($_GET['delete']) && $_GET['delete'] == "true"){
        
            $query = "DELETE FROM watchparty WHERE watchpartyID = '$watchpartyID'";

            if($conn -> query($query)){
                header("Location: ../pages/watchpartys.html");
            }else{
                echo "Error deleting List";
            }
    
    }

}else{
    header("Location: ../pages/watchpartys.html");
}


function displayAllUsers(){
    global $users;

    for($i = 0; $i < count($users); $i++){
        echo "<div class='userBox'>";
        echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='Poster'>";
        echo "<h2>" . $users[$i]['username'] . "</h2>";
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
    <link rel="stylesheet" href="../style/watchpartyDetail.css">
</head>
<body>

   

<a href="../pages/watchpartys.html">
     <div id="header">
        <h1><?php  echo $watchpartyName;?></h1>
    </div>
</a>
    <div class="hl" ></div>

    <div id="confBox">
<a href="watchpartyDetail.php?watchpartyID=<?php echo $watchpartyID;?>&delete=true">
        <div id="deleteBTN">
            Delete List
        </div>
    </a>
    </div>
    <div class="hl" ></div>

    <div class="subHeader">
        the final Movie was:
    </div>

    <div id="contentBox">
        <div id="chosenMovieBox">
            <img src="<?php echo $data['poster'];?>" alt="Poster">
            <h2><?php echo $data['title'];?></h2>
        </div>

        <div class="subHeader">
        everybody who whatched:
        </div>

        <div id="userContainer">
            <?php displayAllUsers();?>
        </div>


    </div>
    
</body>
</html>