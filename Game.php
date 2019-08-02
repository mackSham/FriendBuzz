<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Game</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style>
#game{
	padding:10px;	
}
h2{
	color:#0084c6;
	padding-top:28px;	
}
#game hr{
	border-width:5px;
	color:#666;
	margin-top:8px;
	margin-bottom:8px;
    border-style:solid;
    border-width: 2px;
}
#gamesposter{
	margin-top:8px;
	margin-bottom:8px;
	margin-left:75px;
	width:300px;
	height:430px;	
	float:left;
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
</head>
<body>
<?php 
include("includes/header.php");
?>
 <div id="page_middle">
    <div id="page_middle_content">
        <div id="game">
            <h2>Games</h2>
            <hr>
        </div>
        <div id="gamesposter">
        	<a href="word.php"><img src="images/Screenshot_2016-04-06-09-37-29.png" width="300px" height="430px"></a>
        </div>
        <div id="gamesposter">
        	<a href="memory_game.php"><img src="images/Memory Flip game.png" width="300px" height="430px"></a>
        </div>
        <div id="gamesposter">
        	<img src="images/Moregame copy.jpg" width="300px" height="430px">
        </div>
	</div>
</div>
<?php
include("includes/footer.php");
?>
</body>
</html>