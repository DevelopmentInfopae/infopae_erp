<?php

// $Hostname = "198.57.248.97";
// $Username = "infopae_sylvia";
// $Password = "Sylopez19";
// $Database = "infopae_soberano";

// $Hostname = "192.254.194.178";
// $Username = "infopae_sylvia";
// $Password = "Sylopez18";
// $Database = "infopae_giron2019";

$Hostname = "192.254.134.21";
$Username = "infopaes_sylvia";
$Password = "Sylopez19";
$Database = "infopaes_sadelante2";



$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");