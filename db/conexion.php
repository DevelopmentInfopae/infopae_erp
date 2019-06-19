<?php

$Hostname = "localhost";

$Username = "root";

$Password = "root";

$Database = "infopae_2019";



$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");