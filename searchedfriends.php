<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in header them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>
<?php
if(isset($_GET["friend"])){
	$friend = '';
	$findfriends = $_GET["friend"];
	
	$sql = "SELECT * FROM user WHERE (f_name like '%$findfriends%') OR (l_name like '%$findfriends%') OR (u_name like '%$findfriends%')";
	
	$query = mysqli_query($db_conx,$sql);
	while($row = mysqli_fetch_assoc($query)){
		$f_name = $row["f_name"];
		$l_name = $row["l_name"];
		$u_name = $row["u_name"];
		$avatar = $row["avatar"];
		
		if($avatar==''){
			$avatar ='images/default_pic.jpg';
		}else{
			$avatar = 'user/'.$u_name.'/profilepic/'.$avatar.'';	
		}
		$friend .= '<a href="home.php?u='.$u_name.'"><div id="friendbox">';
		$friend .= '<div id="friendpics">';
		$friend .= '<img src="'.$avatar.'" width="90px" height="110px">';
		$friend .= '</div>';
		$friend .= '<div id="friendname">';
		$friend .= ''.$f_name.' '.$l_name.'';
		$friend .= '</div>';
		$friend .= '<div id="frienduname">';
		$friend .= ''.$u_name.'';
		$friend .= '</div>';
		$friend .= '<div id="'.$u_name.'" class = friendbutton>';
		$sql1 = "SELECT * FROM friends WHERE ((user1 = '$log_username' && user2 = '$u_name') || (user1 = '$u_name' && user2 = '$log_username')) LIMIT 1";
		$query1 = mysqli_query($db_conx,$sql1);
		$nors = mysqli_num_rows($query1);
		$row = mysqli_fetch_assoc($query1);
		$reqID = $row["id"];
		$user1 = $row["user1"];
		if($u_name == $log_username){
			//do nothimg
		}else if($row["accepted"] == '0'){
				if($row["user1"] == $log_username){
						$friend .= '<button onclick="friendToggle(\'cancelfriendrequest\',\''.$u_name.'\',\'friendBtn\')">Cancel Friend Request</button>';
					}else{
						$friend .= '<button onclick="friendReqHandler(\'accept\',\''.$reqID.'\',\''.$user1.'\',\'user_info_'.$reqID.'\')">Accept</button>';
					}
		}else if($row["accepted"] == '1'){
			$friend .= '<button onClick="friendToggle(\'unfriend\',\''.$u_name.'\',\'friendBtn\')">Unfriend</button>';
		}else{
			$friend .= '<button onclick="friendToggle(\'friend\',\''.$u_name.'\',\'friendBtn\')">Request As Friend</button>';
		}
		$friend .= '</div>';
		$friend .= '</div>';
	}
	
}

?>
<html>
<head>
<title>
	Friend Search
</title>
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style>
	#a{
		color:#009;
	}
	#friendbox{
		margin-left:90px;
		margin-top:50px;
		width:544px;
		border:1px solid #000;
		float:left;
	}
	
	#friendpics{
		width:90px;
		height:110px;
		float:left;		
	}
	#friendname{
		width:260px;
		padding-left:110px;
		margin-top:35px;
		font-weight:bold;
	}
	#frienduname{
		margin-left:110px;
		margin-top:2px;	
		
	}
	.friendbutton{
		float:right;	
		position:relative;
		top:-30px;
		right:10px;
	}
	.friendbutton > button{
		font-weight:bold;
		padding:1px;	
	}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script src="js/expand_retract.js"></script>
<script src="js/autoscroll.js"></script>
<script src="js/fadeeffect.js"></script>
<script type="text/javascript">
function friendToggle(type,user,elem){
	var conf = confirm("Press OK to confirm the '"+type+"' action for user '"+user+"'.");
	if(conf != true){
		return false;
	}
	_(user).innerHTML = '<button>Please Wait....</button>';
	var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "friend_request_sent"){
				_(user).innerHTML = '<button onclick="friendToggle(\'cancelfriendrequest\','+user+',\'friendBtn\')">Cancel friend request</button>';
			} else if(ajax.responseText == "unfriend_ok"){
				_(user).innerHTML = '<button onclick="friendToggle(\'friend\','+user+',\'friendBtn\')">Request As Friend</button>';
			} else if(ajax.responseText=="friendrequestcancelled"){
				_(user).innerHTML = '<button onclick="friendToggle(\'friend\','+user+',\'friendBtn\')">Request As Friend</button>';
			}else {
				alert(ajax.responseText);
				_(user).innerHTML = '<button onclick="friendToggle(\'friend\','+user+',\'friendBtn\')">Request As Friend</button>';	
			}
		}
	}
	ajax.send("type="+type+"&user="+user);
}
</script>
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
		include_once("includes/header.php");	
	?>
    <div style="top:100px; position:absolute">
    <?php
    	echo $friend;
	?>
    </div>
</body>
</html>