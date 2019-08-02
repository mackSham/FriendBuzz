<?php
include_once("includes/check_login_status.php");
if(isset($_POST["gallerynamecheck"])){
	include_once("includes/db_conx.php");
	$galleryname = $_POST['gallerynamecheck'];
	$sql = "SELECT id FROM photos WHERE  gallery='$galleryname' AND  user='$log_username' LIMIT 1";
    $query = mysqli_query($db_conx, $sql); 
    $gname_check = mysqli_num_rows($query);
    if ($gname_check < 1) {
	    echo "It's OK";
	    exit();
    } else{
	    echo "It's TAKEN";
	    exit();
    }
}
?>
<?php
include_once("includes/check_login_status.php");
// Make sure the _GET "u" is set, and sanitize it
$u = "";
if(isset($_GET["u"])){
	$u = preg_replace('#[^a-z0-9]#i', '', $_GET['u']);
} else {
    header("location: index.php");
    exit();	
}
$photo_form = "";
// Check to see if the viewer is the account owner
$isOwner = "no";
if($u == $log_username && $user_ok == true){
	$isOwner = "yes";
	$sql = "SELECT DISTINCT gallery FROM photos WHERE user='$log_username'";
	$result = mysqli_query($db_conx, $sql);
	$photo_form  = '<form id="photo_form" enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">';
	$photo_form .=   '<h3>Hi '.$u.', want to add a new Gallery&nbsp;<span onClick="Alert.render()">Click here</span></h3>';
	$photo_form .=   '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<h3>OR</h3><br />';
	$photo_form .=   '<h3>Add a new photo into one of your galleries</h3>';
	$photo_form .=   '<br><b>Choose Gallery:</b> ';
	$photo_form .=   '<select name="gallery" required>';
	while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
	$photo_form .=   	'<option value="'.$row["gallery"].'">'.$row["gallery"].'</option>';
	}
	$photo_form .=   '</select>';
	$photo_form .=   ' &nbsp; &nbsp; &nbsp; <b>Choose Photo:</b> ';
	$photo_form .=   '<input type="file" name="photo" accept="image/*" required><br>';
	$photo_form .=   '<p align="right" style="margin-right:230px;"><br><input type="submit" value="Upload Photo Now"></p>';
	$photo_form .= '</form>';
}
// Select the user galleries
$gallery_list = "";
$sql = "SELECT DISTINCT gallery FROM photos WHERE user='$u'";
$query = mysqli_query($db_conx, $sql);
if(mysqli_num_rows($query) < 1){
	$gallery_list = "This user has not uploaded any photos yet.";
} else {
	while ($row = mysqli_fetch_array($query, MYSQLI_ASSOC)) {
		$gallery = $row["gallery"];
		$countquery = mysqli_query($db_conx, "SELECT COUNT(id) FROM photos WHERE user='$u' AND gallery='$gallery'");
		$countrow = mysqli_fetch_row($countquery);
		$count = $countrow[0];
		$filequery = mysqli_query($db_conx, "SELECT filename FROM photos WHERE user='$u' AND gallery='$gallery' ORDER BY RAND() LIMIT 1");
		$filerow = mysqli_fetch_row($filequery);
		$file = $filerow[0];
		$gallery_list .= '<div>';
		$gallery_list .=   '<div onclick="showGallery(\''.$gallery.'\',\''.$u.'\')">';
		$gallery_list .=     '<img src="user/'.$u.'/'.$file.'" alt="cover photo">';
		$gallery_list .=   '</div>';
		$gallery_list .=   '<b>'.$gallery.'</b> ('.$count.')';
		$gallery_list .= '</div>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?php echo $u; ?> Photos</title>
<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="stylesheet" type="text/css" href="styles/styles.css">
<style type="text/css">
form#photo_form{background:#C7CFE2; padding:20px; border-radius: 5px;
			padding:35px;
			border: solid #5978AC;}
div#galleries{}
div#galleries > div{float:left; margin:20px; text-align:center; cursor:pointer;}
div#galleries > div > div {height:100px; overflow:hidden;}
div#galleries > div > div > img{width:150px; cursor:pointer;}
div#photos{display:none; border:#666 1px solid; padding:20px;}
div#photos > div{float:left; width:125px; height:80px; overflow:hidden; margin:20px;}
div#photos > div > img{width:125px; cursor:pointer;}
div#picbox{display:none; padding-top:36px;}
div#picbox > img{max-width:800px; display:block; margin:0px auto;}
div#picbox > button{ display:block; float:right; font-size:36px; padding:3px 16px;}
input[type="submit"]{
	-webkit-user-select: none;
	background: rgb(76, 142, 250);
	border: 0;
	border-radius: 2px;
	box-sizing: border-box;
	color: #fff;
	width:170px;
	cursor: pointer;
	float: center;
	font-size: .875em;
	margin: 0;
	padding: 10px 24px;
	transition: box-shadow 200ms cubic-bezier(0.4, 0, 0.2, 1);
}
#dialogboxgal{
	display:none;
	position:fixed;
	background-color:#FFFFFF;
	width:500px;
	height:215px;	
	z-index:10;
	border:1px solid #AAA;
}
#dialogboxgal > div{
	margin:8px;
}
#dialogboxheadgal{
	padding:10px;
	background-image:url(images/menu_bg.png);
	text-align:center;
	font-size:26px;
	color:#FFF;
}
#dialogboxbodygal{
	padding:20px;
}
input[type="text"]{
	background-color:#FFFFFF;
	border:1px solid #E2E2E2;
	font-size:15px;
	padding:5px;
	width:305px;
	margin-bottom:3px;
	margin-top:3px;
	height:15px;	
}
</style>
<script src="js/main.js"></script>
<script src="js/ajax.js"></script>
<script>
function showGallery(gallery,user){
	_("galleries").style.display = "none";
	_("section_title").innerHTML = user+'&#39;s '+gallery+' Gallery &nbsp; <button onclick="backToGalleries()">Go back to all galleries</button>';
	_("photos").style.display = "block";
	_("photos").innerHTML = 'loading photos ...';
	var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			_("photos").innerHTML = '';
			var pics = ajax.responseText.split("|||");
			for (var i = 0; i < pics.length; i++){
				var pic = pics[i].split("|");
				_("photos").innerHTML += '<div><img onclick="photoShowcase(\''+pics[i]+'\')" src="user/'+user+'/'+pic[1]+'" alt="pic"><div>';
			}
			_("photos").innerHTML += '<p style="clear:left;"></p>';
		}
	}
	ajax.send("show=galpics&gallery="+gallery+"&user="+user);
}
function backToGalleries(){
	_("photos").style.display = "none";
	_("section_title").innerHTML = "<?php echo $u; ?>&#39;s Photo Galleries";
	_("galleries").style.display = "block";
}
function photoShowcase(picdata){
	var data = picdata.split("|");
	_("section_title").style.display = "none";
	_("photos").style.display = "none";
	_("picbox").style.display = "block";
	_("picbox").innerHTML = '<button onclick="closePhoto()">x</button>';
	_("picbox").innerHTML += '<img src="user/<?php echo $u; ?>/'+data[1]+'" alt="photo">';
	if("<?php echo $isOwner ?>" == "yes"){
		_("picbox").innerHTML += '<p id="deletelink"><a href="#" onclick="return false;" onmousedown="deletePhoto(\''+data[0]+'\')">Delete this Photo <?php echo $u; ?></a></p>';
	}
}
function closePhoto(){
	_("picbox").innerHTML = '';
	_("picbox").style.display = "none";
	_("photos").style.display = "block";
	_("section_title").style.display = "block";
}
function deletePhoto(id){
	var conf = confirm("Press OK to confirm the delete action on this photo.");
	if(conf != true){
		return false;
	}
	_("deletelink").style.visibility = "hidden";
	var ajax = ajaxObj("POST", "php_parsers/photo_system.php");
	ajax.onreadystatechange = function() {
		if(ajaxReturn(ajax) == true) {
			if(ajax.responseText == "deleted_ok"){
				alert("This picture has been deleted successfully. We will now refresh the page for you.");
				window.location = "photos.php?u=<?php echo $u; ?>";
			}
		}
	}
	ajax.send("delete=photo&id="+id);
}
</script>
<script>
	function CustomLogin(){
		this.render = function(){
			var winW = window.innerWidth;
			var winH = window.innerHeight;
			var dialogoverlay = document.getElementById('dialogoverlay');
			var dialogbox = document.getElementById('dialogboxgal');
			dialogoverlay.style.display="block";
			dialogoverlay.style.height=winH+"px";
			dialogbox.style.left = (winW/2)-(450 * .5)+"px";
			dialogbox.style.display = "block";
			dialogbox.style.top = "100px";
		}
		this.cancel=function(){
			document.getElementById('dialogboxgal').style.display = "none";
			document.getElementById('dialogoverlay').style.display = "none";
		}
	}	
	var Alert = new CustomLogin();	
</script>
<script>
function checkgalleryname(){
	var gn = _("galleryname").value;
	if(gn != ""){
		_("mypics").style.display = "block";
		_("mypics").src = "images/checking.gif";
		var ajax = ajaxObj("POST", "photos.php");
        ajax.onreadystatechange = function() {
	       	if(ajaxReturn(ajax) == true) {
	            if(ajax.responseText == "It's OK"){
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
        ajax.send("gallerynamecheck="+gn);
	}
}


function showmsg(){                    //this is to show msg for username box
	_("indication").style.display="block";	
}
function hidemsg(){
	_("indication").style.display="none";	//this is for hide msg for usernamr box
}
</script>
</head>
<body>
<?php 
include("includes/header.php");
?>
<div id="page_middle">
<div id="page_middle_content">
  <div id="photo_form"><?php echo $photo_form; ?></div>
  <h2 id="section_title"><?php echo $u; ?>&#39;s Photo Galleries</h2>
  <div id="galleries"><?php echo $gallery_list; ?></div>
  <div id="photos"></div>
  <div id="picbox"></div>
  <p style="clear:left;">These photos belong to <a href="user.php?u=<?php echo $u; ?>"><?php echo $u; ?></a></p>
</div>
</div>
   <div id="dialogoverlay" onClick="Alert.cancel()"></div>
        <div id="dialogboxgal">
        	<div>
            	<div id="dialogboxheadgal">Add a Gallery</div>
                <hr>
                <form enctype="multipart/form-data" method="post" action="php_parsers/photo_system.php">
                <div id="dialogboxbodygal">
                <table border="0">
                	<tr>
                    	<td> New Gallery:</td><td><input type="text" placeholder="Enter the Name of new Gallery" name="newgalleryname" id="galleryname" onBlur="checkgalleryname()"  required ><img id="mypics" src="images\checking.gif" width="25px" height="25px" style="top:47px; left:423px; position:absolute; display:none;"><div id="indication"></div><br /></td>
                  	</tr>
                    <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                    <tr>
                    	<td> Choose a photo:</td><td><input type="file" name="photoname" accept="image/*" required>'</td>
                        <tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr><tr></tr>
                    <tr>
                    	<td colspan="2" align="center"><input type="submit" value="Upload Now"></td>
                    </tr>
                </table>
                </form>
                </div>
          	</div>
     	</div>
<?php
include("includes/footer.php");
?>
</body>
</html>

