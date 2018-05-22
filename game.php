<?php 
if(!isset($_GET['match'])){
    header("Location: index.php");
}
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
<?php echo $_GET['match'];?>
<!-- Tabindex to make it focusable -->
<canvas tabindex="1" id="Canvas" width="700" height="600" style="border: 1px solid black"></canvas>
<script src="../game.js"></script>
</body>
</html>