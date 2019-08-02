<?php
include_once("../includes/check_login_status.php");
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
if (isset($_POST["show"]) && $_POST["show"] == "galpics"){
	$picstring = "";
	$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["gallery"]);
	$user = preg_replace('#[^a-z0-9]#i', '', $_POST["user"]);
	$sql = "SELECT * FROM photos WHERE user='$user' AND gallery='$gallery' ORDER BY uploaddate ASC";
	$query = mysqli_query($db_conx, $sql);
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$id = $row["id"];
		$filename = $row["filename"];
		$description = $row["description"];
		$uploaddate = $row["uploaddate"];
		$picstring .= "$id|$filename|$description|$uploaddate|||";
    }
	mysqli_close($db_conx);
	$picstring = trim($picstring, "|||");
	echo $picstring;
	exit();
}
?><?php
include_once("../includes/check_login_status.php");
if($user_ok != true || $log_username == "") {
	exit();
}
?><?php 
if (isset($_FILES["avatar"]["name"]) && $_FILES["avatar"]["tmp_name"] != ""){
	$fileName = $_FILES["avatar"]["name"];
    $fileTmpLoc = $_FILES["avatar"]["tmp_name"];
	$fileType = $_FILES["avatar"]["type"];
	$fileSize = $_FILES["avatar"]["size"];
	$fileErrorMsg = $_FILES["avatar"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	$db_file_name = rand(100000000000,999999999999).".".$fileExt;
	if($fileSize > 2621440) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 2.5mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png or jpeg type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$sql = "SELECT avatar FROM user WHERE u_name='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$avatar = $row[0];
	if($avatar != ""){
		$picurl = "../user/$log_username/profilepic/$avatar"; 
	    if (file_exists($picurl)) { unlink($picurl); }
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/profilepic/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../includes/image_resize.php");
	$target_file = "../user/$log_username/profilepic/$db_file_name";
	$resized_file = "../user/$log_username/profilepic/$db_file_name";
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	$sql = "UPDATE user SET avatar='$db_file_name' WHERE u_name='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	foreach($friendslist as $friends){
		$sql1 = "INSERT INTO notifications (initiator, reciever, type, date_time) VALUES ('$log_username','$friends','profile picture',now())";
		mysqli_query($db_conx , $sql1);
	}
	mysqli_close($db_conx);
	header("location: ../home.php?u=$log_username");
	exit();
}
?>
<?php 
if (isset($_FILES["timeline"]["name"]) && $_FILES["timeline"]["tmp_name"] != ""){
	$fileName = $_FILES["timeline"]["name"];
    $fileTmpLoc = $_FILES["timeline"]["tmp_name"];
	$fileType = $_FILES["timeline"]["type"];
	$fileSize = $_FILES["timeline"]["size"];
	$fileErrorMsg = $_FILES["timeline"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	$db_file_name = rand(100000000000,999999999999).".".$fileExt;
	if($fileSize > 2621440) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 2.5mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png or jpeg type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$sql = "SELECT timeline FROM user WHERE u_name='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	$timeline = $row[0];
	if($timeline != ""){
		$picurl = "../user/$log_username/timelinepic/$timeline"; 
	    if (file_exists($picurl)) { unlink($picurl); }
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/timelinepic/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../includes/image_resize.php");
	$target_file = "../user/$log_username/timelinepic/$db_file_name";
	$resized_file = "../user/$log_username/timelinepic/$db_file_name";
	$wmax = 200;
	$hmax = 300;
	img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	$sql = "UPDATE user SET timeline='$db_file_name' WHERE u_name='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	foreach($friendslist as $friends){
		$sql1 = "INSERT INTO notifications (initiator, reciever, type, date_time) VALUES ('$log_username','$friends','timeline pic',now())";
		mysqli_query($db_conx , $sql1);
	}
	mysqli_close($db_conx);
	header("location: ../home.php?u=$log_username");
	exit();
}
?>
<?php 
if (isset($_FILES["photo"]["name"]) && isset($_POST["gallery"])){
	$sql = "SELECT COUNT(id) FROM photos WHERE user='$log_username'";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0] > 14){
		header("location: ../message.php?msg=The demo system allows only 15 pictures total");
        exit();	
	}
	$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["gallery"]);
	$fileName = $_FILES["photo"]["name"];
    $fileTmpLoc = $_FILES["photo"]["tmp_name"];
	$fileType = $_FILES["photo"]["type"];
	$fileSize = $_FILES["photo"]["size"];
	$fileErrorMsg = $_FILES["photo"]["error"];
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	$db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt; // WedFeb272120452013RAND.jpg
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	if($fileSize > 1048576) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 1mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif, png, jpeg type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../includes/image_resize.php");
	$wmax = 800;
	$hmax = 600;
	if($width > $wmax || $height > $hmax){
		$target_file = "../user/$log_username/$db_file_name";
	    $resized_file = "../user/$log_username/$db_file_name";
		img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	}
	$sql = "INSERT INTO photos(user, gallery, filename, uploaddate) VALUES ('$log_username','$gallery','$db_file_name',now())";
	$query = mysqli_query($db_conx, $sql);
	foreach($friendslist as $friends){
		$sql1 = "INSERT INTO notifications (initiator, reciever, type, date_time) VALUES ('$log_username','$friends','gallery',now())";
		mysqli_query($db_conx , $sql1);
	}
	mysqli_close($db_conx);
	header("location: ../photos.php?u=$log_username");
	exit();
}
?><?php 
if (isset($_POST["delete"]) && $_POST["id"] != ""){
	$id = preg_replace('#[^0-9]#', '', $_POST["id"]);
	$query = mysqli_query($db_conx, "SELECT user, filename FROM photos WHERE id='$id' LIMIT 1");
	$row = mysqli_fetch_row($query);
    $user = $row[0];
	$filename = $row[1];
	if($user == $log_username){
		$picurl = "../user/$log_username/$filename"; 
	    if (file_exists($picurl)) {
			unlink($picurl);
			$sql = "DELETE FROM photos WHERE id='$id' LIMIT 1";
	        $query = mysqli_query($db_conx, $sql);
		}
	}
	mysqli_close($db_conx);
	echo "deleted_ok";
	exit();
}
?>
<?php 
if (isset($_FILES["photoname"]["name"]) && isset($_POST["newgalleryname"])){
	$sql = "SELECT COUNT(id) FROM photos WHERE user='$log_username'";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0] > 14){
		header("location: ../message.php?msg=This website allows only 15 pictures total");
        exit();	
	}
	$gallery = preg_replace('#[^a-z 0-9,]#i', '', $_POST["newgalleryname"]);
	$fileName = $_FILES["photoname"]["name"];
    $fileTmpLoc = $_FILES["photoname"]["tmp_name"];
	$fileType = $_FILES["photoname"]["type"];
	$fileSize = $_FILES["photoname"]["size"];
	$fileErrorMsg = $_FILES["photoname"]["error"];
	$sql = "SELECT COUNT(id) FROM photos WHERE user='$log_username' AND gallery='$gallery'";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_row($query);
	if($row[0]>0){
		header("location: ../message.php?msg=You Have have gallery with this name");
		exit();
	}
	$kaboom = explode(".", $fileName);
	$fileExt = end($kaboom);
	$db_file_name = date("DMjGisY")."".rand(1000,9999).".".$fileExt; // WedFeb272120452013RAND.jpg
	list($width, $height) = getimagesize($fileTmpLoc);
	if($width < 10 || $height < 10){
		header("location: ../message.php?msg=ERROR: That image has no dimensions");
        exit();	
	}
	if($fileSize > 2621440) {
		header("location: ../message.php?msg=ERROR: Your image file was larger than 2.5mb");
		exit();	
	} else if (!preg_match("/\.(gif|jpg|png|jpeg)$/i", $fileName) ) {
		header("location: ../message.php?msg=ERROR: Your image file was not jpg, gif or png or jpeg type");
		exit();
	} else if ($fileErrorMsg == 1) {
		header("location: ../message.php?msg=ERROR: An unknown error occurred");
		exit();
	}
	$moveResult = move_uploaded_file($fileTmpLoc, "../user/$log_username/$db_file_name");
	if ($moveResult != true) {
		header("location: ../message.php?msg=ERROR: File upload failed");
		exit();
	}
	include_once("../includes/image_resize.php");
	$wmax = 800;
	$hmax = 600;
	if($width > $wmax || $height > $hmax){
		$target_file = "../user/$log_username/$db_file_name";
	    $resized_file = "../user/$log_username/$db_file_name";
		img_resize($target_file, $resized_file, $wmax, $hmax, $fileExt);
	}
	$sql = "INSERT INTO photos(user, gallery, filename, uploaddate) VALUES ('$log_username','$gallery','$db_file_name',now())";
	$query = mysqli_query($db_conx, $sql);
	mysqli_close($db_conx);
	header("location: ../photos.php?u=$log_username");
	exit();
}
?>