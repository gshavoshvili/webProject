<?php 
 session_start();
 if(isset($_GET['match']) && isset($_SESSION['username'])){
    //variables needed for future
    $db = mysqli_connect('localhost', 'root', '', 'registration');
    $match_link = $_GET['match'];
    $username = $_SESSION['username'];
    //checking if sent ID is valid
    $match_link_check_query = "SELECT match_link FROM matches WHERE match_link='$match_link' LIMIT 1";
    $match_result = mysqli_query($db, $match_link_check_query);
    $match_link_array = mysqli_fetch_assoc($match_result);
    
    // checks if $_GET link exists in DB
    if (isset($match_link_array['match_link'])) {
    // Desides who is connected user, creator of the game or his opponent    
        $username_check_query = "SELECT username from users, matches WHERE users.username='$username' AND users.id = matches.id AND matches.match_link = '$match_link' LIMIT 1 ";
        $username_result = mysqli_query($db,$username_check_query);
        $username_array = mysqli_fetch_assoc($username_result);
        if (isset($username_array['username'])) { 
            
        }
        else {

        $opponent_fill_query = "UPDATE matches m SET opponent_id=(SELECT users.id FROM users WHERE users.username = '$username' and users.id = m.id) WHERE m.match_link = '$match_link'";      

        }


    }

    else {header("Location: index.php"); die();}

    
            
 }
 else {header("Location: index.php"); die();}




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
    body{
        background:grey;
    }
    </style>
</head>
<body>
    
<!-- Tabindex to make it focusable -->
<canvas tabindex="1" id="Canvas" width="700" height="600" style="border: 1px solid black"></canvas>

<script src="../game.js">
var username='<?php echo $username ?>';
var match = '<?php echo $match_link ?>';
</script>
</body>
</html>