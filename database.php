<?php
include_once("includes/db_conx.php");

$tbl_users = "CREATE TABLE IF NOT EXISTS user (
              id INT(11) NOT NULL AUTO_INCREMENT,
			  f_name VARCHAR(100) NOT NULL,
			  l_name VARCHAR(100) NOT NULL,
			  u_name VARCHAR(100) NOT NULL,
			  u_email VARCHAR(100) NOT NULL,
			  u_pass VARCHAR(100) NOT NULL,
			  u_gen ENUM('m','f') NOT NULL,
			  u_country VARCHAR(50) NULL,
			  u_bir_date DATE NOT NULL,
			  signup_date DATE NOT NULL,
			  lastlogin_date DATE NOT NULL,
			  avatar VARCHAR(255) NULL,
			  timeline VARCHAR(255) NULL,
			  ip VARCHAR(255) NOT NULL,
			  notescheck DATETIME NOT NULL,
              PRIMARY KEY (id),
			  UNIQUE KEY username (username,email)
             )";
$query = mysqli_query($db_conx, $tbl_users);
if ($query === TRUE) {
	echo "<h3>user table created OK :) </h3>"; 
} else {
	echo "<h3>user table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_useroptions = "CREATE TABLE IF NOT EXISTS useroptions ( 
                id INT(11) NOT NULL,
                username VARCHAR(16) NOT NULL,
				background VARCHAR(255) NOT NULL,
				question VARCHAR(255) NULL,
				answer VARCHAR(255) NULL,
                PRIMARY KEY (id),
                UNIQUE KEY username (username) 
                )"; 
$query = mysqli_query($db_conx, $tbl_useroptions); 
if ($query === TRUE) {
	echo "<h3>useroptions table created OK :) </h3>"; 
} else {
	echo "<h3>useroptions table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_friends = "CREATE TABLE IF NOT EXISTS friends ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                user1 VARCHAR(16) NOT NULL,
                user2 VARCHAR(16) NOT NULL,
                datemade DATETIME NOT NULL,
                accepted ENUM('0','1') NOT NULL DEFAULT '0',
                PRIMARY KEY (id)
                )"; 
$query = mysqli_query($db_conx, $tbl_friends); 
if ($query === TRUE) {
	echo "<h3>friends table created OK :) </h3>"; 
} else {
	echo "<h3>friends table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_blockedusers = "CREATE TABLE IF NOT EXISTS blockedusers ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                blocker VARCHAR(16) NOT NULL,
                blockee VARCHAR(16) NOT NULL,
                blockdate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_blockedusers); 
if ($query === TRUE) {
	echo "<h3>blockedusers table created OK :) </h3>"; 
} else {
	echo "<h3>blockedusers table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_status = "CREATE TABLE IF NOT EXISTS status ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                osid INT(11) NOT NULL,
                account_name VARCHAR(16) NOT NULL,
                author VARCHAR(16) NOT NULL,
                type ENUM('a','b','c') NOT NULL,
                data TEXT NOT NULL,
                postdate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_status); 
if ($query === TRUE) {
	echo "<h3>status table created OK :) </h3>"; 
} else {
	echo "<h3>status table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_photos = "CREATE TABLE IF NOT EXISTS photos ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                user VARCHAR(16) NOT NULL,
                gallery VARCHAR(16) NOT NULL,
				filename VARCHAR(255) NOT NULL,
                description VARCHAR(255) NULL,
                uploaddate DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_photos); 
if ($query === TRUE) {
	echo "<h3>photos table created OK :) </h3>"; 
} else {
	echo "<h3>photos table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_notifications = "CREATE TABLE IF NOT EXISTS notifications ( 
                id INT(11) NOT NULL AUTO_INCREMENT,
                username VARCHAR(16) NOT NULL,
                initiator VARCHAR(16) NOT NULL,
                app VARCHAR(255) NOT NULL,
                note VARCHAR(255) NOT NULL,
                did_read ENUM('0','1') NOT NULL DEFAULT '0',
                date_time DATETIME NOT NULL,
                PRIMARY KEY (id) 
                )"; 
$query = mysqli_query($db_conx, $tbl_notifications); 
if ($query === TRUE) {
	echo "<h3>notifications table created OK :) </h3>"; 
} else {
	echo "<h3>notifications table NOT created :( </h3>"; 
}
/////////////////////////////////////////
$tbl_message = "CREATE TABLE IF NOT EXISTS messages ( 
				message_id INT(8) NOT NULL AUTO_INCREMENT ,  
				s_uname VARCHAR(18) NOT NULL ,  
				r_uname VARCHAR(18) NOT NULL ,  
				message TEXT NOT NULL ,  
				timesent DATETIME NOT NULL ,  
				sdelete ENUM('0','1') NOT NULL ,  
				rdelete ENUM('0','1') NOT NULL ,    
				PRIMARY KEY  (message_id)
				)";
$query = mysqli_query($db_conx, $tbl_message); 
if ($query === TRUE) {
	echo "<h3>messages table created OK :) </h3>"; 
} else {
	echo "<h3>messages table NOT created :( </h3>"; 
}
//////////////////////////////////////////
$tbl_conversation = "CREATE TABLE IF NOT EXISTS conversations ( 
						conversation_id INT(8) NOT NULL AUTO_INCREMENT , 
						conversation_name VARCHAR(128) NOT NULL , 
						PRIMARY KEY (conversation_id)
						)";
$query = mysqli_query($db_conx, $tbl_conversation); 
if ($query === TRUE) {
	echo "<h3>conversations table created OK :) </h3>"; 
} else {
	echo "<h3>conversations table NOT created :( </h3>"; 
}
/////////////////////////////////////////////////
$tbl_conversation_member = "CREATE TABLE IF NOT EXISTS conversations_member ( 
								conversation_id INT(8) NOT NULL ,  
								user_id INT(8) NOT NULL ,  
								conversation_last_view DATETIME NOT NULL ,  
								conversation_deleted ENUM('0','1') NOT NULL 
								)";
$query = mysqli_query($db_conx, $tbl_conversation_member); 
if ($query === TRUE) {
	echo "<h3>conversation member table created OK :) </h3>"; 
} else {
	echo "<h3>conversation member table NOT created :( </h3>"; 
}
///////////////////////////////////////////////
$alter_conversation_member_add_index = "ALTER TABLE conversations_member 
												ADD INDEX( conversation_id, user_id)";
$query = mysqli_query($db_conx, $alter_conversation_member_add_index); 
if ($query === TRUE) {
	echo "<h3>conversation member table altered OK :) </h3>"; 
} else {
	echo "<h3>conversations member table NOT altered :( </h3>"; 
}
//////////////////////////////////////
$alter_conversation_member_add_unique = "ALTER TABLE conversations_member ADD UNIQUE( conversation_id, user_id)";
$query = mysqli_query($db_conx, $alter_conversation_member_add_unique); 
if ($query === TRUE) {
	echo "<h3>conversation member table altered OK :) </h3>"; 
} else {
	echo "<h3>conversations member table NOT altered :( </h3>"; 
}
/////////////////////////////////////

$tbl_conversation_messages = "CREATE TABLE IF NOT EXISTS conversations_messages ( 
									message_id INT(10) NOT NULL AUTO_INCREMENT ,  
									conversation_id INT(8) NOT NULL ,  
									user_id INT(8) NOT NULL ,  
									message_date DATETIME NOT NULL ,  
									message_text TEXT NOT NULL ,    
									PRIMARY KEY  (message_id)
									)";
$query = mysqli_query($db_conx, $tbl_conversation_messages); 
if ($query === TRUE) {
	echo "<h3>conversations messages table created OK :) </h3>"; 
} else {
	echo "<h3>conversations messages table NOT created :( </h3>"; 
}

///////////////////////////////////
$tbl_posts = "CREATE TABLE IF NOT EXISTS posts( 
					post_id INT(8) NOT NULL AUTO_INCREMENT ,  
					poster VARCHAR(20) NOT NULL ,
					postto VARCHAR(20) NOT NULL , 
					post_time DATETIME NOT NULL ,  
					post TEXT NOT NULL ,  
					post_image VARCHAR(100) NOT NULL ,
					type VARCHAR(1) NOT NULL ,  
					likes INT(8) NOT NULL ,  
					dislikes INT(8) NOT NULL ,  
					likefriends VARCHAR(255) NOT NULL ,  
					dislikefriends VARCHAR(255) NOT NULL ,    
					PRIMARY KEY  (post_id)
					)";
$query = mysqli_query($db_conx, $tbl_posts); 
if ($query === TRUE) {
	echo "<h3>posts table created OK :) </h3>"; 
} else {
	echo "<h3>posts table NOT created :( </h3>"; 
}
////////////////////////////////////
$tbl_postscomment = "CREATE TABLE IF NOT EXISTS posts_comment( 
						comment_id INT(10) NOT NULL AUTO_INCREMENT ,  
						post_id INT(8) NOT NULL ,  
						commenter VARCHAR(20) NOT NULL ,  
						comment TEXT NOT NULL ,
						comment_time DATETIME NOT NULL ,  
						comment_like INT(10) NOT NULL ,  
						comment_dislike INT(10) NOT NULL ,  
						comment_like_friends VARCHAR(255) NOT NULL ,  
						comment_dislike_friends VARCHAR(255) NOT NULL ,    
						PRIMARY KEY  (comment_id)
					)";
$query = mysqli_query($db_conx, $tbl_postscomment); 
if ($query === TRUE) {
	echo "<h3>posts comment table created OK :) </h3>"; 
} else {
	echo "<h3>posts comment table NOT created :( </h3>"; 
}
?>
