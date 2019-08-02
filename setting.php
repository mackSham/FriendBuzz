<?php
include_once("includes/check_login_status.php");
if($user_ok != true){
	header('location:index.php');
}
?>
<!--  THis Is a HTML code that verifies the password-->
<div id="overlay"></div>
                <div id="dialogbox">
                    <div>
                        <div id="boxhead">Recheck your password</div>
                        <hr>
                        <div id="boxbody">
                        <form action="setting.php" method="post"><input type="password" style="background-color:#FFFFFF; border:1px solid #E2E2E2; font-size:15px; padding:5px; width:390px; margin-bottom:3px; height:30px;" placeholder="Enter your password" name="pass">
                        </div>
                        <hr>
                        <div id="boxfoot">
                            <input type="submit" name="checkpass">
                        </form>
                        </div>
                    </div>
                </div>
            </div>

<!-- The following PHP code that wheter the user verified the password or not-->            
<?php
if(isset($_GET['user_verified'])){
	if($_GET['user_verified'] == 'false'){
		echo '<script>
				var winW = window.innerWidth;
				var winH = window.innerHeight;
				var overlay = document.getElementById("overlay");
				var dialogbox = document.getElementById("dialogbox");
				overlay.style.display = "block";
				overlay.style.height = winH+"px";
				dialogbox.style.left = (winW/2)-(450 * .5)+"px";
				dialogbox.style.display = "block";
				dialogbox.style.top = "100px";	
			</script>';
	}
}
?>

<!-- Code to verify the password-->
<?php
	if(isset($_POST["checkpass"])){
		$pass = $_POST["pass"];
		$pass = md5($pass);
		$sql = "SELECT id FROM user WHERE u_name='$log_username' AND u_pass = '$pass' LIMIT 1";
		$query = mysqli_query($db_conx,$sql);
		$numrow = mysqli_num_rows($query);
		if($numrow == 1){ 
			header('location:setting.php?user_verified=true');
		}else{ 
			header('location:setting.php?user_verified=false');
		}
	}
?>

<!-- this code tells what to show in the fileds-->
<?php
$firstname = '';
$lastname = '';
$statusdata = '';
$password = '';
$theme = '';
$married_status = '';
$fav_color = '';
$fav_film = '';
$fav_songs = '';
$best_friend = '';
$feild_of_interest = '';
$sql = "SELECT * FROM useroptions WHERE username='$log_username'";
$query = mysqli_query($db_conx,$sql);
while($row=mysqli_fetch_assoc($query)){
	$fav_color = $row['fav_color'];
	$fav_film = $row['fav_film'];
	$best_friend = $row['best_friend'];
	$feild_of_interest = $row['field_of_interest'];
	$fav_songs = $row['fav_songs'];
	$theme = $row['theme'];
	$married_status = $row['married_status'];
}

$sql1 = "SELECT * FROM status WHERE status_user='$log_username'";
$query1 = mysqli_query($db_conx,$sql1);
while($row1=mysqli_fetch_assoc($query1)){
	$statusdata = $row1['statusdata'];
}
$sql2 = "SELECT * FROM user WHERE u_name='$log_username'";
$query2 = mysqli_query($db_conx,$sql2);
while($row2 = mysqli_fetch_assoc($query2)){
	$f_name = $row2['f_name'];
	$l_name = $row2['l_name'];
	$password = $row2['u_pass'];
}
?>
<!-- Script executed when change values in the bottom button is pressed -->
<?php
if(isset($_POST["Change_values"])){
	$fname_change = $_POST['fname_change'];
	$lname_change = $_POST['lname_change'];
	$status_change = $_POST['status_change'];
	$pass_change = $_POST['pass_change'];
	$mysql = "SELECT u_pass FROM user WHERE u_name='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx,$mysql);
	$row = mysqli_fetch_assoc($query);
	$passwd = $row['u_pass'];
	$married_status_change = $_POST['married_status_change'];
	$color_change = $_POST['color_change'];
	$film_change = $_POST['film_change'];
	$song_change = $_POST['song_change'];
	$friend_change = $_POST['friend_change'];
	$interest_change = $_POST['interest_change'];
	$theme_change = $_POST['theme_change'];
	if($pass_change == $passwd){
		$sql = "UPDATE user SET f_name = '$fname_change',
							l_name = '$lname_change' WHERE u_name='$log_username'";
		mysqli_query($db_conx,$sql);
	}else{
		$pass_change = md5($pass_change);
		$sql = "UPDATE user SET f_name = '$fname_change',
							l_name = '$lname_change',
							u_pass = '$pass_change' WHERE u_name='$log_username'";
		mysqli_query($db_conx,$sql);
		$_SESSION['password'] = $pass_change;
	}
	$sql = "UPDATE status SET statusdata = '$status_change',
							statusdate = now() WHERE status_user='$log_username'";
	mysqli_query($db_conx,$sql);
	
	$sql = "UPDATE useroptions SET fav_color = '$color_change',
							fav_film = '$film_change',
							best_friend = '$friend_change',
							married_status = '$married_status_change',
							field_of_interest = '$interest_change',
							fav_songs = '$song_change',
							theme = '$theme_change' WHERE username='$log_username'";
	mysqli_query($db_conx,$sql);
	header("location:setting.php?user_verified=true");
}
?>


<html>
	<head>
    	<meta charset="utf-8">
		<title>Account Setting</title>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <style>
		#overlay{
			opacity:0.5;
			position:fixed;
			display:none;
			top:0px;
			left:0px;
			background:#000;
			width:100%;
			height:100%;
			z-index:10;
		}
		#dialogbox{
			display:none;
			position:fixed;
			background-color:#FFFFFF;
			width:450px;
			height:190px;	
			z-index:10;
			left:450px;
			top:100px;
		}
		#dialogbox > div{
			margin:8px;
		}
		#boxhead{
			padding:10px;
			background-image:url(images/menu_bg.png);
			text-align:center;
			font-size:26px;
			color:#FFF;
		}
		#boxbody{
			padding:20px;
		}
		
		#boxfoot{
			padding-top:10px;
		}
		#boxfoot input{
			padding:5px;
			float:right;
			width:100px;	
		}
		.heading{
			width:80%;
			margin-top:15px;
			margin-left:65px;
		 	text-align: left;
		}
		.profilepage{
			width: 1010px;
			height: auto;
			margin:0 auto;
			background-color: #C7CFE2;
			border-radius: 5px;
			padding:35px;
			overflow:hidden;
			position: relative;
			bottom: 0;
			border: solid #5978AC;	
		}
		input[type="text"],input[type="password"],textarea{
			background-color:#FFFFFF;
			border:1px solid #E2E2E2;
			font-size:15px;
			padding:5px;
			width:550px;
			margin-bottom:3px;
			margin-top:3px;
			height:30px;	
		}
		input:hover{
			border-color:#000;
		}
		input[type="submit"]{
			-webkit-user-select: none;
			background: rgb(76, 142, 250);
			border: 0;
			border-radius: 2px;
			box-sizing: border-box;
			color: #fff;
			width:120px;
			cursor: pointer;
			float: center;
			font-size: .875em;
			margin: 0;
			padding: 10px 24px;
			transition: box-shadow 200ms cubic-bezier(0.4, 0, 0.2, 1);
		}
		select{
			background-color:#FFFFFF;
			border:1px solid #E2E2E2;
			font-size:15px;
			padding:5px;
			width:550px;
			margin-bottom:3px;
			margin-top:3px;
			height:30px;
		}
		textarea{
			height:100px;	
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
            <div class="heading">
                <font face="sans-serif" size="6" color="#333333"><b>Account Settings</b></font>
          	</div>
            <br>
    		<hr width="89%" size="3" color="#666666" style="margin-left:65px;">
            <br>
            <div class="profilepage">
            
       <form action="" method="post">
            <table width="80%" border="0" cellspacing="0" cellpadding="10" style="margin-left:95px;">
          <tr>
            <td width="30%" height="40px"><b><legend>Change First Name:</legend></b></td>
            <td width="70%"><input type="text" name="fname_change" value="<?php echo $f_name; ?>"></td>
          </tr>
          <tr>
            <td height="40px"><b><legend>Change Last Name:</legend></b></td>
            <td> <input type="text" name="lname_change" value="<?php echo $l_name; ?>"></td>
          </tr>
          <tr>
            <td height="40px"><b><legend>Change Status:</legend></b></td>
            <td> <textarea name="status_change"><?php echo $statusdata; ?></textarea></td>
          </tr>
          <tr>
            <td height="40px"><b> <legend>Change Password:</legend></b></td>
            <td><input type="password" name="pass_change" value="<?php echo $password; ?>"></td>
          </tr>
          <tr>
            <td height="40px"><b> <legend>Change Theme:</legend></b></td>
            <td><select name="theme_change">
													<?php
                                                    	if($theme == ''){
                                                        ?>
                                                        	<option value="" selected>Select the best theme</option>
                                                            <option value="friendbuzz">Friendbuzz</option>
                                                            <option value="twitter">Twitter</option>
                                                            <option value="orkut">Orkut</option>
                                                            <option value="facebook">Facebook</option>
													<?php	
                                                        }else if($theme == 'friendbuzz'){
                                                        ?>
                                                            <option selected value="friendbuzz">Friendbuzz</option>
                                                            <option value="twitter">Twitter</option>
                                                            <option value="orkut">Orkut</option>
                                                            <option value="facebook">Facebook</option>
                                                    <?php		
                                                        }else if($theme == 'twitter'){
                                                        ?>
                                                            <option value="friendbuzz">Friendbuzz</option>
                                                            <option selected value="twitter">Twitter</option>
                                                            <option value="orkut">Orkut</option>
                                                            <option value="facebook">Facebook</option>
                                                    <?php		
                                                        }else if($theme == 'orkut'){
                                                        ?>
                                                            <option value="friendbuzz">Friendbuzz</option>
                                                            <option value="twitter">Twitter</option>
                                                            <option selected value="orkut">Orkut</option>
                                                            <option value="facebook">Facebook</option>
                                                    <?php		
                                                        }else{
                                               	    ?>
                                                            <option value="friendbuzz">Friendbuzz</option>
                                                            <option value="twitter">Twitter</option>
                                                            <option value="orkut">Orkut</option>
                                                            <option selected value="facebook">Facebook</option>
                                                    <?php	
                                                        }
                                                   	?>
                                         	</select></td>
          </tr>
          <tr>
            <td height="40px"><b> <legend>Married Status:</legend></b></td>
            <td><select name="married_status_change">
            										<?php
                                                    	if($married_status == ''){
													?>
                                                    	<option selected>Select your married status</option>
                                                    	<option>Single</option>
                                                		<option>Married</option>
                                                		<option>In a Relationship</option>
                                                <?php		
														}else if($married_status == 'single'){
												?>
                                                        <option selected>Single</option>
                                                        <option>Married</option>
                                                        <option>In a Relationship</option>
                                                <?php		
														}else if($married_status == 'married'){
												?>
                                                		<option>Single</option>
                                                		<option selected>Married</option>
                                                		<option>In a Relationship</option>
                                                <?php		
														}else{
												?>
                                                        <option>Single</option>
                                                        <option>Married</option>
                                                        <option selected>In a Relationship</option>
                                                <?php		
														}
                                                ?>
                                            </select></td>
          </tr>
          <tr>
            <td height="40px"><b><legend>Favourite Color:</legend></b></td>
            <td> <input type="text" name="color_change" value="<?php echo $fav_color; ?>"></td>
          </tr><tr>
            <td height="40px"><b> <legend>Favourite Film:</legend></b></td>
            <td><input type="text" name="film_change" value="<?php echo $fav_film; ?>"></td>
          </tr><tr>
            <td height="40px"><b><legend>Favourite Songs:</legend></b></td>
            <td><input type="text" name="song_change" value="<?php echo $fav_songs; ?>"></td>
          </tr><tr>
            <td height="40px"><b><legend>Best Friend:</legend></b></td>
            <td><input type="text" name="friend_change" value="<?php echo $best_friend; ?>"></td>
          </tr><tr>
            <td height="40px"><b><legend>Field of Interest:</legend></b></td>
            <td><input type="text" name="interest_change" value="<?php echo $feild_of_interest; ?>"></td>
          </tr>
          <tr>
          	<td colspan="2" align="center"  height="80px">
           		<input type="submit" name="Change_values">
           	</td>
           </form>
          </tr>
        </table>
            </div>
      	</div>
    </div>
<?php
include("includes/footer.php");
?>
    	

</body>
</html>

