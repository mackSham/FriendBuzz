<?php
include_once("includes/check_login_status.php"); //Check whether the user is login or not
// Initialize any variables that the page might echo
$fn="";
$ln="";
$u = "";
$profile_pic = "";
$profile_pic_btn = "";
$avatar_form = "";
$timeline_pic = "";
$timeline_pic_btn = "";
$timeline_form = "";
$joindate = "";
$lastsession = "";
// Make sure the $_GET username is set, and sanitize it
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}
// Select the member from the users table
$sql = "SELECT * FROM user WHERE u_name='$u' LIMIT 1";
$user_query = mysqli_query($db_conx, $sql);
// Now make sure that user exists in the table
$numrows = mysqli_num_rows($user_query);
if($numrows < 1){
	echo "That user does not exist";
    exit();	
}
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
	$profile_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'avatar_form\')">Change Profile Picture</a>';
	$avatar_form  = '<form id="avatar_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$avatar_form .=   '<h4>Change your photo</h4>';
	$avatar_form .=   '<input type="file" name="avatar" required>';
	$avatar_form .=   '<p><br><input type="submit" value="Upload"></p>';
	$avatar_form .= '</form>';
	$timeline_pic_btn = '<a href="#" onclick="return false;" onmousedown="toggleElement(\'timeline_form\')">Change Profile Picture</a>';
	$timeline_form  = '<form id="timeline_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$timeline_form .=   '<h4>Change your Timeline Picture</h4>';
	$timeline_form .=   '<input type="file" name="timeline" required>';
	$timeline_form .=   '<p><br><input type="submit" value="Upload"></p>';
	$timeline_form .= '</form>';
}
// Fetch the user row from the query above
while ($row = mysqli_fetch_array($user_query, MYSQLI_ASSOC)) {
	$profile_id = $row["id"];
	$profile_fn = $row["f_name"];
	$profile_ln = $row["l_name"];
	$signup = $row["signup_date"];
	$lastlogin = $row["lastlogin_date"];
	$joindate = strftime("%b %d, %Y", strtotime($signup));
	$avatar = $row["avatar"];
	$timeline = $row["timeline"];
	$lastsession = strftime("%b %d, %Y", strtotime($lastlogin));
	$dt_of_birth = $row["u_bir_date"];
	$dt_of_birth = strftime("%b %d, %Y", strtotime($dt_of_birth));
}
$profile_pic = '<img src="user/'.$u.'/profilepic/'.$avatar.'" alt="'.$u.'" height="160px" width="140px">';
if($avatar == NULL){
	$profile_pic = '<img src="images/default_pic.jpg" alt="'.$u.'" height="160px" width="140px">';
}
$timeline_pic = '<img src="user/'.$u.'/timelinepic/'.$timeline.'" alt="'.$u.'" height="200px" width="500px">';
if($timeline == NULL){
	$timeline_pic = '<img src="images/tumblr_m28n18ka271rqx7i3o1_500.jpg" alt="'.$u.'" height="200px" width="500px">';
}
?>
<?php
$friendslist = array();	
	$sql1 = "SELECT user2 FROM friends WHERE user1='$log_username' AND accepted ='1'";
	$result1 = mysqli_query($db_conx,$sql1);
	while($row1 = mysqli_fetch_assoc($result1)){
		array_push($friendslist , $row1["user2"]);
	}
	$sql2 = "SELECT user1 FROM friends WHERE user2='$log_username' AND accepted ='1'";
	$result2 = mysqli_query($db_conx,$sql2);
	while($row2 = mysqli_fetch_assoc($result2)){
		array_push($friendslist , $row2["user1"]);
	}
?>
<?php
//PHP for post without image
if(isset($_POST["post"])){
	if($_POST["posteddata"]==""){
		echo '<script>alert("Please write something")</script>';
	}
	$posteddata = $_POST["posteddata"];
	
	if($log_username == $u){
		$sql = "INSERT INTO posts (poster , post_time , post , type)VALUES ('$log_username',now(),'$posteddata','a')";
		$result = mysqli_query($db_conx , $sql);
		$last_id = mysqli_insert_id($db_conx);
		foreach($friendslist as $friend){
			$sql3 = "INSERT INTO notifications (initiator , reciever ,info_id , type , did_read , date_time) VALUES
									('$log_username','$friend','$last_id','posted','0',now())";
			mysqli_query($db_conx , $sql3);
		}
	}else{
		$sql = "INSERT INTO posts (poster , postto ,post_time , post,type) VALUES
														('$log_username','$u',now(),'$posteddata','b')";
		$result = mysqli_query($db_conx , $sql);
		$last_id = mysqli_insert_id($db_conx);
		foreach($friendslist as $friend){
			$sql3 = "INSERT INTO notifications (initiator , reciever , tagged_to,info_id , type , did_read , date_time) VALUES
									('$log_username','$friend','$u','$last_id','posted','0',now())";
			mysqli_query($db_conx , $sql3);
		}
	}
	header("location:home.php?u=$u");
}
?>
<?php 
//PHP for post with image
if(isset($_POST["postwithimage"])){
	if($_POST["posteddata"]==""){
		echo "PLEASE WRITE SOMETHING";
	}
	$posteddata = $_POST["posteddata"];
	
	$fileName = $_FILES["postimage"]["name"];
    $fileTmpLoc = $_FILES["postimage"]["tmp_name"];
	$fileType = $_FILES["postimage"]["type"];
	$fileSize = $_FILES["postimage"]["size"];
	$fileErrorMsg = $_FILES["postimage"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	$db_file_name = rand(100000000000,999999999999).".".$fileExt;
	if($fileSize > 2621440) {
		header("location: message.php?msg=ERROR: Your image file was larger than 2.5mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: message.php?msg=ERROR: Your image file was not jpg, gif or png or jpeg type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "posted_images/$db_file_name");
	if ($moveResult != true) {
		header("location: message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("/includes/image_resize.php");
	$target_file = "posted_images/$db_file_name";
	$resized_file = "posted_images/$db_file_name";
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	if($log_username == $u){
		$sql = "INSERT INTO posts (poster , post_time , post , post_image, type)VALUES ('$log_username',now(),'$posteddata','$db_file_name','a')";
		$result = mysqli_query($db_conx , $sql);
		$last_id = mysqli_insert_id($db_conx);
		foreach($friendslist as $friend){
			$sql3 = "INSERT INTO notifications (initiator , reciever ,info_id , type , did_read , date_time) VALUES
									('$log_username','$friend','$last_id','posted an image','0',now())";
			mysqli_query($db_conx , $sql3);
		}
	}else{
		$sql = "INSERT INTO posts (poster, postto, post_time, post, post_image, type)
							VALUES ('$log_username', '$u',now(),'$posteddata','$db_file_name','b')";
		$result = mysqli_query($db_conx , $sql);
		$last_id = mysqli_insert_id($db_conx);
		foreach($friendslist as $friend){
			$sql3 = "INSERT INTO notifications (initiator , reciever , tagged_to,info_id , type , did_read , date_time) VALUES
									('$log_username','$friend','$u','$last_id','posted an image','0',now())";
			mysqli_query($db_conx , $sql3);
		}
	}
	mysqli_close($db_conx);
	header("location:home.php?u=$u");
}
?>
<?php
//PHP for liking post
if(isset($_GET["likepost"])){
	$likepost = $_GET["likepost"];
	$sql = "SELECT likes, likefriends, poster from posts WHERE post_id = '$likepost' LIMIT 1";
	$result = mysqli_query($db_conx , $sql);
	$row = mysqli_fetch_assoc($result);
	
	$likes = $row["likes"];
	$likefriends = $row["likefriends"];
	$poster = $row["poster"];
	if(strchr("$likefriends","$log_username")=='0'){
		$likes = $likes+1;
		if($likefriends == ""){
			$likefriends .= ''.$log_username.'';	
		}else{
			$likefriends .= ','.$log_username.'';
		}
		//For The Notification
		if($log_username != $poster){
			$sql3 = "INSERT INTO notifications (initiator , reciever , info_id , type , did_read , date_time) VALUES
								('$log_username','$poster','$likepost','liked','0',now())";
			mysqli_query($db_conx,$sql3);
		}
	}else{
		$likes = $likes-1;
		$likefriends = str_replace("$log_username,","","$likefriends");
		$likefriends = str_replace(",$log_username","","$likefriends");
		$likefriends = str_replace("$log_username","","$likefriends");
		//For The Notification
		if($log_username != $poster){
			$sql4 = "DELETE FROM notifications WHERE initiator= '$log_username' AND reciever = '$poster' AND info_id = '$likepost' AND type = 'liked'";
			mysqli_query($db_conx,$sql4);
		}
	}
	
	
	$sql1 = "UPDATE posts
				SET likes='$likes', likefriends='$likefriends'
				WHERE post_id='$likepost'";
	mysqli_query($db_conx , $sql1);
	header("location:home.php?u=$u#$likepost");
}

?>
<?php
//PHP fpr disliking POST 
if(isset($_GET["dislikepost"])){
	$dislikepost = $_GET["dislikepost"];
	$sql = "SELECT dislikes, dislikefriends , poster from posts WHERE post_id = '$dislikepost' LIMIT 1";
	$result = mysqli_query($db_conx , $sql);
	$row = mysqli_fetch_assoc($result);
	
	$dislikes = $row["dislikes"];
	$dislikefriends = $row["dislikefriends"];
	$poster = $row["poster"];
	if(strchr("$dislikefriends","$log_username")=='0'){
		$dislikes = $dislikes+1;
		if($dislikefriends == ""){
			$dislikefriends .= ''.$log_username.'';	
		}else{
			$dislikefriends .= ','.$log_username.'';
		}
		//For The Notification
		if($log_username != $poster){
			$sql3 = "INSERT INTO notifications (initiator , reciever , info_id , type , did_read , date_time) VALUES
								('$log_username','$poster','$dislikepost','disliked','0',now())";
			mysqli_query($db_conx,$sql3);
		}
	}else{
		$dislikes = $dislikes-1;
		$dislikefriends = str_replace("$log_username,","","$dislikefriends");
		$dislikefriends = str_replace(",$log_username","","$dislikefriends");
		$dislikefriends = str_replace("$log_username","","$dislikefriends");
		//For The Notification
		if($log_username != $poster){
			$sql4 = "DELETE FROM notifications WHERE initiator= '$log_username' AND reciever = '$poster' AND info_id = '$dislikepost' AND type = 'disliked'";
			mysqli_query($db_conx,$sql4);
		}
	}
	
	
	$sql1 = "UPDATE posts
				SET dislikes='$dislikes', dislikefriends='$dislikefriends'
				WHERE post_id='$dislikepost'";
	mysqli_query($db_conx , $sql1);
	header("location:home.php?u=$u");
}

?>
<?php
//PHP for commenting
if(isset($_POST["commenting"])){
	$commentis = $_POST["commentis"];
	$commentid = $_GET["comment"];
	if(empty($commentis)){
		echo '<script>alert("Please write something");</script>';
	}else{
		$sql = "INSERT INTO posts_comment (post_id ,commenter ,comment ,comment_time)
							VALUES('$commentid','$log_username','$commentis',now())";
		mysqli_query($db_conx , $sql);
		$last_id = mysqli_insert_id($db_conx);
		if($log_username != $poster){
			$sql2 = "SELECT poster FROM posts WHERE post_id = '$commentid' LIMIT 1";
			$query2 = mysqli_query($db_conx,$sql2);
			$row2 = mysqli_fetch_assoc($query2);
			$poster = $row2["poster"];
			//For The Notification
			if($log_username != $poster){
				$sql1 = "INSERT INTO notifications (initiator , reciever , info_id ,comment_id, type , did_read , date_time) VALUES
									('$log_username','$poster','$commentid','$last_id','commented','0',now())";
				mysqli_query($db_conx,$sql1);
			}
		}
		header("location:home.php?u=$u");
	}
}

?>
<?php

//PHP for delete post
if(isset($_GET["deletepost"])){
	$deletepost = $_GET["deletepost"];
	
	$sql1 ="SELECT poster, postto FROM posts WHERE post_id='$deletepost'";
	
	$query = mysqli_query($db_conx,$sql1);
	
	$row = mysqli_fetch_assoc($query);
	
	if($row["poster"]==$log_username || $row["postto"]==$log_username){
		$sql = "DELETE FROM posts WHERE post_id='$deletepost'";
		mysqli_query($db_conx ,$sql);
		//Delete For The Notification
		foreach($friendslist as $friend){
			$sql3 = "DELETE FROM notifications WHERE initiator = '$log_username' AND reciever = '$friend' AND info_id = '$deletepost'";
			mysqli_query($db_conx , $sql3);
		}
		header("location:home.php?u=$u");	
	}else{
		echo 'You are not eligible to delete this post';	
	}
		
}

?>
<?php
if(isset($_GET["deletecomment"])){
	$deletecomment = $_GET["deletecomment"];
	
	$sql1 ="SELECT commenter ,post_id FROM posts_comment WHERE comment_id='$deletecomment'";
	
	$query = mysqli_query($db_conx,$sql1);
	
	$row = mysqli_fetch_assoc($query);
	$post_id = $row["post_id"];
	if($row["commenter"]==$log_username){
		$sql = "DELETE FROM posts_comment WHERE comment_id='$deletecomment'";
		mysqli_query($db_conx , $sql);
		$sql2 = "SELECT poster FROM posts WHERE post_id = '$post_id' LIMIT 1";
		$query2 = mysqli_query($db_conx,$sql2);
		$row2 = mysqli_fetch_assoc($query2);
		$poster = $row2["poster"];
		//For The Notification
		$sql1 = "DELETE FROM notifications WHERE initiator = '$log_username' AND reciever = '$poster' AND info_id = '$post_id' AND comment_id = '$deletecomment'";
		mysqli_query($db_conx,$sql1);
		header("location:home.php?u=$u");
	}else{
		echo 'You are not eligible to delete this comment';
	}
}

?>
<?php
 if(!isset($_SESSION['username'])) {
    header("location: index.php");
}
?>
<?php
$isFriend = false;  //kya hum log friend hai ?
$isFriendRequestSend = false; //Kya humne usko friend request bheja hai ? 
$ownerBlockViewer = false;  //usne to humko block nhi kiya hai ?
$viewerBlockOwner = false;  //humne to usko block nhi kiya hai ?
if($u != $log_username && $user_ok == true){
	$friend_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='1' OR user1='$u' AND user2='$log_username' AND accepted='1' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $friend_check)) > 0){
        $isFriend = true;
    }
	$friend_request_sent_check = "SELECT id FROM friends WHERE user1='$log_username' AND user2='$u' AND accepted='0' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $friend_request_sent_check)) > 0){
		$isFriendRequestSend = true;
	}
	$block_check1 = "SELECT id FROM blockedusers WHERE blocker='$u' AND blockee='$log_username' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check1)) > 0){
        $ownerBlockViewer = true;
    }
	$block_check2 = "SELECT id FROM blockedusers WHERE blocker='$log_username' AND blockee='$u' LIMIT 1";
	if(mysqli_num_rows(mysqli_query($db_conx, $block_check2)) > 0){
        $viewerBlockOwner = true;
    }
}
?>
<?php 
$friend_button = '<button style="display:none">Request As Friend</button>';
$block_button = '<button style="display:none">Block User</button>';
// LOGIC FOR FRIEND BUTTON
if($isFriend == true){
	$friend_button = '<button onclick="friendToggle(\'unfriend\',\''.$u.'\',\'friendBtn\')">Unfriend</button>';
} else if($isFriendRequestSend == true){
	$friend_button = '<button onclick="friendToggle(\'cancelfriendrequest\',\''.$u.'\',\'friendBtn\')">Cancel friend request</button>';
}else if($user_ok == true && $u != $log_username && $ownerBlockViewer == false){
	$friend_button = '<button onclick="friendToggle(\'friend\',\''.$u.'\',\'friendBtn\')">Request As Friend</button>';
}else if($user_ok == true && $u != $log_username && $ownerBlockViewer == true){
	$friend_button = '<button disabled>Request As Friend</button>';
}
// LOGIC FOR BLOCK BUTTON
if($viewerBlockOwner == true){
	$block_button = '<button onclick="blockToggle(\'unblock\',\''.$u.'\',\'blockBtn\')">Unblock User</button>';
} else if($user_ok == true && $u != $log_username){
	$block_button = '<button onclick="blockToggle(\'block\',\''.$u.'\',\'blockBtn\')">Block User</button>';
}
?>
<?php
$friendsHTML = '';
$friends_view_all_link = 'Friends (0)';
$sql = "SELECT COUNT(id) FROM friends WHERE user1='$u' AND accepted='1' OR user2='$u' AND accepted='1'";
$query = mysqli_query($db_conx, $sql);
$query_count = mysqli_fetch_row($query);
$friend_count = $query_count[0];
if($friend_count < 1){
	$friendsHTML = $u." has no friends yet";
} else {
	$max = 8;
	$all_friends = array();
	$sql = "SELECT user1 FROM friends WHERE user2='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user1"]);
	}
	$sql = "SELECT user2 FROM friends WHERE user1='$u' AND accepted='1' ORDER BY RAND() LIMIT $max";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		array_push($all_friends, $row["user2"]);
	}
	$friendArrayCount = count($all_friends);
	if($friendArrayCount > $max){
		array_splice($all_friends, $max);
	}
	
	$friends_view_all_link = 'Friends ('.$friend_count.')';
	if($friend_count > $max){
		$friends_view_all_link = '<a href="view_friends.php?u='.$u.'">Friends ('.$friend_count.')</a>';
	}
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
		$friendsHTML .= '<div class="showfriends"><a href="home.php?u='.$friend_username.'"><img class="friendpics" src="'.$friend_pic.'" alt="'.$friend_username.'" title="'.$friend_username.'"></a><div class="friendname"><a href="home.php?u='.$friend_username.'">'.$friend_firstname.'<br>'.$friend_lastname.'</a></div></div>';
	}
}
?>
<?php 
$coverpic = "";
$sql = "SELECT filename FROM photos WHERE user='$u' ORDER BY RAND() LIMIT 1";
$query = mysqli_query($db_conx, $sql);
if(mysqli_num_rows($query) > 0){
	$row = mysqli_fetch_row($query);
	$filename = $row[0];
	$coverpic = '<img src="user/'.$u.'/'.$filename.'" alt="pic" width="100px" height="100px">';
}
?>

<?php

$friends = array();

$myfriend = "";

$posts = "";

$sql1 = "SELECT user2 FROM friends WHERE user1='$u' AND accepted ='1'";

$result1 = mysqli_query($db_conx,$sql1);

while($row1 = mysqli_fetch_assoc($result1)){

array_push($friends , $row1["user2"]);
	
}

$sql2 = "SELECT user1 FROM friends WHERE user2='$u' AND accepted ='1'";

$result2 = mysqli_query($db_conx,$sql2);

while($row2 = mysqli_fetch_assoc($result2)){

array_push($friends , $row2["user1"]);

}

array_push($friends , $u);

foreach($friends as $friend){

$myfriend .= 'poster = "'.$friend.'" OR postto = "'.$friend.'" OR '; 

}
$myfriend = substr_replace("$myfriend","",-3);

$sql = "SELECT * FROM posts WHERE $myfriend ORDER BY post_time DESC";

$query = mysqli_query($db_conx, $sql);

while($row = mysqli_fetch_assoc($query)){
	$poster = $row["poster"];
	$postto = $row["postto"];
	$posts .= '<div class = "posts" id="'.$row["post_id"].'">';	//posts
	$posts .= '<div id="post">';	//post
	$posts .= '<div id = "image">';		//images
		$query1 = "SELECT f_name , l_name , avatar , u_name FROM user WHERE u_name = '$poster' LIMIT 1";
		$result = mysqli_query($db_conx,$query1);
		$r = mysqli_fetch_assoc($result);
		if($r["avatar"] != ""){
			$pic = 'user/'.$r["u_name"].'/profilepic/'.$r["avatar"].'';  
		} else {
			$pic = 'images/default_pic.jpg';
		}
	$posts .= '<img src="'.$pic.'" width="80px" height="80px">';
	
	$posts .= '</div>';		//images
	
	
	
	$posts .= '<div id="poster_name">';	//poster_name
	
	$posts .='<span><a href="home.php?u='.$row["poster"].'">'.$r["f_name"].' '.$r["l_name"].'</a></span>&nbsp;';
	
	if($row["type"] == 'a'){
	$posts .='has posted in timeline';
	}else{
		$q1 = mysqli_query($db_conx,"SELECT f_name,l_name from user WHERE u_name = '$postto' LIMIT 1");
		$r1 = mysqli_fetch_assoc($q1);
	$posts .='has posted in <a href="home.php?u='.$row["postto"].'">'.$r1["f_name"].' '.$r1["l_name"].'&prime;s</a> timeline';
	}
	$posts .= '</div>';			//poster_name
	
	if($row["poster"]==$log_username||$row["postto"]==$log_username){
	$posts .= '<div id = "close">';		//close
	$posts .='<a href="home.php?u='.$u.'&amp;deletepost='.$row["post_id"].'"><img src="images/close.png" width="21px" height="25px"></a>';
	$posts .='</div>';		//close
	}
	
	$posts .='<div id = "posttime">'.$row["post_time"].'</div>';
	$posts .='<div id = "actualpost">';	//actualpost
	
	if($row["post"] != ""){
	$posts .='<div id = "posttext">'.$row["post"].'</div>'; //posttext //posttext
	}
	if($row["post_image"] != ""){
	$postedimage = 'posted_images/'.$row["post_image"].'';
	
	$posts .='<div id = "postimage"><img src="'.$postedimage.'" width="300px" height="300px"></div>'; //posttmage //postimage
	}
	$posts .= '</div>';		//actualpost
	
	$posts .= '</div>';		//post
	
	$posts .= '<div class="ldc">'; //ldc
	$friendsliked = $row["likefriends"];
	$friendsdisliked = $row["dislikefriends"];
	if(strchr("$friendsliked","$log_username")=='0'){ 	//this gives that is the logged user has liked that posts or not ..... 
	$posts .= '<a href = "home.php?u='.$u.'&amp;likepost='.$row["post_id"].'"><img src="images/like.jpg" width="25px" height="25px"></a><span><a href="home.php?u='.$u.'&amp;showlikefriends='.$row["post_id"].'">Like('.$row["likes"].')</a></span>';
	}else{
	$posts .= '<a href = "home.php?u='.$u.'&amp;likepost='.$row["post_id"].'"><img src="images/like2.jpg" width="25px" height="25px"></a><span><a href="home.php?u='.$u.'&amp;showlikefriends='.$row["post_id"].'">Like('.$row["likes"].')</a></span>';	
	}
	if(strchr("$friendsdisliked","$log_username")=='0'){
	$posts .= '<a href = "home.php?u='.$u.'&amp;dislikepost='.$row["post_id"].'"><img src="images/dislike.jpg" width="25px" height="25px"></a><span><a href="home.php?u='.$u.'&amp;showdislikefriends='.$row["post_id"].'">Dislike('.$row["dislikes"].')</a></span>';
	}else{
	$posts .= '<a href = "home.php?u='.$u.'&amp;dislikepost='.$row["post_id"].'"><img src="images/dislike2.jpg" width="25px" height="25px"></a><span><a href="home.php?u='.$u.'&amp;showdislikefriends='.$row["post_id"].'">Dislike('.$row["dislikes"].')</span></a>';	
	}
	
	
	$post_id =$row["post_id"];
	$sql0 = "SELECT * FROM posts_comment WHERE post_id = '$post_id' ORDER BY comment_time DESC";
	
	$query0=mysqli_query($db_conx,$sql0);
	$row_count0 = mysqli_num_rows($query0);
	$posts .= '&nbsp;<a href = "home.php?u='.$u.'&amp;commentpost='.$row["post_id"].'"><span><img src="images/comment.jpg" width="25px" height="25px" style="position:relative; top:7px;">&nbsp;Comment('.$row_count0.')</span></a>';
	$posts .= '</div>';			//ldc
	$posts .= '<div id="commentreply"><form action="home.php?u='.$u.'&amp;comment='.$post_id.'" method="POST"><textarea name ="commentis"></textarea><br><input type="submit" value="Comment" name="commenting"></form></div>';
	
	while($row0=mysqli_fetch_assoc($query0)){	
	$posts .='<div id="comments">'; //comments
	$commenteruname = $row0["commenter"];
	
	$sql9 = "SELECT f_name,l_name,u_name,avatar from user WHERE u_name='$commenteruname' LIMIT 1";
	$query9=mysqli_query($db_conx, $sql9);
	$row9 = mysqli_fetch_assoc($query9);
	
	if($row9["avatar"] != ""){
		$commenterpic = 'user/'.$row9["u_name"].'/profilepic/'.$row9["avatar"].'';  
	} else {
		$commenterpic = 'images/default_pic.jpg';
	}
		
	$posts .='<div id="commenter_pic"><img src="'.$commenterpic.'" width="50px" height="50px"></div>';
	$posts .='<div id="commenter_name">'.$row9["f_name"].' '.$row9["l_name"].'</div>';
	if($commenteruname == $log_username || ($commenteruname == $row["poster"] && $commenteruname == $log_username)){
	$posts .= '<div id = "deletecomment">';		//deletecomment
	$posts .='<a href="home.php?u='.$u.'&amp;deletecomment='.$row0["comment_id"].'"><img src="images/close.png" width="18px" height="16px"></a>';
	$posts .='</div>'; //deletecomment
	}
	$posts .='<div id="commenter_time">'.$row0["comment_time"].'</div>';
	
	
	$posts .='<div id="commenter_comment">'.$row0["comment"].'</div>';
	
	$posts .='</div>';	//comments		
	
	}
	
	$posts .= '</div>';		//posts

}
?>

<?php
$status = '';

$sql = "SELECT * FROM status WHERE status_user = '$u' ORDER BY id DESC LIMIT 1";
$query = mysqli_query($db_conx,$sql);
while($row = mysqli_fetch_assoc($query)){	
 $status = $row["statusdata"];
}
?>

<!--Retrive information for About me-->
<?php
$fav_color = '';
$fav_film = '';
$best_friend = '';
$married_status = '';
$feild_of_interest = '';

$sql = "SELECT * FROM useroptions WHERE username = '$u' LIMIT 1";

$query = mysqli_query($db_conx,$sql);
while($row=mysqli_fetch_assoc($query)){
	$fav_color=$row["fav_color"];
	$fav_film = $row["fav_film"];
	$best_friend = $row["best_friend"];
	$married_status = $row['married_status'];
	$field_of_interest = $row['field_of_interest'];
	$fav_songs = $row["fav_songs"];
}
?>
<!DOCTYPE Html>
<html>
	<head>
    	<meta charset="utf-8">
		<title>Friend Buzz</title>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <style type="text/css">
		img.friendpics{border:#000 1px solid; width:100px; height:100px; margin:2px;}
		#leftcontent{
			float:left;
		}
		#rightcontent{
			float:left;
			margin-left:63px;
			margin-right:63px;
			margin-top:55px;	
		}
		
		.posts{
			width:580px;
			height:auto;
			float:left;
			margin-top:33px;
			margin-left:63px;
			padding-bottom:10px;
		}
		
		#image{
			border:1px solid #AAA;
			width:80px;
			height:80px;
			border-radius:50%;
			overflow:hidden;
			margin-top:10px;
			margin-left:5px;
			float:left;
		}
		
		#post{
			float:left;
			background-color:#FFF;
			width:573px;
			height:auto;
			position:relative;
			right:7px;
			margin-left:10px;
			margin-top:10px;
			padding-bottom:10px;
		}
		#poster_name{
			float:left;
			margin-top:25px;
			margin-left:15px;
			font-weight:bold;
			width:400px;
			max-width:410px; 	
		}
		#poster_name span{
			font-weight:bold;
			font-family:Georgia, "Times New Roman", Times, serif;
			font-size:24px;
		}
		#poster_name a{
			text-decoration:none;	
		}
		
		#close{
		 	float:right;
			margin-top:25px;
			margin-right:5px;
			width:21px;
			height:25px;	
		}
		#close img{
			float:right;	
		}
		#posttime{
			float:left;
			margin-left:15px;
			font-size:12px;
		}
		#posttext{
			width:400px;
			float:left;
			margin-left:102px;
			margin-top:10px;
			clear:both;
			word-wrap:break-word;
			font-family:Tahoma, Geneva, sans-serif;	
		}
		#postimage{
			width:400px;
			float:left;
			margin-left:104px;
			margin-top:10px;
			clear:both;
		}
		.ldc{
			background-color:#FFF;
			float:right;
			position:relative;
			right:4px;
			width:573px;
			margin-top:5px;	
		}
		.ldc a{
		 margin-right:5px;
		 color:blue;
		 position:relative;
		 text-decoration:none;	
		}
		.ldc img{
			position:relative;
			top:2px;	
		}
		.ldc span{
			position:relative;
			bottom:5px;
			cursor:pointer;
			color:blue;	
		}
		#commentreply{
			float:right;
			position:relative;
			right:4px;
			width:573px;	
		}
		#commentreply textarea{
			height:60px;
			width:573px;	
		}
		#commentreply input{
			float:right;
			margin-bottom:6px;
		}
		#comments{
			border:1px solid #AAA;
			background-color:rgb(154, 210, 126);
			float:right;
			width:573px;	
		}
		#commenter_pic{
			height:50px;
			width:50px;
			float:left;
		}
		#commenter_name{
			margin-top:1px;
			font-weight:bold;
			margin-left:5px;
			float:left;
			width:395px;
		}
		#commenter_time{
			float:left;
			width:395px;
			margin-top:1px;
			margin-left:5px;
			font-size:11px;
		}
		#commenter_comment{
			float:left;
			margin-top:18px;
			width:520px;
		}
		#deletecomment{
			float:right;	
		}
		#posteddata{
			float:left;
			width:690px;	
		}
		#boxes{
			width:200px;
			float:left;			
			width:275px;
			margin-left:11px;
			margin-bottom:5px;
			cursor:pointer;
		}
		#pic{
			float:left;
		}
		#pic img{
			float:left;
		}
		#name{	
			font-weight:bold;
			width:275px;
			margin-top:2px;
		}
		#country{
			margin-top:5px;	
		}
		#friend{
			margin-top:5px;
			margin-bottom:4px;
		}
		#friend button{
			font-weight:bold;
			padding:2px;
		}
		button{
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
		#openedpost{
			display:none;
			position:fixed;
			background-color:#AAA;
			width:500px;
			height:220px;	
			z-index:10;
			border:1px solid #AAA;
		}
		#openedpost hr{
			margin-bottom:5px;
		}
		#openedpost > div{
			margin:8px;
		}
		#postarea textarea{
			width:480px;
		}
		#openedpostboxhead{
			font-size:30px;
			font-family:"Comic Sans MS", cursive;	
		}
		#postboxbody span{
			font-family:Tahoma, Geneva, sans-serif;
			font-size:18px;
			cursor:pointer;
			position:relative;
			top:2px;
			margin-bottom:10px;
			padding-left:15px;
			padding-right:15px;	
		}
		#text{
			margin-left:1px;
			background:#FFF;	
		}
		</style>
        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script src="js/expand_retract.js"></script>
        <script src="js/autoscroll.js"></script>
        <script src="js/fadeeffect.js"></script>
        <script type="text/javascript">
			function friendToggle(type,user,elem){
				var conf = confirm("Press OK to confirm the '"+type+"' action for user <?php echo $u; ?>.");
				if(conf != true){
					return false;
				}
				_(elem).innerHTML = '<button>Please Wait....</button>';
				var ajax = ajaxObj("POST", "php_parsers/friend_system.php");
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						if(ajax.responseText == "friend_request_sent"){
							_(elem).innerHTML = '<button onclick="friendToggle(\'cancelfriendrequest\',\'<?php echo $u; ?>\',\'friendBtn\')">Cancel friend request</button>';
						} else if(ajax.responseText == "unfriend_ok"){
							_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
						} else if(ajax.responseText=="friendrequestcancelled"){
							_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';
						}else {
							alert(ajax.responseText);
							_(elem).innerHTML = '<button onclick="friendToggle(\'friend\',\'<?php echo $u; ?>\',\'friendBtn\')">Request As Friend</button>';	
						}
					}
				}
				ajax.send("type="+type+"&user="+user);
			}
			function blockToggle(type,blockee,elem){
				var conf = confirm("Press OK to confirm the '"+type+"' action on user <?php echo $u; ?>.");
				if(conf != true){
					return false;
				}
				_(elem).innerHTML = '<button>Please Wait....</button>';
				var ajax = ajaxObj("POST", "php_parsers/block_system.php");
				ajax.onreadystatechange = function() {
					if(ajaxReturn(ajax) == true) {
						if(ajax.responseText == "blocked_ok"){
							_(elem).innerHTML = '<button onclick="blockToggle(\'unblock\',\'<?php echo $u; ?>\',\'blockBtn\')">Unblock User</button>';
						} else if(ajax.responseText == "unblocked_ok"){
							_(elem).innerHTML = '<button onclick="blockToggle(\'block\',\'<?php echo $u; ?>\',\'blockBtn\')">Block User</button>';
						} else {
							alert(ajax.responseText);
							_(elem).innerHTML = 'Try again later';
						}
					}
				}
				ajax.send("type="+type+"&blockee="+blockee);
			}
		</script>
       	<script>
			function CustomLogin(){
				this.render = function(){
					var winW = window.innerWidth;
					var winH = window.innerHeight;
					var dialogoverlay = document.getElementById('dialogoverlay');
					var openedpost = document.getElementById('openedpost');
					dialogoverlay.style.display="block";
					dialogoverlay.style.height=winH+"px";
					openedpost.style.left = (winW/2)-(450 * .5)+"px";
					openedpost.style.display = "block";
					openedpost.style.top = "100px";
				}
				this.cancel=function(){
					document.getElementById('openedpost').style.display = "none";
					document.getElementById('dialogoverlay').style.display = "none";
					_('showlikefriends').style.display = "none";
				}
			}	
			var Alert = new CustomLogin();	
		</script>

		<script>
			function taketotext(){
				var postarea = _('postarea');
				var text = _('text');
				var photo = _('photo');
				_("photo").style.background = "none";
				_("text").style.background = "#FFF";
				postarea.innerHTML = '<form action="" method="post"><textarea name="posteddata" required></textarea><input type="submit" value="Post" name="post"></form>';
			}
			function taketophoto(){
				var postarea = _('postarea');
				var text = _('text');
				var photo = _('photo');
				_("text").style.background = "none";
				_("photo").style.background = "#FFF";
				postarea.innerHTML = '<form action="" method="post" enctype="multipart/form-data"><textarea name="posteddata" required></textarea> <input type="file" name="postimage" ><input type="submit" name="postwithimage" value="Post"></form>';
			}
        </script>
        <script>
			function closeoverlay(){
				document.getElementById('showlikefriends').style.display = "none";
				document.getElementById('dialogoverlay').style.display = "none";
				window.location = "home.php?=<?php echo $u; ?>";	
			}
        </script>

	</head>
<body>
<?php 
include("includes/header.php");
?>
    <div id="page_middle">
    	<div id="page_middle_content">
        <table id="leftcontent">
        	<tr>
            	<td>
        			<div id="profilepicbox">
            			<div id="timelinepic">
                        	<?php echo $timeline_pic_btn; ?><?php echo $timeline_form; ?><?php echo $timeline_pic; ?>
                		</div>
                		<div id="profilepic">
                			<?php echo $profile_pic_btn; ?><?php echo $avatar_form; ?><?php echo $profile_pic; ?>
                		</div>
                		<div id="profilename">
                			<span><?php echo $profile_fn.' '.$profile_ln; ?></span><br>
                    		<?php echo $u; ?><br>
                    		<div>Join date : <?php echo $joindate; ?></div>
                    		<div>Last login : <?php echo $lastsession; ?></div>
                		</div>
                		<div id="frienddecision">
                			<span id="friendBtn"><?php echo $friend_button; ?></span>
                    		<span id="blockBtn"><?php echo $block_button; ?></span>
                            <?php
								if($log_username!==$u){
									echo '<span> <button onclick="window.location=\'messages.php?u='.$u.'\'">Send Message</button></span> ';
								}
							?>
                		</div>
       	  			</div>
            	</td>
     		</tr>
            <tr>
            	<td>
                	<div id="friendlistbox">
                    	<div id="friendlisthead">
                        	Status
                        </div>
                        <div id="friendlistbody" style="padding-bottom:10px; font-weight:bold">
                        	<?php  echo $status; ?>
                        </div>
                    </div>
                </td>
        	</tr>
            <tr>
            	<td>
                	<div id="friendlistbox">
                    	<div id="friendlisthead">
                        	<?php echo $friends_view_all_link; ?>
                        </div>
                        <div id="friendlistbody">
                        	<?php echo $friendsHTML; ?>
                        </div>
                    </div>
                    
                </td>
          	</tr>
            <tr>
            	<td>
                	<div id="friendlistbox">
                    	<div id="friendlisthead">
                        	About Me
                        </div>
                        <div id="friendlistbody">
                        	<table border="0">
                            	<tr><td id="dobold">Name:</td><td><?php echo $profile_fn.' '.$profile_ln; ?></td>
                                	<td rowspan="6">
                                    	<div id="photoframebox">
                                    		<div id="photoframe" onclick="window.location = 'photos.php?u=<?php echo $u; ?>';" title="view <?php echo $u; ?>&#39;s photo galleries"> <?php echo $coverpic; ?>
                                            	
                                            </div>	
                                    	</div>
                                  	</td>
                                </tr>
                                <tr><td id="dobold">Date of Birth:</td><td><?php echo $dt_of_birth; ?></td><td></td></tr>
                                <tr><td id="dobold">Favourite Color:</td><td><?php echo $fav_color;   ?></td><td></td></tr>
                                <tr><td id="dobold">Favourite Film:</td><td><?php echo $fav_film;  ?></td><td></td></tr>
                                <tr><td id="dobold">Best Friend:</td><td><?php echo $best_friend; ?></td><td></td></tr>
                                <tr><td id="dobold">Married Status</td><td><?php echo $married_status; ?></td><td></td></tr>
                                <tr><td id="dobold">Field Of Interest</td><td><?php echo $field_of_interest; ?></td><td></td></tr>
                                <tr><td id="dobold">Favourite Songs</td><td><?php echo $fav_songs; ?></td><td></td></tr>
                        	</table>
                        </div>
                    </div>
                </td>
          	</tr>
  		</table>
            <!--Starting of Post system-->
        <div id="posteddata">	<!--posteddata-->
        <table id="rightcontent">
        	<tr>
            	<td>
                	<div id="postbox">
            			<div id="postboxhead">
                			Post
                		</div><hr>
            			<div id="posttextarea">
                			<textarea placeholder="Whats on your mind" onFocus="Alert.render()"></textarea>
                		</div>
            		</div>
            	</td>
         	</tr>
    	</table>
            <!--End of Post Box-->
            <?php
             	echo $posts;
			?>
            </div>      	<!--posteddata-->
      	</div>
    </div>


 <div id="dialogoverlay" onClick="Alert.cancel()"></div>
        <div id="openedpost">
        	<div>
            	<div id="openedpostboxhead">Post</div>
                <hr>
                <div id="postboxbody">
                	<span onClick="taketotext()" id="text">Text</span><span onClick="taketophoto()" id="photo">Photo</span>
    				<div id="postarea"><form action="" method="post"><textarea name="posteddata"></textarea><input type="submit" value="Post" name="post"></form></div>
                </div>
          	</div>
     	</div>
   	<!---------end of the first overlay--------------->
    	<div id="showlikefriends">
        <h2>People Who LIKE This STATUS<img src="images/close.png" onClick="closeoverlay()"></h2>
        	<?php
if(isset($_GET["showlikefriends"])){
	$id = $_GET["showlikefriends"];
	$sql = "SELECT likefriends FROM posts WHERE post_id='$id'";
	$query = mysqli_query($db_conx , $sql);
	$row = mysqli_fetch_assoc($query);
	$likedfriends = $row["likefriends"];
	$friendsliked = array();
	
	$friendsliked = explode(",",$likedfriends);
	$likefriends = '';
	foreach($friendsliked as $friendslike){
		$likefriends .= 'u_name="'.$friendslike.'" OR ';
	}
	$likefriends = substr_replace("$likefriends","",-3);
	
	$showfriends = '';
	
	$sql9 = "SELECT u_name, f_name, l_name, avatar, u_country FROM user WHERE $likefriends";
	$result = mysqli_query($db_conx,$sql9);
	while($row9 = mysqli_fetch_assoc($result)){
		$u_name = $row9["u_name"];
		$f_name = $row9["f_name"];
		$l_name = $row9["l_name"];
		
		if($row9["avatar"] == ""){
			$pics = 'images/default_pic.jpg';	
		}else{
			$pics = 'user/'.$u_name.'/profilepic/'.$row9["avatar"].'';	
		}
		
		$u_country = $row9["u_country"];
		$sql1 = "SELECT id FROM friends WHERE (user1='$u_name' AND user2='$log_username' AND accepted ='1') OR (user2='$u_name' AND user1='$log_username' AND accepted ='1') LIMIT 1";
		$query1=mysqli_query($db_conx, $sql1);
		$row_conut = mysqli_num_rows($query1);
		if($row_conut == 0){
			if($row9["u_name"]==$log_username){
				$button = '';
			}else{
			$button = 'Add as Friend';
			}
		}else{
			$button = 'Friends';
		}
		
		$showfriends .= '<div id = "boxes" onclick="window.location=\'home.php?u='.$u_name.'\'">';
		$showfriends .= '<div id = "pic"><img src="'.$pics.'" width="70px" height="70px"></div>';
		$showfriends .= '<div id = "name">&nbsp;'.$f_name.' '.$l_name.'</div>';
		$showfriends .= '<div id = "country">&nbsp;'.$u_country.'</div>';
		if($u_name==$log_username){
			$showfriends .= '<div id = "friend"></div>';
		}else{
			$showfriends .= '<div id = "friend">&nbsp;<button>'.$button.'</button></div>';
		}
		$showfriends .= '</div>';
	}
	echo "<script>
			var winW = window.innerWidth;
			var winH = window.innerHeight;
			document.getElementById('dialogoverlay').style.display='block';
			document.getElementById('dialogoverlay').style.height=winH+'px';	
			document.getElementById('showlikefriends').style.left = (winW/2)-(600 * .5)+'px';
			document.getElementById('showlikefriends').style.display = 'block';
			document.getElementById('showlikefriends').style.top = '100px';	
			</script>";
	echo $showfriends;
}
?>
<?php
if(isset($_GET["showdislikefriends"])){
	$id = $_GET["showdislikefriends"];
	$sql = "SELECT dislikefriends FROM posts WHERE post_id='$id'";
	$query = mysqli_query($db_conx , $sql);
	$row = mysqli_fetch_assoc($query);
	$likedfriends = $row["dislikefriends"];
	$friendsliked = array();
	
	$friendsliked = explode(",",$likedfriends);
	$likefriends = '';
	foreach($friendsliked as $friendslike){
		$likefriends .= 'u_name="'.$friendslike.'" OR ';
	}
	$likefriends = substr_replace("$likefriends","",-3);
	
	$showfriends = '';
	
	$sql9 = "SELECT u_name, f_name, l_name, avatar, u_country FROM user WHERE $likefriends";
	$result = mysqli_query($db_conx,$sql9);
	while($row9 = mysqli_fetch_assoc($result)){
		$u_name = $row9["u_name"];
		$f_name = $row9["f_name"];
		$l_name = $row9["l_name"];
		
		if($row9["avatar"] == ""){
			$pics = 'images/default_pic.jpg';	
		}else{
			$pics = 'user/'.$u_name.'/profilepic/'.$row9["avatar"].'';	
		}
		
		$u_country = $row9["u_country"];
		$sql1 = "SELECT id FROM friends WHERE (user1='$u_name' AND user2='$log_username' AND accepted ='1') OR (user2='$u_name' AND user1='$log_username' AND accepted ='1') LIMIT 1";
		$query1=mysqli_query($db_conx, $sql1);
		$row_conut = mysqli_num_rows($query1);
		if($row_conut == 0){
			if($row9["u_name"]==$log_username){
				$button = '';
			}else{
			$button = 'Add as Friend';
			}
		}else{
			$button = 'Friends';
		}
		
		$showfriends .= '<div id = "boxes" onclick="window.location=\'home.php?u='.$u_name.'\'">';
		$showfriends .= '<div id = "pic"><img src="'.$pics.'" width="70px" height="70px"></div>';
		$showfriends .= '<div id = "name">&nbsp;'.$f_name.' '.$l_name.'</div>';
		$showfriends .= '<div id = "country">&nbsp;'.$u_country.'</div>';
		if($u_name==$log_username){
			$showfriends .= '<div id = "friend"></div>';
		}else{
			$showfriends .= '<div id = "friend">&nbsp;<button>'.$button.'</button></div>';
		}
		$showfriends .= '</div>';
	}
	echo "<script>
			var winW = window.innerWidth;
			var winH = window.innerHeight;
			document.getElementById('dialogoverlay').style.display='block';
			document.getElementById('dialogoverlay').style.height=winH+'px';	
			document.getElementById('showlikefriends').style.left = (winW/2)-(600 * .5)+'px';
			document.getElementById('showlikefriends').style.display = 'block';
			document.getElementById('showlikefriends').style.top = '100px';	
			</script>";
	echo $showfriends;
}
?>
        </div>
