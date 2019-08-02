<?php
$db_conx = mysqli_connect("localhost","root","","friendbuzz");

	if(mysqli_connect_errno()){
		echo mysql_connect_error();
		exit();
	}

?>