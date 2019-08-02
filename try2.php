<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Try</title>
<style>
.flip3D{ width:240px;height:200px; margin:10px; float:left;}
.flip3D > .front{
	position:absolute;
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
	position:absolute;
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

.flip3D:hover > .front{
	transform:perspective( 800px ) rotateY( -180deg );
}
.flip3D:hover > .back{
	transform:perspective( 800px ) rotateY( 0deg );
}
</style>
</head>

<body>
<div class="flip3D">
	<div class="back">Box 1-Back</div>
    <div class="front">Box 1-Front</div>
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
</html><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>
</body>
</html>