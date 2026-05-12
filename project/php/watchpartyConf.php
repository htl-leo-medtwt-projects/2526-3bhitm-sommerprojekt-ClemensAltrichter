<?php
require_once "./dbConnection.php";

echo $_SESSION['userID'];

if(!isset($_SESSION['userID'])){
    header("Location: ../userSys/index.html");
    exit;
}

$allUsedLists = [];


 $query = "SELECT * FROM user where userID IN 
 (SELECT DISTINCT userID1 from friend where userID2 = 1
 UNION ALL
  SELECT DISTINCT userID2 from friend where userID1 =1)"; // hier muss die userID der eingeloggten Person rein

        if($conn -> query($query)){
            $result = $conn -> query($query);
            $users = $result -> fetch_all(MYSQLI_ASSOC);
        }


        


$query = "SELECT * FROM list where userID = 1"; // hier muss die userID der eingeloggten Person rein

        if($conn -> query($query)){
            $result = $conn -> query($query);
            $lists = $result -> fetch_all(MYSQLI_ASSOC);
        }



        if(isset($_GET['listID']) && ! empty($_GET['listID'])){
            $listID = $_GET['listID'];

            $allUsedLists[count($allUsedLists)] = $listID;
           
        }
   

if(isset($_GET['getLists']) && $_GET['getLists'] == "true"){
            echo json_encode($lists);
        }

        if(isset($_GET['getFriends']) && $_GET['getFriends'] == "true"){
            echo json_encode($users);
        }




        if(isset($_POST['submit'])){

        }






function display4Users(){
   // $friends = getUsers();
   global $users;

   if(count($users) < 1){
    echo "<p id='noFriends'>No friends to invite yet</p>";
   }


    for($i = 0; $i < min(4,count($users)); $i++){
        echo "<div class='userBox'>";
        echo "<img class='avatar' src='../resource/img/" . $users[$i]['avatar'] . "' alt='Poster'>";
        echo "<h2>" . $users[$i]['username'] . "</h2>";
        echo "<div class='inviteBox'></div>";
        echo "</div>";
    }

}

function displayAllLists(){
    global $lists;

    for($i = 0; $i < count($lists); $i++){
        echo "<div class='listBox'>";
        echo "<h2>" . $lists[$i]['name'] . "</h2>";
        echo "<div class='listCheckBox'  onclick='addListToWatchparty( ". $lists[$i]['listID'] .", this )'></div>";
        echo "</div>";
    }

    
}



?>

