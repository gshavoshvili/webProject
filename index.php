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
 if (isset($_GET['action'])) {
    
	$keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$pieces = [];
	$max = mb_strlen($keyspace, '8bit') - 1;
	for ($i = 0; $i < 8; ++$i) {
		$pieces []= $keyspace[random_int(0, $max)];
	}
	if (!isset($_SESSION['link1'])) {
	$_SESSION['link1'] = implode('', $pieces);}
	elseif (!isset($_SESSION['link2'])) {
		$_SESSION['link2'] = implode('', $pieces);}
	elseif (!isset($_SESSION['link3'])) {
		$_SESSION['link3'] = implode('', $pieces);}
	elseif (!isset($_SESSION['link4'])) {
		$_SESSION['link4'] = implode('', $pieces);}
	elseif (!isset($_SESSION['link5'])) {
		$_SESSION['link5'] = implode('', $pieces);}		
	else echo "You can't create more games";
	exit();
 }

 /*if (isset($_SESSION['link'])){
	 echo $_SESSION['link'];
 }*/
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
		if (msg=="You can't create more games") {
		alert( msg );}
		else {
		window.location.href = "game.php";
		}
		});
  	 });

	
	</script>
	</div>
</div>

</body>
</html>