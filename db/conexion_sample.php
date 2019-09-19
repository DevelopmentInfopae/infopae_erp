<?php

$Hostname = "192.254.194.178";

$Username = "infopae_sylvia";

$Password = "Sylopez18";

$Database = "infopae_infopae2019";



$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");