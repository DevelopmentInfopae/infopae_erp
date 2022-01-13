<?php

$Hostname = "162.240.16.254";

$Username = "infopaed_sylvia";

$Password = "Sylopez20";

$Database = "infopaed_demo";



$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");