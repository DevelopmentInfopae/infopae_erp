<?php
	$Hostname = "www.wappsi.com";
	$Username = "e6y1o9y1_sylvia";
	$Password = "Sylopez18";
	$Database = "e6y1o9y1_infopae2019";

	$Link = new mysqli($Hostname, $Username, $Password, $Database);
	if ($Link->connect_errno) {
	  echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
	}
	$Link->set_charset("utf8");