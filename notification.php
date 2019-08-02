<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
$notification_list_yesterday = "";
$notification_list = "";
$notification_list_today = '';
$sql1 = "SELECT notescheck FROM user where u_name = '$log_username'";
$query1 = mysqli_query($db_conx,$sql1);
$result = mysqli_fetch_assoc($query1);
$last_notes_check = $result["notescheck"];
$sql = "SELECT * FROM notifications WHERE reciever = '$log_username' ORDER BY date_time DESC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$notification_list = "You do not have any notifications";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$noteid = $row["id"];
		$initiator = $row["initiator"];
		$challenge_score =  $row["word_best_score"];
		$sql1 = "SELECT f_name , l_name FROM user WHERE u_name = '$initiator' LIMIT 1";
		$query1 = mysqli_query($db_conx , $sql1);
		$row1 = mysqli_fetch_assoc($query1);
		$fname = $row1['f_name'];
		$lname = $row1['l_name'];
		$type = $row["type"];
		$id = $row["id"];
		$tagged_to = $row["tagged_to"];
		$date_time = $row["date_time"];
		if($last_notes_check <= $date_time){
			$color = '#FFF';
		}else{
			$color = '#E9EAED';	
		}
		$date = strftime("%b %d, %Y", strtotime($date_time));
		$time = explode(" ",$date_time);
		$time = $time[1];
		if($date == date("M d, Y")){
			if($type == "posted an image"){
				if($tagged_to == "$log_username"){
					$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='time'>$time</div></div>";
				}else{
					$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type. in his Timeline.</p></div><div id='time'>$time</div></div>";
				}
			}else if($type == "commented"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type on your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "liked"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "disliked"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "posted"){
				if($tagged_to == "$log_username"){
					$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='time'>$time</div></div>";
				}else{
					$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in his timeline</p></div><div id='time'>$time</div></div>";
				}
			}else if($type == "profile picture"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "timeline pic"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "gallery"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> updated $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "challenged"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='wordchallenge.php?&id=$id'>$fname $lname</a> $type you to beat $challenge_score on Word Rush</p></div><div id='time'>$time</div></div>";
			}else if($type == "win_wordrush"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> win the challenge of Word Rush</p></div><div id='time'>$time</div></div>";
			}else if($type == "loss_wordrush"){
				$notification_list_today .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> loss the challenge of Word Rush</p></div><div id='time'>$time</div></div>";
			}
		}else if($date == date('M d, Y',strtotime("-1 days"))){
			if($type == "posted an image"){
				if($tagged_to == "$log_username"){
					$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='time'>$time</div></div>";
				}else{
					$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type. in his Timeline.</p></div><div id='time'>$time</div></div>";
				}
			}else if($type == "commented"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type on your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "liked"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "disliked"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='time'>$time</div></div>";
			}else if($type == "posted"){
				if($tagged_to == "$log_username"){
					$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='time'>$time</div></div>";
				}else{
					$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in his timeline</p></div><div id='time'>$time</div></div>";
				}
			}else if($type == "profile picture"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "timeline pic"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "gallery"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> updated $type.</p></div><div id='time'>$time</div></div>";
			}else if($type == "challenged"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='wordchallenge.php?id=$id''>$fname $lname</a> $type you to beat $challenge_score on Word Rush</p></div><div id='time'>$time</div></div>";
			}else if($type == "win_wordrush"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> win the challenge of Word Rush</p></div><div id='time'>$time</div></div>";
			}else if($type == "loss_wordrush"){
				$notification_list_yesterday .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> win the challenge of Word Rush</p></div><div id='time'>$time</div></div>";
			}
			
		}else{
			if($type == "posted an image"){
				if($tagged_to == "$log_username"){
					$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='date_time'>$date_time</div></div>";
				}else{
					$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type. in his Timeline.</p></div><div id='date_time'>$date_time</div></div>";
				}
			}else if($type == "commented"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type on your post.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "liked"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "disliked"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type your post.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "posted"){
				if($tagged_to == "$log_username"){
					$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in your Timeline.</p></div><div id='date_time'>$date_time</div></div>";
				}else{
					$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> $type in his timeline</p></div><div id='date_time'>$date_time</div></div>";
				}
			}else if($type == "profile picture"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "timeline pic"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> changed $type.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "gallery"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> updated $type.</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "challenged"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='wordchallenge.php?id=$id''>$fname $lname</a> $type you to beat $challenge_score on Word Rush</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "win_wordrush"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> win the challenge of Word Rush</p></div><div id='date_time'>$date_time</div></div>";
			}else if($type == "loss_wordrush"){
				$notification_list .= "<div id='notification_list' style='background-color:$color'><div id='notification'><p><a href='home.php?u=$initiator'>$fname $lname</a> win the challenge of Word Rush</p></div><div id='date_time'>$date_time</div></div>";
			}
		}
	}
}
mysqli_query($db_conx, "UPDATE user SET notescheck=now() WHERE u_name='$log_username' LIMIT 1");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Notification</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style type="text/css">
div#notesBox{float:left; width:1190px; border:#F0F 1px dashed; margin-right:60px; padding:10px;}
#notesBox hr{
	border-width:5px;
	color:#666;
	margin-top:8px;
	margin-bottom:8px;
    border-style:solid;
    border-width: 2px;
}
#notification_list{
	margin-top:10px;
	margin-bottom:10px;
	border:1px solid #000;	
	padding-top:5px;
	padding-bottom:5px;
}
#notification{
	width:1122px;
	max-width:1122px;
	float:left;	
}
#notification_list > a{
		
}
#notification_list:hover{
	background-color:#FFF;	
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
  <!-- START Page Content -->
  <div id="notesBox"><h2 style="color:#0084c6; padding-top:26px;">Notifications</h2><hr>
  <?php 
  	if($notification_list == '' && $notification_list_today == '' && $notification_list_yesterday == ''){
		echo '<div id="notification_list">You have no notification today</div>';
	}
  ?>
    <?php
    if($notification_list_today == ''){
    	
    }else{
		echo "<h3>Today's</h3>";
  		echo $notification_list_today; 
    }
    ?>
    <?php
    if($notification_list_yesterday == ''){
    	
    }else{
		echo "<h3>Yesterday's</h3>";
  		echo $notification_list_yesterday; 
    }
    ?>
    <?php
	if($notification_list != ''){
		echo "<h3>Older Notification</h3>";
  		echo $notification_list;
	}
    ?>
  </div>
  <!-- END Page Content -->
</div>
</div>
<?php
include("includes/footer.php");
?>
</body>
</html>
<?php
	$sql = "UPDATE notifications SET did_read = '1' WHERE reciever = '$log_username'";
	$query = mysqli_query($db_conx,$sql);
?>