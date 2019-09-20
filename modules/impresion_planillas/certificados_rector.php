<?php
if(isset($_POST['tipoPlanilla']) && $_POST['tipoPlanilla'] == 1){
	include 'certificado_rector.php';
}
else if(isset($_POST['tipoPlanilla']) && $_POST['tipoPlanilla'] == 2){
	include 'certificado_dias.php';
}