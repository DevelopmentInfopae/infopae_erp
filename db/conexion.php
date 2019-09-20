<?php

$Hostname = "192.254.134.21";
// $Hostname = "198.57.248.97";

$Username = "infopaes_sylvia";
// $Username = "infopae_sylvia";

$Password = "Sylopez19";

$Database = "infopaes_sadelante2";
// $Database = "infopae_fupadeso";



$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");