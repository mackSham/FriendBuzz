<?php
include_once("includes/check_login_status.php");
// If the page requestor is not logged in, usher them away
if($user_ok != true || $log_username == ""){
	header("location: index.php");
    exit();
}
?>

<?php
$sql = "SELECT best_time FROM useroptions WHERE username = '$log_username' LIMIT 1";
$query = mysqli_query($db_conx,$sql);
$row = mysqli_fetch_assoc($query);
$best_time = $row['best_time'];
?>

<?php
if(isset($_POST["time"])){
	$time = $_POST["time"];
	$sql = "UPDATE useroptions SET best_time = '$time' WHERE username  = '$log_username'";
	mysqli_query($db_conx,$sql);
}
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Memory Game</title>
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style type="text/css">
div#memory_board{
	background:#CCC;
	border:#999 solid 1px;
	width:800px;
	height:540px;
	padding:24px;
	position:relative;
	margin-left:90px;
	top:75px;	
	float:left;
}

div#memory_board > div{
	background:url(images/Memory.jpg) no-repeat;
	background-size:100% 100%;
	width:71px;
	height:71px;
	float:left;
	margin:10px;
	padding:20px;
	font-size:64px;
	cursor:pointer;
	text-align:center;
}
#stopwatch{
	float:left;
	width:350px;
	height:665px;
	position:relative;
	top:75px;	
}
#stopwatch > table{
	margin-top:100px;	
}
.container {
	text-align: center;
}

.timer {
	padding: 10px;
	background: linear-gradient(top, #222, #444);
	overflow: hidden;
	display: inline-block;
	border: 7px solid #efefef;
	border-radius: 5px;
	position: relative;
	width:310px;
	font-size:48px;
	text-align:center;
	box-shadow: 
		inset 0 -2px 10px 1px rgba(0, 0, 0, 0.75), 
		0 5px 20px -10px rgba(0, 0, 0, 1);
}
</style>
<script>
function update_db(){
	var time = document.getElementById("time").innerHTML;
	var best_time = document.getElementById("best_time").innerHTML;
	alert(time+' ' +best_time);	
	if(best_time <= time){
	}else{
		var ajax = ajaxObj("POST", "memory_game.php");
		ajax.onreadystatechange = function() {
			if(ajaxReturn(ajax) == true) {
				_('best_time').innerHTML = time;
				reset();
				stop();
			}
		}
		ajax.send("time="+time);
	}	
}
</script>
<script type="text/javascript">
var memory_array = ['1','1','2','2','3','3','4','4','5','5','6','6','7','7','8','8','9','9','10','10','11','11','12','12'];
var memory_values = [];
var memory_tile_ids = [];
var tiles_flipped = 0;
Array.prototype.memory_tile_shuffle = function(){
	var i = this.length,j,temp;
	while(--i > 0){
		j = Math.floor(Math.random() * (i+1));
		temp = this[j];
		this[j] = this[i];
		this[i] = temp;
	}
}

function newBoard(){
	tiles_flipped = 0;
	var output = '';
	memory_array.memory_tile_shuffle();
	for(var i = 0; i < memory_array.length; i++){
		output += '<div id="tile_'+i+'" onclick="memoryFlipTile(this,\''+memory_array[i]+'\'); start();"></div>'; 
	}
	document.getElementById('memory_board').innerHTML = output;
}

function memoryFlipTile(tile,val){
	if(tile.innerHTML == "" && memory_values.length < 2){
		tile.style.backgroundImage = "url('images/Memory_Game/"+val+".jpg')";
		tile.style.backgroundSize = "100% 100%";
		if(memory_values.length == 0){
			memory_values.push(val);
			memory_tile_ids.push(tile.id);
		}else if(memory_values.length == 1){
			memory_values.push(val);
			memory_tile_ids.push(tile.id);
			if(memory_values[0] == memory_values[1]){
				tiles_flipped += 2;
				var tile_1 = document.getElementById(memory_tile_ids[0]);
				var tile_2 = document.getElementById(memory_tile_ids[1]);
				setTimeout(function(){tile_1.style.background = '#CCC';tile_2.style.background = '#CCC';}, 1000);
				tile_1.innerHTML=" ";
				tile_2.innerHTML=" ";
				//Clear both arrays
				memory_values = [];
				memory_tile_ids = [];
				//check to see if the whole board is cleared
				if(tiles_flipped == memory_array.length){
					update_db();
					alert("Board Cleared.... generating new Board");
					document.getElementById('memory_board').innerHTML = '';
					window.location = "memory_game.php";
				}
			}else{
				function flip2Back(){
					//Flip the 2 tilesback over
					var tile_1 = document.getElementById(memory_tile_ids[0]);
					var tile_2 = document.getElementById(memory_tile_ids[1]);
					tile_1.style.background = 'url(images/Memory.jpg) no-repeat';
					tile_1.style.backgroundSize = "100% 100%";
					tile_1.innerHTML ='';
					tile_2.style.background = 'url(images/Memory.jpg) no-repeat';
					tile_2.style.backgroundSize = "100% 100%";
					tile_2.innerHTML ='';
					//Clear both Arrays
					memory_values = [];
					memory_tile_ids = [];					
				}
				setTimeout(flip2Back,700);
			}
		}
	}
}
</script>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script src="js/expand_retract.js"></script>
<script src="js/autoscroll.js"></script>
<script src="js/fadeeffect.js"></script>
<script>

var clsStopwatch = function() {
		// Private vars
		var	startAt	= 0;	// Time of last start / resume. (0 if not running)
		var	lapTime	= 0;	// Time on the clock when last stopped in milliseconds

		var	now	= function() {
				return (new Date()).getTime(); 
			}; 
 
		// Public methods
		// Start or resume
		this.start = function() {
				startAt	= startAt ? startAt : now();
			};

		// Stop or pause
		this.stop = function() {
				// If running, update elapsed time otherwise keep it
				lapTime	= startAt ? lapTime + now() - startAt : lapTime;
				startAt	= 0; // Paused
			};

		// Reset
		this.reset = function() {
				lapTime = startAt = 0;
			};

		// Duration
		this.time = function() {
				return lapTime + (startAt ? now() - startAt : 0); 
			};
	};

var x = new clsStopwatch();
var $time;
var clocktimer;

function pad(num, size) {
	var s = "0000" + num;
	return s.substr(s.length - size);
}

function formatTime(time) {
	var h = m = s = ms = 0;
	var newTime = '';

	h = Math.floor( time / (60 * 60 * 1000) );
	time = time % (60 * 60 * 1000);
	m = Math.floor( time / (60 * 1000) );
	time = time % (60 * 1000);
	s = Math.floor( time / 1000 );
	ms = time % 1000;

	newTime = pad(h, 2) + ':' + pad(m, 2) + ':' + pad(s, 2) + ':' + pad(ms, 3);
	return newTime;
}

function show() {
	$time = document.getElementById('time');
	update();
}

function update() {
	$time.innerHTML = formatTime(x.time());
}

function start() {
	clocktimer = setInterval("update()", 1);
	x.start();
}

function stop() {
	x.stop();
	clearInterval(clocktimer);
}

function reset() {
	stop();
	x.reset();
	update();
}
	</script>
</head>
<body onload="show();">
<?php 
include("includes/header.php");
?>
<div id="memory_board">
<script>newBoard();</script>
</div>
<div id="stopwatch">
<table border="0">
<tr>
	<td>
		<div class="container">
			<div><div class="timer"><div id="best_time"><?php echo $best_time; ?></div></div></div>
		</div>
    </td>
</tr>
<tr>
<td align="center" style="font-size:28px;">Best Time</td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr><tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>
<tr>
<td></td>
</tr>

<tr>
<td>
<div class="container">
	<div class="timer"><span id="time"></span></div>
	<!--
    <input type="button" value="start" onclick="start();">
	<input type="button" value="stop" onclick="stop();">
	<input type="button" value="reset" onclick="reset()">
    -->
</div>
</td>
</tr>
<tr>
<td align="center" style="font-size:28px;">
Time
</td>
</tr>
</table>


</div>
</body>
</html>