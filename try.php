<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Try</title>
<style>
.flip3D{ width:240px;height:200px; margin:10px; float:left;}
.flip3D > .front{
	-webkit-transform:perspective( 800px ) rotateY( 0deg );
	transform:perspective( 800px ) rotateY( 0deg );
	background:#FC0;
	width:240px;
	height:200px;
	border-radius:7px;
	-webkit-backface-visibility:hidden;
	backface-visibility:hidden;
	transition:-webkit-transform .5s linear 0s;
	transition:transform .5s linear 0s;
}


.flip3D > .back{
	-webkit-transform:perspective( 800px ) rotateY( 180deg );
	transform:perspective( 800px ) rotateY( 180deg );
	background:#80BFFF;
	width:240px;
	height:200px;
	border-radius:7px;
	-webkit-backface-visibility:hidden;
	backface-visibility:hidden;
	transition:-webkit-transform .5s linear 0s;
	transition:transform .5s linear 0s;
}
</style>
<script>
function flip(){
	var front = document.getElementById("front");
	var back = document.getElementById("back");
	front.style.transition = "perspective .5s linear 0s,transform .5s linear 0s";
	front.style.perspective = "800px";
	front.style.transform = "rotateY(-180deg)";
}
function flip2back(){
	var front = document.getElementById("front");
	var back = document.getElementById("back");
	front.style.transition = "perspective .5s linear 0s,transform .5s linear 0s";
	front.style.perspective = "800px";
	front.style.transform = "rotateY(0deg)";
}
</script>
</head>

<body>
<div class="flip3D">
	<div class="back" id="back" onclick="flip2back()">Box 1-Back</div>
    <div class="front" id="front" onclick="flip()">Box 1-Front</div>
</div>
<div class="flip3D">
	<div class="back">Box 2-Back</div>
    <div class="front">Box 2-Front</div>
</div>
<div class="flip3D">
	<div class="back">Box 3-Back</div>
    <div class="front">Box 3-Front</div>
</div>


</body>
</html>