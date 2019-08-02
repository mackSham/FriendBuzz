<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>
<?php
$friends = "";
$u = $_GET["u"];
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friends = $u." has no friends yet";
} else {
	{
	$all_friends = array();
	$sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND()";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user1"]);
	}
	$sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND()";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user2"]);
	}
	$friendArrayCount = count($all_friends);
	$orLogic = '';
	foreach($all_friends as $key => $user){
			$orLogic .= "u_name='$user' OR ";
	}
	$orLogic = chop($orLogic, "OR ");
	$sql = "SELECT u_name, avatar, f_name, l_name FROM user WHERE $orLogic";
	$query = mysqli_query($db_conx, $sql);
	while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$friend_firstname = $row["f_name"];
		$friend_lastname = $row["l_name"];
		$friend_username = $row["u_name"];
		$friend_avatar = $row["avatar"];
		if($friend_avatar != ""){
			$friend_pic = 'user/'.$friend_username.'/profilepic/'.$friend_avatar.'';
		} else {
			$friend_pic = 'images/default_pic.jpg';
		}
		$friends .= '<div class="allfriends"><a href="home.php?u='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'" width="100px" height="100px"></a><div class="friendname"><a href="home.php?u='.$friend_username.'">'.$friend_firstname.'<br>'.$friend_lastname.'</a></div></div>';
	}
}
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Friend Requests</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style type="text/css">
div#friendReqBox{float:left; width:1190px; border:#F0F 1px solid; padding:10px; background-color:#F69;}
div#friendReqBox h2{color:#0084c6; font-family:Arial, Helvetica, sans-serif; padding-top:26px;}
div#friendReqBox hr{ margin-bottom:12px;}
.allfriends{
float:left;
margin:20px;	
	
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
  <!-- START Page Content -->
    <div id="page_middle_content">
  		<div id="friendReqBox"><h2>Friends</h2><br><hr>
		<?php
        	echo $friends;
        ?></div>
    </div>
  <!-- END Page Content -->
</div>
<?php
include("includes/footer.php");
?>
</body>
</html>