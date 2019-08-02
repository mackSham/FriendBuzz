<?php
include("includes/check_login_status.php");
$gid = $_GET["gid"];

$showmembername = '';
?>
<?php
if(!isset($_SESSION['username'])) {
    header("location: index.php");
}
?>

<?php
$sql="SELECT * from user WHERE u_name='$log_username'";
$user_query = mysqli_query($db_conx, $sql);
while ($row = mysqli_fetch_assoc($user_query)){
	$loggeduser_fn = $row["f_name"];
	$loggeduser_ln = $row["l_name"];
}

?>



<?php
	if(isset($_POST["submit"])){
		$msg=$_POST["msg"];
		if($msg==''){
			echo 'Please enter any msg';
			exit();	
		}
		$sql = "INSERT INTO conversations_messages (conversation_id,user_id,message_date,message_text) VALUE ('".$gid."','".$log_id."',now(),'".$msg."')";
	
		if(mysqli_query($db_conx,$sql)){
		header("location:groupmessages.php?gid=$gid");	
		}
	} 
?>
<?php
if(isset($_GET["delete_conversation"])){

$delete_conversation = $_GET["delete_conversation"];

$sql = "SELECT 
			conversations.conversation_id, 
			conversations_member.user_id 
		FROM conversations
		INNER JOIN conversations_member ON conversations.conversation_id = conversations_member.conversation_id
		WHERE conversations.conversation_id = $delete_conversation 
		AND conversations_member.user_id = $log_id
		GROUP BY conversations.conversation_id";
		
$result = mysqli_query($db_conx, $sql);

$count_row = mysqli_num_rows($result);

if($count_row < 1){
	echo 'This is not a joke OK';
	exit();	
}

$sql = "SELECT DISTINCT conversation_deleted
			FROM conversations_member
			WHERE user_id != $log_id
			AND conversation_id = $delete_conversation";

$query = mysqli_query($db_conx,$sql);

$row = mysqli_fetch_assoc($query);

if(mysqli_num_rows($query)==1 && $row['conversation_deleted']==1){
	mysqli_query($db_conx,"DELETE FROM conversations WHERE conversation_id=$delete_conversation");
	mysqli_query($db_conx,"DELETE FROM conversations_member WHERE conversation_id=$delete_conversation");
	mysqli_query($db_conx,"DELETE FROM conversations_messages WHERE conversation_id=$delete_conversation");
}else{
	$sql = "UPDATE conversations_member SET conversation_deleted='1' WHERE conversation_id=$delete_conversation AND user_id = $log_id";
	$query1= mysqli_query($db_conx,$sql);
}

}
?>
<?php 
if(isset($_POST['addgroup'])){
	$errors = array();
	if($_POST['groupname']==""){
		$errors[] = "The group name can not be empty";
	}
	if($_POST['groupmembers']==""){
		$errors[] = "The members list can not be empty";
	}else if(preg_match('#^[a-z0-9, ]+$#i',$_POST["groupmembers"])===0){
		$errors[] = "The given lists of members does not look good";
	}else{
		$user_names = explode(',',$_POST['groupmembers']);
		foreach($user_names as &$name){
			$name = trim($name);
		}
		foreach ($user_names as &$name){
		$name = mysqli_real_escape_string($db_conx, $name);
		}
		$sql = "SELECT id ,u_name FROM user WHERE u_name IN ('".implode("', '",$user_names)."')";
		$result = mysqli_query($db_conx,$sql);
		$names = array();
		while($row=mysqli_fetch_assoc($result)){
			$names[$row['u_name']] = $row['id'];
		}
		$user_ids = $names;
		if(count($user_ids) !== count($user_names)){
			$errors[] = 'The following user could not be found:' .implode(', ',array_diff($user_names, array_keys($user_ids)));	
		}
	}
	
	//execute when thre is no error
	if(empty($errors)){
		$user_ids=array_unique($user_ids);
		$group_name=$_POST["groupname"];
		$sql = "INSERT INTO conversations (conversation_name,date_created,user_created) VALUES ('$group_name',now(),'$log_username')";
		if(mysqli_query($db_conx,$sql)){
			echo "success";	
		}else{
			die(mysqli_error($db_conx));	
		}
		$conversation_id = mysqli_insert_id($db_conx);
		
		$values = array("($conversation_id, $log_id, now(), '0')");
		
		foreach($user_ids as $user_id){
			$user_id = (int) $user_id;
			$values[] = "($conversation_id, $user_id, 0, '0')";
		}
		$sql = "INSERT INTO conversations_member (conversation_id,user_id,conversation_last_view,conversation_deleted) VALUES" . implode(", ",$values);	
		if(mysqli_query($db_conx,$sql)){
			echo "Succ";	
		}else{
			die(mysqli_error($db_conx));	
		}
	}
	header("location:groupmessages.php");
}

if(isset($errors)){
	if(empty($errors)){
		echo "alert(Success...)";
	}else{
		foreach($errors as $error){
		echo "alert($error)";	
		}
	}
}  
?>
<?php
	
	$ids= array();
	
	$sql = "SELECT 
				conversations.conversation_id, 
				conversations.conversation_name ,
				MAX(conversations_messages.message_date) AS conversation_last_reply,
				MAX(conversations_messages.message_date) > conversations_member.conversation_last_view AS conversation_unread
			FROM conversations
			LEFT JOIN conversations_messages ON conversations.conversation_id= conversations_messages.conversation_id
			INNER JOIN conversations_member ON conversations.conversation_id=conversations_member.conversation_id
			WHERE conversations_member.user_id=$log_id
			AND conversations_member.conversation_deleted = '0'
			GROUP BY conversations.conversation_id
			ORDER BY conversation_last_reply DESC";
			
	$result = mysqli_query($db_conx,$sql);
	
	$row_count = mysqli_num_rows($result);
		
	$group_user = "";
	
	if($row_count==0){
		$group_user = 'You have not created any group';	
	}
	
	while($row=mysqli_fetch_assoc($result)){
		$id = $row["conversation_id"];
		
		array_push($ids,$row["conversation_id"]);
		
		if($id==$gid){
			if($row["conversation_unread"]==1){
				$group_user .='<div id="userbox" onclick="window.location =\'groupmessages.php?gid='.$id.'\';" style="background-color:#AAA">';
			}else{
				$group_user .='<div id="userbox" onclick="window.location =\'groupmessages.php?gid='.$id.'\';" style="background-color:#FFFFFF">';
			}
		}else{
			if($row["conversation_unread"]==1){
				$group_user .='<div id="userbox" onclick="window.location =\'groupmessages.php?gid='.$id.'\';" style="background-color:#AAA">';
			}else{
				$group_user .='<div id="userbox" onclick="window.location =\'groupmessages.php?gid='.$id.'\';">';
			}
		}
		$group_user .= '<div id="name">'.$row["conversation_name"].'<div id="delete"><a href="groupmessages.php?gid='.$id.'&amp;delete_conversation='.$id.'">[x]</a></div></div>';
		$query = "SELECT user_id,message_text FROM conversations_messages WHERE conversation_id=$id ORDER BY message_date DESC LIMIT 1";
		$results = mysqli_query($db_conx, $query);
		$count_rows = mysqli_num_rows($results);
		$rows = mysqli_fetch_assoc($results);
		if($count_rows < 1){
			$group_user .= '<div id="msg">No one has message yet</div>';
		}else{
			$group_user .= '<div id="msg">'.$rows["message_text"].'</div>';
			}
		$group_user .= '<div id="time">'.$row["conversation_last_reply"].'</div>';
		$group_user .='</div>';
	}
?>
<?php
	if(!isset($_GET["gid"])){
		header("location:groupmessages.php?gid=$ids[0]");
	}

?>

<?php
if(!empty($ids)){
$group_member = array();
$sql1 = "SELECT 
			user.f_name, 
			user.l_name, 
			conversations_member.user_id
		FROM conversations_member 
		INNER JOIN user ON user.id = conversations_member.user_id
		WHERE conversations_member.conversation_id = $gid";

$query1 = mysqli_query($db_conx,$sql1);

while($row1=mysqli_fetch_assoc($query1)){
	
	array_push($group_member, $row["user_id"]);
	
	$showmembername .=''.$row1["f_name"].'&nbsp;'.$row1["l_name"].',';
	
}
$showmembername = substr("$showmembername",0,-1);
$showmembername = str_ireplace("$loggeduser_fn&nbsp;$loggeduser_ln,","","$showmembername");
$showmembername = str_ireplace(",$loggeduser_fn&nbsp;$loggeduser_ln","","$showmembername");
$showmessages = "";
$sql2 = "SELECT
			user.f_name,
			user.l_name,
			user.u_name,
			user.avatar,
			conversations_messages.message_text,
			conversations_messages.message_date
		FROM conversations_messages
		INNER JOIN user ON user.id = conversations_messages.user_id
		WHERE conversations_messages.conversation_id=$gid
		ORDER BY conversations_messages.message_date ASC";
		
$query2 = mysqli_query($db_conx , $sql2);
while($row2=mysqli_fetch_assoc($query2)){
	$fname = $row2["f_name"];
	$lname = $row2["l_name"];
	$uname = $row2["u_name"];
	$photo = $row2["avatar"];
	$message = $row2["message_text"];
	$message_date = $row2["message_date"];
	if($photo != ""){
		$pic = 'user/'.$uname.'/profilepic/'.$photo.'';  
	} else {
		$pic = 'images/default_pic.jpg';
	}
	if($uname==$log_username){
		$showmessages .='<div id="mymsgbox">
									<div id="naam">'.$fname.' '.$lname.'</div>
									<img src="'.$pic.'" width="25px" height="25px"><div id="mymsg">'.$message.'</div><br>';
		$showmessages .='<div id="mymsgtime">'.$message_date.'</div></div>';
	}else{
		$showmessages .='<div id="othermsgbox">
									<div id="naam">'.$fname.' '.$lname.'</div>
									<img src="'.$pic.'" width="25px" height="25px"><div id="othermsg">'.$message.'</div><br>';
		$showmessages .='<div id="othermsgtime">'.$message_date.'</div><br></div>';
	}
}
}
?>

<!DOCTYPE Html>
<html>
	<head>
    	<meta charset="utf-8">
		<title>Friend Buzz</title>
         <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        	<script>
			function CustomLogin(){
				this.render = function(){
					var winW = window.innerWidth;
					var winH = window.innerHeight;
					var dialogoverlay = document.getElementById('dialogoverlay');
					var dialogbox = document.getElementById('dialogbox');
					dialogoverlay.style.display="block";
					dialogoverlay.style.height=winH+"px";
					dialogbox.style.left = (winW/2)-(450 * .5)+"px";
					dialogbox.style.display = "block";
					dialogbox.style.top = "100px";
				}
				this.cancel=function(){
					document.getElementById('dialogbox').style.display = "none";
					document.getElementById('dialogoverlay').style.display = "none";
				}
			}	
			var Alert = new CustomLogin();	
		</script>
        <script type="text/javascript">
			function updateScroll(){
    			var element = document.getElementById("actualgroupmsgbody");
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
		#msgbox_header a img{
			position:relative;
			top:3px;
			left:-15px;	
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
			overflow:scroll;	
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
		}
		#reply input{
			float:right;
			padding:3px;	
		}
		#delete{
			float:right;	
		}
		#delete a{
			text-decoration:none;
			color:#000000;	
		}
		#messengergroupname{
			margin-top:25px;
			margin-left:25px;
			width:450px;
			height:35px;
			font-size:18px;
			font-weight:bold;
			word-wrap:break-word;
		}
		#actualgroupmsgbody{
			position:relative;
			top:25px;
			width:480px;
			height:400px;
			border:1px solid #AAA;
			background-color:#FFF;
			border-radius:5px;
			margin:5px auto;
			overflow-y:scroll;	
		}
		#groupreply{
			position:relative;
			top:25px;
			margin:2px auto;
			width:480px;
		}
		#groupreply textarea{
			padding:5px;
			width:472px;
			height:47px;
		}
		#groupreply input{
			float:right;
			padding:3px;	
		}
		#naam{
			font-weight:bold;
			margin-bottom:2px;
		}
		/*
		End of message center part
		*/
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
            	<a href="messages.php">Inbox</a><a  class="active" href="groupmessages.php">Group</a><a onClick="Alert.render()" ><img src="images/add new.png" title="Add New Group"></a>
            </div>
            <hr>
            <?php 
			echo $group_user; 
			?>
            </div>
            <!--Start of message center part -->
            <div id='msgcenpart'>
         		<div id="messengergroupname">
                	<?php 
						echo $showmembername ;
					?>
                </div>
                <div id="actualgroupmsgbody">
                	<?php 
						if(!empty($ids)){ 
							echo $showmessages ;
						}else{
                    		echo 'You have not made any group yet';
                    	}
                    ?>
                </div>
                <div id="groupreply">
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
	  <div id="dialogoverlay" onClick="Alert.cancel()"></div>
        <div id="dialogbox">
        	<div>
            	<div id="dialogboxhead">Add new group</div>
                <hr>
                <div id="dialogboxbody">
                <form action='' method="post">
                <table>
                <tr><td>Group name : <input type="text" style="background-color:#FFFFFF; border:1px solid #E2E2E2; font-size:15px; padding:5px; width:275px; margin-bottom:3px; height:20px;" placeholder="Group name" name="groupname" required></td></tr><tr><td></td></tr><tr><td></td></tr>
                <tr><td>Group Members : <input type="text" style="background-color:#FFFFFF; border:1px solid #E2E2E2; font-size:15px; padding:5px; width:249px; margin-bottom:3px; height:20px;" placeholder="Enter username of your friends by comma seprated list" name="groupmembers" required></td></tr>
                </table>
                </div>
                <hr>
                <div id="dialogboxfoot">
                	<input type="submit" value="Add Group" name="addgroup"> </form>
                </div>
            </div>
        </div>
<?php
$sql = "UPDATE
			conversations_member 
		SET conversation_last_view = now()
		WHERE conversation_id = $gid
		AND user_id = $log_id";
		
		mysqli_query($db_conx,$sql);

?>