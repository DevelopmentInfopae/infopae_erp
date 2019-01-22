<?php

// $Hostname = "192.254.194.178";

// $Username = "infopae_sylvia";

// $Password = "Sylopez18";

// $Database = "infopae_giron2019";

/*
$Hostname = "pruebas.wappsi.com";
$Username = "e6y1o9y1_ricardo";
$Password = "Jm574817";
$Database = "e6y1o9y1_infopae2018";
*/

// $Hostname = "www.wappsi.com";
// $Username = "e6y1o9y1_sylvia";
// $Password = "Sylopez18";
// $Database = "e6y1o9y1_infopae2018";

// $Hostname = "www.wappsi.com";
// $Username = "e6y1o9y1_sylvia";
// $Password = "Sylopez18";
// $Database = "e6y1o9y1_infopae2019";

// $Hostname = "192.254.194.178";
// $Username = "infopae_sylvia";
// $Password = "Sylopez18";
<<<<<<< HEAD
=======
// $Database = "infopae_giron2019";

// $Hostname = "192.254.194.178";
// $Username = "infopae_sylvia";
// $Password = "Sylopez18";
>>>>>>> master
// $Database = "infopae_infopae2019";

$Hostname = "localhost";
$Username = "root";
$Password = "asd123";
$Database = "e6y1o9y1_infopae2019";


$Link = new mysqli($Hostname, $Username, $Password, $Database);

if ($Link->connect_errno) {

	echo "Fallo al contenctar a MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;

}

$Link->set_charset("utf8");