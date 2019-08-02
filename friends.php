<!DOCTYPE Html>
<html>
	<head>
    	<meta charset="utf-8">
		<title>Friend Buzz</title>
        <link rel="stylesheet" type="text/css" href="styles/styles.css">
        <script src="js/main.js"></script>
        <script src="js/ajax.js"></script>
        <script src="js/expand_retract.js"></script>
        <script src="js/autoscroll.js"></script>
        <script src="js/fadeeffect.js"></script>
	</head>
<body>
<?php 
include("includes/header.php");
?>
    <div id="page_middle">
    	<div id="page_middle_content">
       	  	<div id="cover_pic">
            	<img src="images/timelineimage.jpg" width="1000px" height="245px" >
            </div>
            <div id="profile_pic">
            	<img src="images/default_pic.jpg" width="160px" height="180px">
            </div>
            <div id="profile_pic_rest">
            	<div id="profile_menu_rest">
            		<span>Mayank Sharma</span>
            		<input type="button" name="add as friend" value="Add as friend">
            		<input type="button" name="msg" value="Message">
            	</div>
           		<div id="profile_menu">
                	<div id="menu2">
                		<span style="font-size:70px;position:relative;top:-26px;font-family:Edwardian Script ITC">|</span>
                        <a href="timeline.php">Timeline</a><span style="font-size:70px;position:relative;top:-26px;font-family:Edwardian Script ITC">|</span>
                		<a href="about.php">About</a><span style="font-size:70px;position:relative;top:-26px;font-family:Edwardian Script ITC">|</span>
                		<a href="friends.php" class="active">Friends</a><span style="font-size:70px;position:relative;top:-26px;font-family:Edwardian Script ITC">|</span>
               	 		<a href="gallary.php">Gallary</a><span style="font-size:70px;position:relative;top:-26px;font-family:Edwardian Script ITC">|</span>       
                	</div>
            	</div>
            </div> 
   
   
   
    	<div id="box">
        	<div id="box_head">
            	<div id="box_head_title">
                	Friends
                </div>
            </div>
            <div id="friends">
            </div>
            <div id="friends">
            </div>
            <div id="friends">
            </div>
            <div id="friends">
            </div>
        </div>



        </div>
    </div>
    
<?php
include("includes/footer.php");
?>
    	