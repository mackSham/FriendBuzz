<?php 
	include_once("includes/db_conx.php");
	$log_username=$_SESSION['username'];
	$sql="SELECT * from user WHERE u_name='$log_username'";
	$user_query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$loggeduser_id = $row["id"];
	$loggeduser_fn = $row["f_name"];
	$loggeduser_ln = $row["l_name"];
	$loggeduser_username = $row["u_name"];
	$loggeduser_profilepic = $row["avatar"];
}
if($loggeduser_profilepic==""){
	$loggedusersprofilepic='<img src="images/default_pic.jpg" width=30px height=30px />';
}else{
	$loggedusersprofilepic='<img src="user/'.$loggeduser_username.'/profilepic/'.$loggeduser_profilepic.'" width=30px height=30px />';	
}
?>

<?php 
if(isset($_POST["findfriend"])){
	$friendslist = "";
	$findfriend = preg_replace('#[^a-z 0-9,]#i', '', $_POST["findfriend"]);
	$findfriends = $_POST["findfriend"];
	$sql = "SELECT * FROM user WHERE (f_name like '%$findfriends%') OR (l_name like '%$findfriends%') OR (u_name like '%$findfriends%')";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$f_name = $row["f_name"];
		$l_name = $row["l_name"];
		$u_name = $row["u_name"];
		$avatar = $row["avatar"];
		if($avatar==''){
			$avatar ='images/default_pic.jpg';
		}else{
			$avatar = 'user/'.$u_name.'/profilepic/'.$avatar.'';	
		}
		$friendslist .= "$f_name|$l_name|$u_name|$avatar|||";
    }
	mysqli_close($db_conx);
	$friendslist = trim($friendslist,"|||");
	echo $friendslist;
	exit();
}
?>

<?php

$sql = "SELECT message_id AS messages_unread FROM messages WHERE r_uname = '$log_username' AND timesent > messages_last_seen";

$result = mysqli_query($db_conx , $sql);

$row_count = mysqli_num_rows($result);
?>

<?php $loginLink = '<a href="home.php?u='.$log_username.'">'.$loggeduser_fn.' '.$loggeduser_ln.'</a>' ?>
<?php
	$sql = "SELECT did_read FROM notifications WHERE reciever='$log_username' && did_read='0'";
	$query = mysqli_query($db_conx,$sql);
	$noofrows = mysqli_num_rows($query);

?>
<script>
function showsearchfriend(){
	var friend = _("findfriend").value;
	window.location = "searchedfriends.php?friend="+friend;	
}
</script>
<div id="page_top">
    	<div id="page_top_wrap">
        	<div id="page_top_logo">
        		<img src="images/Friend Buzz logo.png" width="300" height="50" style="float:left">
        	</div>
            <div id="page_top_rest">
            	<div id="search_box_container"><div id="showname"><?php echo $loggedusersprofilepic; ?>&nbsp;&nbsp;<span><?php echo $loginLink; ?></span></div><div id="search_box"><input type="search" placeholder="Find Friends" onsearch="showsearchfriend()" id="findfriend"/><div id="friendlist"></div></div></div>
                <div id="menu">
                	<a href="home.php"> Home </a>
                	<a href="messages.php"> Message(<?php  echo $row_count ?>)</a>
                	<a href="friendrequest.php"> Friend Requests </a>
               	 	<a href="notification.php"> Notification(<?php  echo $noofrows ?>)</a>
                    <a href="game.php"> Games </a>
                	<a href="setting.php?user_verified=false"> Account Settings </a>
                    <a href="logout.php"> Log Out </a>              
                </div>
            </div>
        </div>
    </div>