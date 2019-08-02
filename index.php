<?php
include_once("includes/check_login_status.php");
// If user is already logged in, header that weenis away
if($user_ok == true){
	header("location: home.php?u=".$_SESSION["username"]);
    exit();
}
?>
<?php
if(isset($_POST["usernamecheck"])){
	include_once("includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM user WHERE u_name='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '3 - 16 characters please';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo 'Usernames must begin with a letter';
	    exit();
    }
    if ($uname_check < 1) {
	    echo "It's OK";
	    exit();
    } else {
	    echo "It's TAKEN";
	    exit();
    }
}
?>
<?php
if(isset($_POST["u"])){
	include_once("includes/db_conx.php");
	$fn = preg_replace('#[^a-z]#i', '', $_POST['fn']);
	$ln = preg_replace('#[^a-z]#i', '', $_POST['ln']);
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	$c = preg_replace('#[^a-z ]#i', '', $_POST['c']);
	$bd = $_POST['bd'];
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	$sql = "SELECT id FROM user WHERE u_name='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	$sql = "SELECT id FROM user WHERE u_email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	if($u == "" || $e == "" || $p == "" || $g == "" || $c == ""){
		echo "The form submission is missing values.";
        exit();
	} else if ($u_check > 0){ 
        echo "The username you entered is alreay taken";
        exit();
	} else if ($e_check > 0){ 
        echo "That email address is already in use in the system";
        exit();
	} else if (strlen($u) < 3 || strlen($u) > 16) {
        echo "Username must be between 3 and 16 characters";
        exit(); 
    } else if (is_numeric($u[0])) {
        echo 'Username cannot begin with a number';
        exit();
    } else {
		$p_hash = md5($p);
		$sql = "INSERT INTO user (f_name, l_name, u_name, u_email, u_pass, u_gen, u_country, u_bir_date, signup_date, lastlogin_date, ip)VALUES('$fn','$ln','$u','$e','$p_hash','$g','$c','$bd',now(),now(),'$ip')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_conx, $sql);
		$sql = "INSERT INTO status (status_id, status_user,statusdata,statusdate) VALUES ('$uid','$u','Hey!!! Welcome Me on FriendBuzz',now())";
		$query = mysqli_query($db_conx, $sql);
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
			mkdir("user/$u/profilepic", 0755);
			mkdir("user/$u/timelinepic", 0755);
		}
		echo "signup_success";
	}
	
}
?>
<?php
// AJAX CALLS THIS LOGIN CODE TO EXECUTE
if(isset($_POST["le"])){
	// CONNECT TO THE DATABASE
	include_once("includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES AND SANITIZE
	$le = mysqli_real_escape_string($db_conx, $_POST['le']);
	$lp = md5($_POST['lp']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// FORM DATA ERROR HANDLING
	if($le == "" || $lp == ""){
		echo "login_failed";
        exit();
	} else {
	// END FORM DATA ERROR HANDLING
		$sql = "SELECT id, u_name, u_pass FROM user WHERE u_email='$le' LIMIT 1";
        $query = mysqli_query($db_conx, $sql);
        $row = mysqli_fetch_row($query);
		$db_id = $row[0];
		$db_username = $row[1];
        $db_pass_str = $row[2];
		if($lp != $db_pass_str){
			echo "login_failed";
            exit();
		} else {
			// CREATE THEIR SESSIONS AND COOKIES
			$_SESSION['userid'] = $db_id;
			$_SESSION['username'] = $db_username;
			$_SESSION['password'] = $db_pass_str;
			setcookie("id", $db_id, strtotime( '+30 days' ), "/", "", "", TRUE);
			setcookie("user", $db_username, strtotime( '+30 days' ), "/", "", "", TRUE);
    		setcookie("pass", $db_pass_str, strtotime( '+30 days' ), "/", "", "", TRUE); 
			// UPDATE THEIR "IP" AND "LASTLOGIN" FIELDS
			$sql = "UPDATE user SET ip='$ip', lastlogin_date=now() WHERE u_name='$db_username' LIMIT 1";
            $query = mysqli_query($db_conx, $sql);
			echo $db_username;
		    exit();
		}
	}
	exit();
}
?>
<html>
	<head>
    	<title>Friend Buzz</title>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script>
			function restrict(el){
				var tf = _(el);
				var rx = new RegExp;
				if(el=="fname" || el=="lname"){
					rx = /[^a-z]/gi;
				}else if(el=="username"){
					rx = /[^a-z0-9]/gi;
				}else if(el == "email"){
					rx = /[' "]/gi;
				}
				tf.value = tf.value.replace(rx,"");
			}
			function showmsg(){                    //this is to show msg for username box
				_("indication").style.display="block";	
			}
			function hidemsg(){
				_("indication").style.display="none";	//this is for hide msg for usernamr box
			}
			
			function checkusername(){
				var u = _("username").value;
				if(u != ""){
					_("mypics").style.display = "block";
					_("mypics").src = "images/checking.gif";
					var ajax = ajaxObj("POST", "index.php");
        			ajax.onreadystatechange = function() {
	       				if(ajaxReturn(ajax) == true) {
	            			if(ajax.responseText == "3 - 16 characters please"){
								_("mypics").src = "images/problem.jpg";
								_("mypics").style.display = "block";
								_("mypics").alt = "3 - 16 characters please";
								_("indication").innerHTML="3 - 16 characters please";
								_("mypics").addEventListener("mouseover", showmsg);
								_("mypics").addEventListener("mouseout", hidemsg);
							} else if(ajax.responseText == "Usernames must begin with a letter"){
								_("mypics").src = "images/problem.jpg";
								_("mypics").style.display = "block";
								_("mypics").alt = "Usernames must begin with a letter";
								_("indication").innerHTML="Usernames must begin with a letter";
								_("mypics").addEventListener("mouseover", showmsg);
								_("mypics").addEventListener("mouseout", hidemsg);
							} else if(ajax.responseText == "It's OK"){
								_("mypics").src = "images/tick.jpg";
								_("mypics").style.display = "block";
								_("mypics").alt = "This username is OK";
								_("indication").innerHTML="This username is OK";
								_("mypics").addEventListener("mouseover", showmsg);
								_("mypics").addEventListener("mouseout", hidemsg);
							} else if(ajax.responseText == "It's TAKEN"){
								_("mypics").src = "images/problem.jpg";
								_("mypics").style.display = "block";
								_("mypics").alt = "This username is Taken";
								_("indication").innerHTML="This username is Taken";
								_("mypics").addEventListener("mouseover", showmsg);
								_("mypics").addEventListener("mouseout", hidemsg);
							}
	        			}
        			}
        			ajax.send("usernamecheck="+u);
				}
			}
			
			function signup(){
				var fn = _("fname").value;
				var ln = _("lname").value;
				var u = _("username").value;
				var e = _("email").value;
				var p1 = _("pass1").value;
				var p2 = _("pass2").value;
				var g = _("gender").value;
				var c = _("country").value;
				var bd = _("bdate").value;
				
				//Check for valid Email Address
				var n1 = e.search("@");
				var n2 = e.search(".");
				
				if(g == "Choose Your Gender" || c == "Choose Your Country"){
					alert("Fill out all of the form data");
				}else if(n1==-1||n2==-1){
					alert("Enter a Valid Email Address");
				}else if(p1 != p2){
					alert("Your password fields do not match");
				}  else {
					_("signupbtn").style.display = "none";
					_("signupbtn").innerHTML = "Please Wait";
					var ajax = ajaxObj("POST", "index.php");
        			ajax.onreadystatechange = function() {
	        			if(ajaxReturn(ajax) == true) {
	            			if(ajax.responseText.match(/signup_success/) == "signup_success"){
								alert("Success!!! Welcome To FriendBuzz");
								window.location = 'index.php';
							} else{
								alert("SignUp Unsuccessful");
								_("signupbtn").style.display = "block";
								_("signupbtn").innerHTML = "Create an account";
							}
	        			}
        			}
        			ajax.send("fn="+fn+"&ln="+ln+"&u="+u+"&e="+e+"&p="+p1+"&c="+c+"&g="+g+"&bd="+bd);
				}
			}
		</script>
        <script>
			function login(){
				var le = _("lemail").value;
				var lp = _("lpassword").value;
				if(le == "" || lp == ""){
					alert("Fill out all of the form data");
				} else {
					_("loginbtn").style.opacity ="0.9";
					_("loginbtn").innerHTML = 'please wait ...';
					var ajax = ajaxObj("POST", "index.php");
        			ajax.onreadystatechange = function() {
	        			if(ajaxReturn(ajax) == true) {
	            			if(ajax.responseText == "login_failed"){
								alert("Login unsuccessful, please try again");
								_("loginbtn").style.opacity = "1";
								_("loginbtn").innerHTML = 'Login';
							}else {
								window.location = "home.php?u="+ajax.responseText;
							}
	        			}
        			}
        			ajax.send("le="+le+"&lp="+lp);
				}
			}
		</script>
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
					/*dialogbox.style.WebkitAnimation = "loginmove 1s 1 forwards";
					dialogbox.style.animation = "loginmove 1s 1 forwards";*/
				}
				this.cancel=function(){
					document.getElementById('dialogbox').style.display = "none";
					document.getElementById('dialogoverlay').style.display = "none";
				}
			}	
			var Alert = new CustomLogin();	
		</script>
    </head>
    <body>
    <div class="background_image" id="background_image">
    	<div class="header">
        	<div class="header_logo">
            <img src="images/Friend Buzz Logo.png" width="340px" height="40px">
            </div>
            <div class="header_log_in">
            	<a style="font-size:25px; color:#FFF; cursor:pointer;" onClick="Alert.render()" >Login</a>
            </div>
        </div>
        <div class="body_content">
        	<div class="body_banner">
            	<h2 style="color:#000;">The New Social Network for Meeting<img src="images/new_people_en_US.png" style="position:relative"></h2>
            </div>
            <div class="body_signup">
            	<div class="sign_up">
                	<span>Sign Up</span>
                </div>
                <hr>
                <div class="signup_form">
                	 <table border="0" width="450px" height="60%" style="position:relative; top:16px;">
                        <form id="signupform" name="signupform" onSubmit="return false;">
                        <tr><td><input type="text" name="fname" placeholder="Enter First Name" required class="text_box_fname" id="fname" onKeyUp="restrict('fname')"> &nbsp;&nbsp;&nbsp;<input type="text" name="lname" placeholder="Enter Last Name" required class="text_box_lname" id="lname" onKeyUp="restrict('lname')"></td></tr><tr><td></td></tr>
                        <tr><td><input type="text" name="uname" placeholder="Enter User Name" required class="text_box_uname" id="username" onKeyUp="restrict('username')" onBlur="checkusername()"><img id="mypics" src="images\checking.gif" width="25px" height="25px" style="top:47px; left:423px; position:absolute; display:none"><div id="indication"></div></td></tr><tr><td></td></tr>
                        <tr><td><input type="email" name="email" placeholder="Enter Email Address" required class="text_box" id="email" onKeyUp="restrict('email')" ></td></tr><tr><td></td></tr>
                        <tr><td><input type="password" name="pass1" placeholder="Enter Password" required class="text_box" id="pass1"></td></tr><tr><td></td></tr>
                        <tr><td><input type="password" name="pass2" placeholder="Enter Password Again" required class="text_box" id="pass2"></td></tr><tr><td></td></tr>
                        <tr><td><select required id="gender">
  								<option selected disabled>Choose Your Gender</option>
 							    <option value="m">Male</option>
  								<option value="f">Female</option>
					   	</select></td></tr><tr><td></td></tr>
                        <tr><td><select required id="country">
  								<option selected disabled>Choose Your Country</option>
 							    <option value="India">India</option>
  								<option value="USA">United State America</option>
						</select></td></tr><tr><td></td></tr>
                           <tr><td> <label style="font-size:20px">Birthday:</label></td></tr><tr><td></td></tr>
                        	<tr><td>
                            	<input type="date" id="bdate" class="text_box" placeholder="Enter your Birthday" required>
                            </td></tr><tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td></td></tr><tr><td></td></tr>
                          <tr><td align="center"><input type="submit" class="signupbtn" onClick="signup()" id="signupbtn" value="Create an account"></td></tr>
                          </form>
                        </table>
                </div>
            </div>
        </div>
        
        
        <div id="dialogoverlay" onClick="Alert.cancel()"></div>
        <div id="dialogbox">
        	<div>
            	<div id="dialogboxhead">Login</div>
                <hr>
                <div id="dialogboxbody">
                <form onSubmit="return false;">
                <table>
                <tr><td><input type="email" style="background-color:#FFFFFF; border:1px solid #E2E2E2; font-size:15px; padding:5px; width:390px; margin-bottom:3px; height:30px;" placeholder="Enter your email address" id="lemail" required></td></tr><tr><td></td></tr><tr><td></td></tr>
                <tr><td><input type="password" style="background-color:#FFFFFF; border:1px solid #E2E2E2; font-size:15px; padding:5px; width:390px; margin-bottom:3px; height:30px;" placeholder="Enter your password" id="lpassword" required></td></tr>
                </table>
                </div>
                <hr>
                <div id="dialogboxfoot">
                	<a href="forgetpassword.php">Forget Password?</a>
                	<button type="submit"  onClick="login()" id="loginbtn">Login</button> </form>
                 	<button type="submit" onClick="Alert.cancel()">Cancel</button>
                </div>
            </div>
        </div>
    </div>