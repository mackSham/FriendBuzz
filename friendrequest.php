<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?><?php
$friend_requests = "";
$sql = "SELECT * FROM friends WHERE user2='$log_username' AND accepted='0' ORDER BY datemade ASC";
$query = mysqli_query($db_conx, $sql);
$numrows = mysqli_num_rows($query);
if($numrows < 1){
	$friend_requests = 'No friend requests';
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$reqID = $row["id"];
		$user1 = $row["user1"];
		$datemade = $row["datemade"];
		$datemade = strftime("%B %d", strtotime($datemade));
		$thumbquery = mysqli_query($db_conx, "SELECT f_name,l_name,avatar FROM user WHERE u_name='$user1' LIMIT 1");
		$thumbrow = mysqli_fetch_row($thumbquery);
		$user1fname = $thumbrow[0];
		$user1lname = $thumbrow[1];
		$user1avatar = $thumbrow[2];
		$user1pic = '<img src="user/'.$user1.'/profilepic/'.$user1avatar.'" alt="'.$user1.'" class="user_pic">';
		if($user1avatar == NULL){
			$user1pic = '<img src="images/default_pic.jpg" alt="'.$user1.'" class="user_pic">';
		}
		$friend_requests .= '<div id="friendreq_'.$reqID.'" class="friendrequests">';
		$friend_requests .= '<a href="home.php?u='.$user1.'">'.$user1pic.'</a>';
		$friend_requests .= '<div class="user_info" id="user_info_'.$reqID.'"><a href="home.php?u='.$user1.'">'.$user1fname.' '.$user1lname.'</a> requests friendship<br />on '.$datemade.'</div><div class="acceptreject">';
		$friend_requests .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')"><b>Accept</b></button>  ';
		$friend_requests .= '<button onclick="friendReqHandler(\'reject\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')"><b>Reject</b></button>';
		$friend_requests .= '</div>';
		$friend_requests .= '</div>';
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
div#friendReqBox{float:left; width:1190px; border:#F0F 1px solid; padding:10px; min-height:540px}
div#friendReqBox h2{color:#0084c6; font-family:Arial, Helvetica, sans-serif; padding-top:26px;}
div#friendReqBox hr{ margin-bottom:12px;}
div.friendrequests{height:74px; border:#CCC 1px solid; margin-bottom:8px; width:570px; display:inline-block; margin-right:10px;background-color:#FFF; padding-top:8px; padding-left:12px;}
img.user_pic{float:left; width:68px; height:68px; margin-right:8px;}
div.user_info{float:left; font-size:14px; padding:15px; font-size:17px;}
div.user_info a{text-decoration:none; color:#009;}
div.acceptreject{float:right; position:relative; top:22px; right:10px;}
div.acceptreject button{padding:3px; width:59px;}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script type="text/javascript">
function friendReqHandler(action,reqid,user1,elem){
	var conf = confirm("Press OK to '"+action+"' this friend request.");
	if(conf != true){
		return false;
	}
	_(elem).innerHTML = "processing ...";
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "accept_ok"){
				_(elem).innerHTML = "<b>Request Accepted!</b><br />Your are now friends";
			} else if(ajax.responseText == "reject_ok"){
				_(elem).innerHTML = "<b>Request Rejected</b><br />You chose to reject friendship with this user";
			} else {
				_(elem).innerHTML = ajax.responseText;
			}
		}
	}
	ajax.send("action="+action+"&reqid="+reqid+"&user1="+user1);
}
</script>
</head>
<body>
<?php 
include("includes/header.php");
?>
<div id="page_middle">
  <!-- START Page Content -->
    <div id="page_middle_content">
  		<div id="friendReqBox"><h2>Friend Requests</h2><br><hr><?php echo $friend_requests; ?></div>
  		<div style="clear:left;"></div>
    </div>
  <!-- END Page Content -->
</div>
<?php
include("includes/footer.php");
?>
</body>
</html>