<?php
include_once("includes/check_login_status.php");
$u = $_GET["u"];
?>
<?php
if(!isset($_SESSION['username'])) {
    header("location: index.php");
}
?>

<?php
	$sql="SELECT s_uname,r_uname FROM messages WHERE s_uname='$log_username' OR r_uname='$log_username' ORDER BY timesent DESC";
	$result = mysqli_query($db_conx, $sql);
	$friends = array();
	while($row=mysqli_fetch_assoc($result)){
		array_push($friends, $row["s_uname"]);
		array_push($friends, $row["r_uname"]);
	}
	
	$friends = array_unique($friends);
	
	if (array_search("$log_username",$friends)!==false)
  	{
		$key = array_search("$log_username",$friends);
  		unset($friends[$key]);
  	}
	$showuser = "";
	$actualmsg = "";
	///to check if u variable is set or not
	$friends = array_values($friends);
	if(!isset($_GET["u"])){
		
		header("location:messages.php?u=$friends[0]");
	}
	$u = $_GET["u"];
	if(count($friends)===0){
		$showuser = "You Have no msg yet";	
	}
	if (!in_array("$u", $friends))
  	{
  	$actualmsg = "You have not talk to that person yet Start a conversation right now";
  	}
	$sql="SELECT id FROM user WHERE u_name='$u' LIMIT 1";
	$result = mysqli_query($db_conx,$sql);
	$rowcount = mysqli_num_rows($result);
	if($rowcount < 1){
		if($u!=''){
		header("location:message.php?msg='This username thus not exits in our database'");
		}
	}
	foreach($friends as $friend){
		$sql = "SELECT * FROM messages WHERE (s_uname='$log_username' AND r_uname='$friend')OR(s_uname='$friend' AND r_uname='$log_username') ORDER BY timesent DESC LIMIT 1";
		$result = mysqli_query($db_conx, $sql);
		
		while($row=mysqli_fetch_assoc($result)){
			$query = "SELECT f_name,l_name,avatar FROM user WHERE u_name='$friend' LIMIT 1";
			$answer = mysqli_query($db_conx,$query);
			$row1=mysqli_fetch_assoc($answer);
			$fname = $row1["f_name"];
			$lname = $row1["l_name"];
			$suname = $row["s_uname"];
			$friend_avatar = $row1["avatar"];
			if($friend_avatar != ""){
				$friend_pic = 'user/'.$friend.'/profilepic/'.$friend_avatar.'';  
			} else {
				$friend_pic = 'images/default_pic.jpg';
			}
			$uname = $friend;
			$message = $row["message"];		
			$timesent = $row["timesent"];
			$sql0 = "SELECT timesent > messages_last_seen AS messages_unread FROM messages WHERE s_uname = '$friend' AND r_uname = '$log_username' ORDER BY message_id DESC LIMIT 1";
			$result0 = mysqli_query($db_conx, $sql0);
			
			$row0 = mysqli_fetch_assoc($result0);
			
			if($friend==$u){
				if($row0["messages_unread"]==1){
					$showuser .='<div id="userbox" onclick="window.location =\'messages.php?u='.$friend.'\';" style="background-color:#AAA">';
				}else{
					$showuser .='<div id="userbox" onclick="window.location =\'messages.php?u='.$friend.'\';" style="background-color:#FFFFFF">';
				}
			}else{
				if($row0["messages_unread"]==1){
					$showuser .='<div id="userbox" onclick="window.location =\'messages.php?u='.$friend.'\';" style="background-color:#AAA">';
				}else{
					$showuser .='<div id="userbox" onclick="window.location =\'messages.php?u='.$friend.'\';">';
				}
			}
			$showuser .='<table border="0">';
			$showuser .= '<tr><td valign="top" rowspan="2" width="76px"><div id="pic"><img src="'.$friend_pic.'"></div></td>';
			$showuser .='<td><div id="name">&nbsp;'.$fname.' '.$lname.'</div>';
			if($suname===$friend){
				$reply = 'images/replyback.jpg';
			}else{
				$reply = 'images/reply.jpg';
			}
			
			$showuser .='<div id="msg">&nbsp;<img src="'.$reply.'">&nbsp;'.$message.'</div></td></tr>';
			$showuser .='<tr><td valign="bottom"><div id="time">'.$timesent.'</div></td></tr>';
			$showuser .='</table>';
			$showuser .='</div>';
	}
}
?>
<?php
$query = "SELECT f_name,l_name,avatar FROM user WHERE u_name='$u' LIMIT 1";
$queryresult=mysqli_query($db_conx,$query);
$row=mysqli_fetch_assoc($queryresult);
$f_name=$row['f_name'];
$l_name=$row['l_name'];
$hisavatar = $row['avatar'];
if($hisavatar != ""){
	$his_pic = 'user/'.$u.'/profilepic/'.$hisavatar.'';  
} else {
	$his_pic = 'images/default_pic.jpg';
}
$query1 = "SELECT avatar FROM user WHERE u_name='$log_username' LIMIT 1";
$queryresult1=mysqli_query($db_conx,$query1);
$row1=mysqli_fetch_assoc($queryresult1);
$myavatar = $row1["avatar"];
if($myavatar != ""){
	$my_pic = 'user/'.$log_username.'/profilepic/'.$myavatar.'';  
} else {
	$my_pic = 'images/default_pic.jpg';
}
$sql = "SELECT * FROM messages WHERE (s_uname='$log_username' AND r_uname='$u')OR(s_uname='$u' AND r_uname='$log_username') ORDER BY timesent ASC";
$result=mysqli_query($db_conx,$sql);
while($row=mysqli_fetch_assoc($result)){
	$s_uname = $row['s_uname'];
	$r_uname = $row['r_uname'];
	$time = $row['timesent'];
	$msg = $row['message'];
	if($s_uname===$log_username){
	$actualmsg .='<div id="mymsgbox"><img src="'.$my_pic.'" width="25px" height="25px"><div id="mymsg">'.$msg.'</div><br>';
	$actualmsg .='<div id="mymsgtime">'.$time.'</div></div>';	
	}else{
	$actualmsg .='<div id="othermsgbox"><img src="'.$his_pic.'" width="25px" height="25px"><div id="othermsg">'.$msg.'</div><br>';
	$actualmsg .='<div id="othermsgtime">'.$time.'</div><br></div>';	
	}
}

?>
<?php
	if(isset($_POST["submit"])){
		$msg=$_POST["msg"];
		if($msg==''){
			echo 'Please enter any msg';
			exit();	
		}
		if($u == ""){
			echo 'Please specify to whom you want to send the message';
			exit();	
		}
		$sql = "INSERT INTO messages (s_uname,r_uname,message,timesent) VALUE ('".$log_username."','".$u."','".$msg."',now())";
		if(mysqli_query($db_conx,$sql)){
		header("location:messages.php?u=$u");	
		}
	} 
?>
<!DOCTYPE Html>
<html>
	<head>
    	<meta charset="utf-8">
		<title>Message</title>
         <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script type="text/javascript">
			function updateScroll(){
    			var element = document.getElementById("actualmsgbody");
    			element.scrollTop = element.scrollHeight;
			}
		</script>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <style>
		#newmsgbox{
			width:310px;
			border-right:solid #000000 1px;
			float:left;
			max-height:600px;
			overflow-y:scroll;
			overflow-x:visible;
		}
		#msgbox_header{
			padding:15px;
			padding-top:18px;
			width:260px;

		}
		#msgbox_header a{
			margin-right:25px;
			text-decoration:none;	
		}
		.active{
			font-weight:bold;	
		}
		#userbox{
			width:280px;
			height:80px;
			padding:5px;
			cursor:pointer;
			border-bottom:1px solid #AAA;
		}
		#userbox table{
			width:280px;
			height:80px;
		}
		#pic{
			width:50px;
			height:50px;	
		}
		#pic img{
			width:75px;
			height:75px;	
		}
		#name{
			font-weight:bold;
		}
		#msg{
			font-size:13px;
			opacity:0.9;
			color:#333;
			width:180px;
			overflow:hidden;
			text-overflow: ellipsis;	
		}
		#msg img{
			position:relative;
			top:4px;	
		}
		#time{
			font-size:12px;
			float:right;	
		}
		/*
		Start of message center part
		*/
		#msgcenpart{
			float:left;
			width:500px;
			height:auto;	
		}
		#messengername{
			margin-top:25px;
			margin-left:25px;
			width:280px;
			height:25px;
			font-size:20px;
			font-weight:bold;
		}
		#actualmsgbody{
			width:480px;
			height:400px;
			border:1px solid #AAA;
			background-color:#FFF;
			border-radius:5px;
			margin:5px auto;
			overflow-y:scroll;	
		}
		#mymsg{
		 	float:right;
			width:auto;
			max-width:320px;
			height:auto;
			background-color:#CF6;
			padding:5px;
			margin-right:6px;
			border-radius:3px;
			word-wrap: break-word;	
		}
		#othermsg{
			float:left;
			width:auto;
			max-width:320px;
			height:auto;
			background-color:#0C6;
			padding:5px;
			margin-left:6px;
			border-radius:3px;
			word-wrap: break-word;	
		}
		#mymsgtime{
			float:left;
			font-size:10px;	
		}
		#othermsgtime{
			float:right;
			font-size:10px;
		}
		#othermsgbox{
			float:left;
			margin-top:6px;
			margin-left:2px;
			clear:both;	
		}
		#othermsgbox img{
			float:left;	
		}
		#mymsgbox{
			float:right;
			margin-top:6px;
			margin-right:2px;
			clear:both;	
		}
		#mymsgbox img{
			float:right;
		}
		#reply{
			margin:2px auto;
			width:480px;
		}
		#reply textarea{
			padding:5px;
			width:472px;
			height:47px;
		}
		#reply input{
			float:right;
			padding:3px;	
		}
		input[type="submit"]{
			-webkit-user-select: none;
			background: rgb(76, 142, 250);
			border: 0;
			border-radius: 2px;
			box-sizing: border-box;
			color: #fff;
			cursor: pointer;
			float: right;
			font-size: .875em;
			margin: 0;
			padding: 10px 24px;
			transition: box-shadow 200ms cubic-bezier(0.4, 0, 0.2, 1);	
		}
		/*
		End of message center part
		*/
		</style>
       
  	</head>
    <body onload="updateScroll()">
    <?php 
		include("includes/header.php");
	?>
    <div id="page_middle" style="height:700px;">
    	<div id="page_middle_content" style="height:700px;">
        	<div id="newmsgbox"> 
            <div id="msgbox_header">
            	<a class="active" href="messages.php">Inbox</a><a href="groupmessages.php">Group</a>
            </div>
            <hr>
    		<?php
				echo $showuser;
			?>
            </div>
            <!--Start of message center part -->
            <div id='msgcenpart'>
         		<div id="messengername">
                <?php echo $f_name; echo ' '; echo $l_name; ?>	
                </div>
                <div id="actualmsgbody">
                <?php 
					echo $actualmsg;
				?>
                </div>
                <div id="reply">
                <form method="post">
            		<textarea cols="65" rows="3" placeholder="Enter your msg here......" name="msg" required></textarea>
                    <input type="submit" value="Send" name="submit">
                </form>
            	</div>
            </div>
            <!-- End of message center part-->
        </div>
  	</div>
    
	<?php
		include("includes/footer.php");
	?>
	
<?php

$sql = "UPDATE messages SET messages_last_seen = now() WHERE s_uname = '$u' AND r_uname = '$log_username'";

mysqli_query($db_conx, $sql);
?>    