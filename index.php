<?php
  session_start();
  if (!isset($_SESSION['username'])) {
  	$_SESSION['msg'] = "You must log in first";
  	header('location: login.php');
  }
  if (isset($_GET['logout'])) {
  	session_destroy();
  	unset($_SESSION['username']);
  	header("location: login.php");
  }
  //generator
 function generator () {
	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < 8; ++$i) {
		$pieces []= $keyspace[random_int(0, $max)];
	}

	$_SESSION['link'] = implode('', $pieces);
 }
 if (isset($_GET['action'])) {
	$db = mysqli_connect('localhost', 'root', '', 'registration');

	//
	do{	
		generator();
		$sess_link = $_SESSION['link'];
		$match_link_check_query = "SELECT match_link FROM matches WHERE match_link='$sess_link' LIMIT 1";
		$result = mysqli_query($db, $match_link_check_query);
		$match_link = mysqli_fetch_assoc($result);
	}
	while($match_link['match_link'] === $sess_link);
	//
	
	//checks number of links created by user; if > 5 stops
	$sess_username=$_SESSION['username'];
	$count_links="SELECT COUNT(*) as num FROM matches WHERE creator_id =(SELECT ID FROM users WHERE username ='$sess_username')";
	$result = mysqli_query($db, $count_links);
	$counter = mysqli_fetch_assoc($result);
	if ($counter['num'] >=5 ) { echo "You can not have more that 5 active games"; }
	else{  
	$query="INSERT INTO matches (match_link,creator_id) Values ('$sess_link',(SELECT ID FROM users WHERE username ='$sess_username'))";
	mysqli_query($db,$query);}
	
	echo $_SESSION['link'];
	
	
	
	exit();
 }

?>
<!DOCTYPE html>
<html>
<head>
	<title>Home</title>
	<link rel="stylesheet" type="text/css" href="style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</head>
<body>

<div class="header">
	<h2>Home Page</h2>
</div>
<div class="content">
  	<!-- notification message -->
  	<?php if (isset($_SESSION['success'])) : ?>
      <div class="error success" >
      	<h3>
          <?php
          	echo $_SESSION['success'];
          	unset($_SESSION['success']);
          ?>
      	</h3>
      </div>
  	<?php endif ?>

    <!-- logged in user information -->
    <?php  if (isset($_SESSION['username'])) : ?>
    	<p>Welcome <strong><?php echo $_SESSION['username']; ?></strong></p>
    	<p> <a href="index.php?logout='1'" style="color: red;">logout</a> </p>
    <?php endif ?>

	<input type="submit" class="button" name="generate" value="Generate link">

	<script> 
		
	$('.button').click(function() {

		$.ajax({
		type: "GET",
		url: "index.php",
		data: { action: "abc" }
		}).done(function(msg) {
		if (msg=="You can not have more that 5 active games") {
			alert(msg);}
		else {
		window.location.href = "game/"+msg;}
		});
  	 });

	
	</script>
	</div>
</div>

</body>
</html>