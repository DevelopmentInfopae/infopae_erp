<?php
session_start();//para saber cual es la sesion a destruir


session_destroy();//con esto destruyes la sesion
echo "<script type='text/javascript'>
       	window.location.href= 'index.php';
      </script>";
 