<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>
<?php
$id = $_GET["id"];
$sql = "SELECT * FROM notifications WHERE id = '$id' LIMIT 1";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_assoc($query);
if($row['reciever'] != $log_username){
	header('location:index.php');
}else{
	$score = $row["word_best_score"];
	$challenger = $row["initiator"];	
}
?>
<?php
function update_database(){
	$sql = "SELECT best_score FROM useroptions WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_assoc($query);
	$high_score = $row['best_score'];
}

$sql = "SELECT best_score FROM useroptions WHERE username='$log_username' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$row = mysqli_fetch_assoc($query);
	$high_score = $row['best_score'];
?>
<?php
if(isset($_POST["score"])){
	$score = $_POST["score"];
	$sql = "UPDATE useroptions SET best_score = '$score' WHERE username  = '$log_username'";
	mysqli_query($db_conx,$sql);
}
?>
<?php
if(isset($_POST["scores"])){
	$id=$_GET["id"];
	$challenged_score = $_POST['challenged_score'];
	$score = $_POST['scores'];
	$sql = "SELECT * FROM notifications WHERE id = '$id' LIMIT 1";
	$query = mysqli_query($db_conx,$sql);
	$row = mysqli_fetch_assoc($query);
	$challenger = $row["initiator"];
	$sql = "DELETE FROM notifications WHERE id='$id'";
	mysqli_query($db_conx,$sql);
	if(ceil($score) > ceil($challenged_score)){
		$sql = "INSERT INTO notifications (initiator,reciever,type,date_time)VALUES('$log_username','$challenger','loss_wordrush',now())";
		mysqli_query($db_conx,$sql);
		$sql = "INSERT INTO notifications (initiator,reciever,type,date_time)VALUES('$log_username','$log_username','win_wordrush',now())";
		mysqli_query($db_conx,$sql);
		echo 'You Win '.$score.' '.$challenged_score.'';
	}else{
		$sql = "INSERT INTO notifications (initiator,reciever,type,date_time)VALUES('$log_username','$challenger','win_wordrush',now())";
		mysqli_query($db_conx,$sql);
		$sql = "INSERT INTO notifications (initiator,reciever,type,date_time)VALUES('$log_username','$log_username','loss_wordrush',now())";
		mysqli_query($db_conx,$sql);
		echo 'You Lose '.$score.' '.$challenged_score.'';
	}
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Word Rush</title>
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style>
#game{
	width:700px;
	height:400px;
	float:left;
		
}
#correct_word{
	width:550px;
	height:200px;
	margin:0px auto;
	padding-top:20px;
}
#correct_word > div{
	float:left;
	width:100px;
	height:100px;
	margin-left:30px;
	background-color:#33C;
	border-radius:15px;	
}
#timer{
	width:410px;
	margin:0px auto;
	padding:20px auto;
	text-align:center;	
}
#my_canvas > div{
	width:50px;
	height:50px;
}

#word_0{
	color:#FFF;
	text-align:center;
	font-size:90px;	
}
#word_1{
	color:#FFF;
	text-align:center;
	font-size:90px;	
}
#word_2{
	color:#FFF;
	text-align:center;
	font-size:90px;	
}
#word_3{
	color:#FFF;
	text-align:center;
	font-size:90px;	
}
#jumble{
	width:140px;
	height:140px;
	position:relative;
	top:-200px;
	left:135px;
	text-align:center;
	font-size:50px;
	color:#FFF;
}
#jumble_0{
	width:65px;
	height:65px;
	background:#393;
	top:-200px;
	left:135px;
	text-align:center;
	font-size:58px;
	color:#FFF;
	cursor:pointer;
	border-radius:13px;
	float:left;
	margin-left:5px;
	margin-bottom:5px;
}
#jumble_1{
	width:65px;
	height:65px;
	background:#393;
	top:-265px;
	left:209px;
	font-size:58px;
	color:#FFF;
	cursor:pointer;
	border-radius:13px;
	float:left;
	margin-left:5px;
	margin-bottom:5px;
}
#jumble_2{
	width:65px;
	height:65px;
	background:#393;
	top:-258px;
	left:135px;
	font-size:58px;
	color:#FFF;
	cursor:pointer;
	border-radius:13px;
	float:left;
	margin-left:5px;
	margin-bottom:5px;
}
#jumble_3{
	width:65px;
	height:65px;
	background:#393;
	top:-323px;
	left:209px;
	font-size:58px;
	color:#FFF;
	cursor:pointer;
	border-radius:13px;
	float:left;
	margin-left:5px;
	margin-bottom:5px;
}
#score_div{
	float:left;
	width:500px;
	height:800px;	
}
#incenter{
	margin-top:20px;
	width:500px;
	height:200px;

}
#scores,#challenge > table{
	font-size:36px;
	color:#FFF;
	text-align:center;
}
#records > table{
	text-align:center;
	font-family:Verdana, Geneva, sans-serif;
	font-size:18px;
	color:#FFF; 	
}
#new_game{
	cursor:pointer;
}
#challenge{
	cursor:pointer;	
}
#home{
	cursor:pointer;	
}
#challenger{
	font-size:30px;
	color:#FFF;
	border:1px dashed #000;
	position:relative;
	top:-80px;	
	
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
	var filledalpha_no = 0;  // No. of content filled in upper box
	var filledalpha = []; // What is filled in upperbox
	var filledalpha_id = []; // Ids of lower divs filled in upper div
	var score = 00;
	
	Array.prototype.search = function(elm){
		var i;
		for(i=0 ; i < this.length ; i++){
			if(elm == this[i]){
				return true;
			}
		}
	}
	var words = ['DEAN','BIDE','DAFT','CART','OATH','DUPE','SOLE','GRIM','PIES','SWAT','AMID','YANK','MARE','RINK','AFRO','PROS','ONYK','AIRY',
'SIRE','SWAB','NERD','SPUR','RODE','TAXI','ONYX','ABLY','GOWN','SWAB','COWL','SWAT','WHIP','ANON','SIRS','PIPS','MAIM','DEAF','RUFF','HUTS','WEDS','PAIL',
'SUMS','BURP','ZINC','LENT','HOTS','SLAY','MAID','EYED','HOST','NANO','SHOT','THUS','REND','FADE'];
	
	Array.prototype.shuffle = function(){
		var i = this.length,j,temp;
		while(--i > 0){
			j = Math.floor(Math.random() * (i+1));
			temp = this[j];
			this[j] = this[i];
			this[i] = temp;
		}
	}
	words.shuffle();
	function setwords(){
		for(var i = 0 ; i < words.length ; i++){
			var words_to_show = words[i];
			var word_array = words_to_show.split("");
			word_array.shuffle();
			for(var j = 0 ; j < 4; j++){
				document.getElementById("jumble_"+j).innerHTML = word_array[j];
			}
		}
	}
	function validate(alpha){
		var send = document.getElementById(alpha).innerHTML;
		document.getElementById(alpha).style.opacity='0.2';
		if(filledalpha_id.search(alpha) == true){
			var a = filledalpha_id.indexOf(alpha);
			//alert(a);
			filledalpha_no -= 1;
			filledalpha_id.splice(a, 1);
			filledalpha.splice(a, 1);
   			//alert(filledalpha_id.valueOf());
			//alert(filledalpha.valueOf());
			document.getElementById('word_'+a).innerHTML='';
			document.getElementById(alpha).style.opacity='1';
			for(var i=0 ; i < filledalpha.length ; i++){
				document.getElementById('word_'+i).innerHTML = filledalpha[i];
			}
			for(var j = filledalpha.length; j<4; j++){
				document.getElementById('word_'+j).innerHTML = '';
			}
			
			return;
		}else{
			for(var i = 0; i < 4; i++){
				var word = document.getElementById('word_'+i);
				if(word.innerHTML == ''){
					word.innerHTML = send;
					break;
				}
			}
			filledalpha_no += 1;
			filledalpha.push(send);
			filledalpha_id.push(alpha);
			//alert(filledalpha_no+' '+filledalpha.valueOf()+' '+filledalpha_id.valueOf());
			
			if(filledalpha_no == '4'){
				var coming_word = filledalpha[0]+filledalpha[1]+filledalpha[2]+filledalpha[3];
				//alert(coming_word);
				if(words.search(coming_word) == true){
					//alert("Right word");
					for(var i = 0; i < 4; i++){
						var word = document.getElementById('word_'+i);
						word.innerHTML = '';
						filledalpha_no = 0;
						filledalpha = [];
						filledalpha_id = [];
						document.getElementById('jumble_'+i).style.opacity='1';
					}
					right();
					setTimeout(showdivs , 1000);
					decreasetime();
					words.shuffle();
					setwords();
					score = score+1;
					document.getElementById('score').innerHTML = score;
				}else{
					wrong();
					setTimeout(showdivs , 1000);
					for(var i = 0; i < 4; i++){
						var word = document.getElementById('word_'+i);
						word.innerHTML = '';
						filledalpha_no = 0;
						filledalpha = [];
						filledalpha_id = [];
						document.getElementById('jumble_'+i).style.opacity='1';
					}	
				}
			}
		}
	}
</script>
<script>
function decreasetime(){
	al = al-10;
	if(al < 0){
		al = 0;
	}
}
function changecolor(){
	color = 'red';	
}
function right(){
	document.getElementById("jumble_0").style.display = "none";
	document.getElementById("jumble_1").style.display = "none";
	document.getElementById("jumble_2").style.display = "none";
	document.getElementById("jumble_3").style.display = "none";
	document.getElementById("jumble").style.background = "url('images/tick.png') no-repeat";
	document.getElementById("jumble").style.backgroundSize = "140px 140px";
}
function wrong(){
	document.getElementById("jumble_0").style.display = "none";
	document.getElementById("jumble_1").style.display = "none";
	document.getElementById("jumble_2").style.display = "none";
	document.getElementById("jumble_3").style.display = "none";
	document.getElementById("jumble").style.background = "url('images/cross.png') no-repeat";
	document.getElementById("jumble").style.backgroundSize = "140px 140px";
}
function showdivs(){
	document.getElementById("jumble").style.background = "";
	document.getElementById("jumble").style.backgroundSize = "";
	document.getElementById("jumble_0").style.display = "block";
	document.getElementById("jumble_1").style.display = "block";
	document.getElementById("jumble_2").style.display = "block";
	document.getElementById("jumble_3").style.display = "block";
}
function complete(){
	document.getElementById("jumble_0").style.display = "none";
	document.getElementById("jumble_1").style.display = "none";
	document.getElementById("jumble_2").style.display = "none";
	document.getElementById("jumble_3").style.display = "none";
	document.getElementById("jumble").style.top = "-180px";
	document.getElementById("jumble").style.left = "130px";
	document.getElementById("jumble").innerHTML = "TIME'S UP!";
	setTimeout(check_who_win(), 3000);	
}
function compare(el1,el2){
	if(el1 > el2){
		return true;
	}else{
		return false;	
	}
}
</script>
<script>
function quit() {
	window.location = "index.php";	
}

function myFunction() {
    location.reload();
}
</script>
<script>
var score = document.getElementById("score").innerHTML;
function challenge(){
	window.location="select_friends.php?score="+score;
}
function check_who_win(){
	var challenged_score = document.getElementById("challenged_score").innerHTML;
	var score = document.getElementById("score").innerHTML;
	alert(score+' '+challenged_score);
	if(Math.sqrt(score)>Math.sqrt(challenged_score)){
		alert("You Win The Challenge");
	}else{
		alert("you lost the challenge");	
	}
	var ajax = ajaxObj("POST", "");
		ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			alert(ajax.responseText);	
		}
	}
	ajax.send("challenged_score="+challenged_score+"&scores="+score);
}
</script>
</head>
<body>
<?php 
include("includes/header.php");
?>
<div id="page_middle">
	<div id="page_middle_content" style="background-color:#263041;">
    <div id = "game">
    	<div id="correct_word">
        	<div id="word_0"></div>
            <div id="word_1"></div>
            <div id="word_2"></div>
            <div id="word_3"></div>
        </div>
        <div id="timer">
			<canvas id="my_canvas" width="250" height="250" style="background-color:#263041;"></canvas>
        	<div id="jumble">
                <div id="jumble_0" onClick="validate('jumble_0')"></div>
                <div id="jumble_1" onClick="validate('jumble_1')"></div>
                <div id="jumble_2" onClick="validate('jumble_2')"></div>
                <div id="jumble_3" onClick="validate('jumble_3')"></div>
            </div>
            <script>
				setwords();
			</script>
			<script>
            var ctx = document.getElementById('my_canvas').getContext('2d');
            var al= 0;
            var start = 4.72;
            var cw = ctx.canvas.width;
            var ch = ctx.canvas.height;
            var diff;
			var color = '#FFF';
            function progessSim(){
                diff = ((al / 100) * Math.PI*2*10).toFixed(2);
                ctx.clearRect(0, 0, cw, ch);
                ctx.lineWidth = 15;
				ctx.strokeStyle = '#000';
                ctx.beginPath();
               	ctx.arc(125,125,115,0*Math.PI,2*Math.PI);
                ctx.stroke();
                ctx.strokeStyle = color;
                ctx.beginPath();
                ctx.arc(125,125,115,start,-diff/10+start,true);
                ctx.stroke();
                if(al >= 100){
                    clearTimeout(sim);
					complete();
					document.getElementById("challenge").disabled = false;
                }
				if(al > 80){
					if(al%2==0){
						color = 'red';
					}else{
						color = 'green';	
					}
				}else{
					color = '#FFF';	
				}
                al++;
            }
            var sim = setInterval(progessSim, 400);
            </script>
            <?php
				echo '<div id="challenger">'.$challenger.'</div>';
			?>
      	</div>
    </div>
    <div id="score_div">
    	<div id="incenter">
        <div id="scores">
        <table border="1" width="500px" height="100px">
        <tr>
        	<td valign="bottom"><div id="score">0</div></td>
        	<td valign="bottom">
			<div id="challenged_score">
					<?php echo $score; ?>
			</div></td>
        </tr>
        <tr>
        	<td align="center" valign="top">Score</td>
        	<td align="center" valign="top">
            To Beat
			  </td>
        </tr>
        </table>
        </div>
        <div id="records">
        <table border="1" width="500px">
        <tr>
        <td>Sr. No.</td>
        <td align="left">Name</td>
        <td>High Score</td>
        </tr>
        <?php
		$sql = "SELECT * FROM useroptions ORDER BY best_score DESC LIMIT 10";
		$query = mysqli_query($db_conx,$sql);
		$i = 0;
		while($row = mysqli_fetch_assoc($query)){
			$i = $i + 1;
			$username = $row["username"];
			$other_best_score = $row["best_score"];
			$sql1 = "SELECT f_name , l_name FROM user WHERE u_name = '$username'";
			$query1 = mysqli_query($db_conx,$sql1);
			$row1 = mysqli_fetch_assoc($query1);
			$fname = $row1["f_name"];
			$lname = $row1["l_name"];
			echo '<tr><td>'.$i.'</td><td align = "left">'.$fname.' '.$lname.'</td><td>'.$other_best_score.'</td></tr>';
		}
		?>
        </table>
        </div>
        <div id="challenge">
        <table border="1" width="500px" height="100px">
        	<tr>
                <td><div id="new_game"><img src="images/New_Game.png" onClick="myFunction()"></div></td>
                <td><div id="challenge"><img src="images/Challenge_friends.png" onClick="challenge()"></div></td>
            	<td><div id="home"><img src="images/Home.png" onclick="quit()"></div></td>
           </tr>
        </table>
        </div>
        </div>
    </div>
  </div>
</div>
</body>
</html>