<?php
session_start();
// If user is logged in, header them away
if(isset($_SESSION["username"])){
	header("location: message.php?msg=NO to that weenis");
    exit();
}
?>
<?php
// Ajax calls this NAME CHECK code to execute
if(isset($_POST["usernamecheck"])){
	include_once("includes/db_conx.php");
	$username = preg_replace('#[^a-z0-9]#i', '', $_POST['usernamecheck']);
	$sql = "SELECT id FROM user WHERE u_name='$username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $uname_check = mysqli_num_rows($query);
    if (strlen($username) < 3 || strlen($username) > 16) {
	    echo '<strong style="color:#F00;">3 - 16 characters please</strong>';
	    exit();
    }
	if (is_numeric($username[0])) {
	    echo '<strong style="color:#F00;">Usernames must begin with a letter</strong>';
	    exit();
    }
    if ($uname_check < 1) {
	    echo '<strong style="color:#009900;">' . $username . ' is OK</strong>';
	    exit();
    } else {
	    echo '<strong style="color:#F00;">' . $username . ' is taken</strong>';
	    exit();
    }
}
?>
<?php
// Ajax calls this REGISTRATION code to execute
if(isset($_POST["u"])){
	// CONNECT TO THE DATABASE
	include_once("includes/db_conx.php");
	// GATHER THE POSTED DATA INTO LOCAL VARIABLES
	$fn = preg_replace('#[^a-z]#', '', $_POST['fn']);
	$ln = preg_replace('#[^a-z]#', '', $_POST['ln']);
	$u = preg_replace('#[^a-z0-9]#i', '', $_POST['u']);
	$e = mysqli_real_escape_string($db_conx, $_POST['e']);
	$p = $_POST['p'];
	$g = preg_replace('#[^a-z]#', '', $_POST['g']);
	$c = preg_replace('#[^a-z ]#i', '', $_POST['c']);
	$bd = preg_replace('#[^0-9]#i', '', $_POST['bd']);
	$bm = preg_replace('#[^a-z]#i', '', $_POST['bm']);
	$by = preg_replace('#[^0-9]#i', '', $_POST['by']);
	// GET USER IP ADDRESS
    $ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	// DUPLICATE DATA CHECKS FOR USERNAME AND EMAIL
	$sql = "SELECT id FROM user WHERE u_name='$u' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$u_check = mysqli_num_rows($query);
	// -------------------------------------------
	$sql = "SELECT id FROM user WHERE u_email='$e' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
	$e_check = mysqli_num_rows($query);
	// FORM DATA ERROR HANDLING
	if(g == "Choose Your Gender" || c == "Choose Your Country" || bd == "Date" || bm == "Month" || by == "Year"){
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
	// END FORM DATA ERROR HANDLING
	    // Begin Insertion of data into the database
		// Hash the password and apply your own mysterious unique salt
		$cryptpass = crypt($p);
		include_once ("includes/randStrGen.php");
		$p_hash = randStrGen(20)."$cryptpass".randStrGen(20);
		// Add user info into the database table for the main site table
		$sql = "INSERT INTO user(f_name, l_name, u_name, u_email, u_pass, u_gen,u_country, u_bir_date, u_bir_mon, u_bir_year, signup_date, lastlogin_date, ip)VALUES('$fn','$ln','$u','$e','$p_hash','$g','$c','$bd','$bm','$by',now(),now(),'$ip')";
		$query = mysqli_query($db_conx, $sql); 
		$uid = mysqli_insert_id($db_conx);
		// Establish their row in the useroptions table
		$sql = "INSERT INTO useroptions (id, username, background) VALUES ('$uid','$u','original')";
		$query = mysqli_query($db_conx, $sql);
		// Create directory(folder) to hold each user's files(pics, MP3s, etc.)
		if (!file_exists("user/$u")) {
			mkdir("user/$u", 0755);
		}
		// Email the user their activation link
		echo "signup_success";
	}
	exit();
}
?>
<!DOCTYPE Html>
<html>
	<head>
    	<meta charset="utf-8">
		<title>Friend Buzz</title>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script>
			function restrict(el){
				var tf = _(el);
				var rx = new RegExp;
				if(el=="fname"){
					rx = /[^a-z]/gi;
				}else if(el=="lname"){
					rx = /[^a-z]/gi;
				} 
				tf.value = tf.value.replace(rx,"");
			}
			function restrict1(el){
				var tf = _(el);
				var rx = new RegExp;
				if(el=="username"){
					rx = /[^a-z0-9]/gi;
				}
				tf.value = tf.value.replace(rx,"");
			}
			
			function checkusername(){
				var u =_("username").value;
				if(u != ""){
					_("unamestatus").innerHTMl='checking...';
					var ajax = ajaxObj("POST","index.php");
					ajax.onreadystatechange = function() {
						if(ajaxReturn(ajax) == true) {
							_("unamestatus").innerHTML = ajax.responseText;
						}
					}
					ajax.send("usernamecheck="+u);
				}
			}
			
			function signup(){
				var fn = _("fname").value;
				var ln= _("lname").value;
				var u = _("username").value;
				var e1 = _("email1").value;
				var e2 = _("email2").value;
				var p1 = _("password1").value;
				var p2 = _("password2").value;
				var g = _("gender").value;
				var c = _("country").value;
				var bd = _("bdate").value;
				var bm = _("bmon").value;
				var by = _("byear").value;
				var status = _("status")
				if(g == "Choose Your Gender" || c == "Choose Your Country" || bd == "Date" || bm == "Month" || by == "Year"){
					status.innerHTML = "Fill out all the form data";
				}else if(p1!=p2){
					status.innerHTML = "Password fields do not match";
				}else if(e1!=e2){
					status.innerHTML = "Email fields do not match";
				} else {
					status.innerHTML = "Submitting.....";
					var ajax = ajaxObj("POST","index.php");
					 ajax.onreadystatechange = function() {
	        		if(ajaxReturn(ajax) == true) {
	            		if(ajax.responseText != "signup_success"){
							status.innerHTML = ajax.responseText;
							_("signupbtn").style.display = "block";
						} else {
							window.scrollTo(0,0);
							_("signupform").innerHTML = "OK "+u+", check your email inbox and junk mail box at <u>"+e+"</u> in a moment to complete the sign up process by activating your account. You will not be able to do anything on the site until you successfully activate your account.";
						}
	        		}
        		}
				ajax.send("fn="+fn+"ln="+ln+"u="+u+"&e="+e1+"&p="+p1+"&c="+c+"&g="+g+"bd="+bd+"bm="+bm+"by="+by);
			}
		}

		</script>
	</head>
<body>
<?php
	include("includes/header1.php");
?>
    <div id="page_middle" style="height:670px">
    	<div id="page_middle_content" style="height:670px">
       	  <table width=100% align="right" border="1">
            	<tr>
                	<td width="60%" valign="top"> <img src="images/homepage.png" alt="homepage">
                    </td>
                    <td width="2%"></td>
                    <td width="38%" valign="top" >
                    	<h2>Sign up Today for Free!!!</h2><br>
                        <form name="signupform" id="signupform" onsubmit="return false;">
                        <table border="1" width="100%" height="60%">
                        <tr><td><input type="text" name="fname" placeholder="Enter First Name" required class="text_box" id="fname" onKeyUp="restrict('fname')"></td></tr><tr><td></td></tr>
                        <tr><td><input type="text" name="lname" placeholder="Enter Last Name" required class="text_box" id="lname" onKeyUp="restrict('lname')"></td></tr><tr><td></td></tr>
                        <tr><td><input type="text" name="uname" placeholder="Enter User Name" required class="text_box" id="username" onKeyUp="restrict1('username')" onblur="checkusername()"></td></tr><tr><td></td></tr>
                        <tr><td><input type="email" name="email1" placeholder="Enter Email Address" required class="text_box" id="email1"></td></tr><tr><td></td></tr>
                        <tr><td><input type="email" name="email2" placeholder="Enter Email Address Again" required class="text_box" id="email2"></td></tr><tr><td></td></tr>
                        <tr><td><input type="password" name="pass1" placeholder="Enter Password" required class="text_box" id="password1"></td></tr><tr><td></td></tr>
                        <tr><td><input type="password" name="pass2" placeholder="Enter Password Again" required class="text_box" id="password2"></td></tr><tr><td></td></tr>
                        <tr><td><select required id="gender">
  								<option selected disabled>Choose Your Gender</option>
 							    <option value="Male">Male</option>
  								<option value="Female">Female</option>
					   	</select></td></tr><tr><td></td></tr>
                        <tr><td><select required id="country">
  								<option selected disabled>Choose Your Country</option>
 							    <option value="India">India</option>
  								<option value="USA">United State America</option>
						</select></td></tr><tr><td></td></tr>
                           <tr><td> <label style="font-size:20px">Birthday:</label></td></tr><tr><td></td></tr>
                        	<tr><td>
                            <select required class="birth_day_date" id="bdate">
  								<option selected disabled>Date</option>
 							    <option value="01">01</option>
  								<option value="02">02</option>
                                <option value="03">03</option>
  								<option value="04">04</option><option value="05">05</option>
  								<option value="06">06</option><option value="07">07</option>
  								<option value="08">08</option><option value="09">09</option>
  								<option value="10">10</option><option value="11">11</option>
  								<option value="12">12</option><option value="13">13</option>
  								<option value="14">14</option><option value="15">15</option>
  								<option value="16">16</option><option value="17">17</option>
  								<option value="18">18</option><option value="19">19</option>
  								<option value="20">20</option><option value="21">21</option>
  								<option value="22">22</option><option value="23">23</option>
  								<option value="24">24</option><option value="25">25</option>
  								<option value="26">26</option><option value="27">27</option>
  								<option value="28">28</option><option value="29">29</option>
  								<option value="30">30</option><option value="31">31</option>
							</select>
                            <select required class="birth_day_mon" id="bmon">
  								<option selected disabled>Month</option>
 							    <option value="jan">Janurary</option>
  								<option value="feb">Feburary</option>
                                <option value="mar">March</option>
  								<option value="arp">April</option>
                                <option value="may">May</option>
  								<option value="jun">June</option>
                                <option value="jul">July</option>
  								<option value="aug">August</option>
                                <option value="sep">September</option>
  								<option value="oct">October</option>
                                <option value="nov">November</option>
  								<option value="dec">December</option>
							</select>
                            <select required class="birth_day_year" id="byear">
  								<option selected disabled>Year</option>
 							    <option value="2000">2000</option>
  								<option value="1999">1999</option>
 							    <option value="1998">1998</option>
 							    <option value="1997">1997</option>
  								<option value="1996">1996</option>
 							    <option value="1995">1995</option>
  								<option value="1994">1994</option>
 							    <option value="1993">1993</option>
  								<option value="1992">1992</option>
 							    <option value="1991">1991</option>
  								<option value="1990">1990</option>
 							    <option value="1989">1989</option>
  								<option value="1988">1988</option>
 							    <option value="1987">1987</option>
  								<option value="1986">1986</option>
 							    <option value="1985">1985</option>
  								<option value="1984">1984</option>
 							    <option value="1983">1983</option>
  								<option value="1982">1982</option>
 							    <option value="1981">1981</option>
  								<option value="1980">1980</option>
 							    <option value="1979">1979</option>
  								<option value="1978">1978</option>
 							    <option value="1977">1977</option>
  								<option value="1976">1976</option>
 							    <option value="1975">1975</option>
  								<option value="1974">1974</option>
 							 </select></td></tr><tr><td></td></tr>
                            <tr><td></td></tr>
                            <tr><td style="padding:2px"><input type="checkbox" required> &nbsp;I agree your <a href="#" onClick="return false;">Terms and Condition.</a></td></tr><tr><td></td></tr>
                          <tr><td align="center"><button class="signupbtn" onclick="signup()">Create an account</button><div id="status" style="border:1px dashed#000000;height:15px; width:50px;"></div></td></tr>
                        </table>
                </td></tr>
            </table>
        </div>
    </div>
    <span id="unamestatus" style="position:absolute;top:257px;left:1120px;height:25px;width:200px;border:1px #FFFFFF dashed; padding:8px"></span>
  <?php
  	include("/includes/footer.php");
  ?>
 
</body>
</html>