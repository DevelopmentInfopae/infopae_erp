<?php 

require_once '../../../db/conexion.php';
require_once '../../../config.php';

if (isset($_POST['TipoProductoFT'])) {
	$tipoProducto = $_POST['TipoProductoFT'];
} else {
	$tipoProducto = "";
}

if (isset($_POST['IdFT'])) {
	$IdFT = $_POST['IdFT'];
} else {
	$IdFT = "";
}

if (isset($_POST['idProducto'])) {
	$idProducto = $_POST['idProducto'];
} else {
	$idProducto = "";
}

if (isset($_POST['productoFichaTecnicaDet'])) {
	$productoFichaTecnicaDet = $_POST['productoFichaTecnicaDet'];
} else {
	$productoFichaTecnicaDet = "";
}


if (isset($_POST['cantidadProducto'])) {
	$cantidadProducto = $_POST['cantidadProducto'];
} else {
	$cantidadProducto = "";
}

if (isset($_POST['unidadMedidaProducto'])) {
	$unidadMedidaProducto = $_POST['unidadMedidaProducto'];
} else {
	$unidadMedidaProducto = "";
}

if (isset($_POST['pesoBrutoProducto'])) {
	$pesoBrutoProducto = $_POST['pesoBrutoProducto'];
} else {
	$pesoBrutoProducto = "";
}

if (isset($_POST['pesoNetoProducto'])) {
	$pesoNetoProducto = $_POST['pesoNetoProducto'];
} else {
	$pesoNetoProducto = "";
}

$validaRegistro = 0;

if (($tipoProducto == "01" || $tipoProducto == "02" || $tipoProducto == "04") && $idProducto != 0) {
  if ($tipoProducto == "01" && $IdFT != 0) {
    for ($i=1; $i <= sizeof($productoFichaTecnicaDet); $i++) { 
      $consultaDesc = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
      $resultadoDesc = $Link->query($consultaDesc) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaDesc);
      if ($resultadoDesc->num_rows > 0) {
        if ($row = $resultadoDesc->fetch_assoc()) {
          $descProductoFichaTecnicaDet = $row['Descripcion'];
        }
      }

      $subtipo = substr($productoFichaTecnicaDet[$i], 0, 2);

      if ($subtipo == "04") {
        $tipoalimento = "Industrializados";
      } else if ($subtipo == "03") {
        $tipoalimento = "Alimento";
      } else if ($subtipo == "02") {
        $tipoalimento = "Preparación";
      }

      $sqlFichaTecnicaDet = "insert into fichatecnicadet (Id, codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) values (NULL, '".$productoFichaTecnicaDet[$i]."', '".$descProductoFichaTecnicaDet."', '1', 'u', '0', '".$IdFT."', '0', '0', '0', '".$tipoalimento."', '".$tipoalimento."', '0', '0')";
      if ($Link->query($sqlFichaTecnicaDet) === true) {
        $validaRegistro++;
      } else {
        echo "Error : ".$sqlFichaTecnicaDet;
      }
    }
  } else if ($tipoProducto == "02" && $IdFT != 0) {
    for ($i=1; $i <= sizeof($productoFichaTecnicaDet); $i++) { 
      $consultaDesc = "select Descripcion from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
      $resultadoDesc = $Link->query($consultaDesc) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaDesc);
      if ($resultadoDesc->num_rows > 0) {
        if ($row = $resultadoDesc->fetch_assoc()) {
          $descProductoFichaTecnicaDet = $row['Descripcion'];
        }
      }

      $consultaCantidadUnd2Producto = "select CantidadUnd2 from productos".$_SESSION['periodoActual']." where Codigo = ".$productoFichaTecnicaDet[$i];
      $resultadoCantidadUnd2Producto = $Link->query($consultaCantidadUnd2Producto) or die('Unable to execute query. '. mysqli_error($Link)." ".$consultaCantidadUnd2Producto);
      if ($resultadoCantidadUnd2Producto->num_rows > 0) {
        if ($row = $resultadoCantidadUnd2Producto->fetch_assoc()) {
          $CantidadUnd2 = $row['CantidadUnd2'];
        }
      }

      if ($cantidadProducto[$i] != 0) {
        $factorProducto = $CantidadUnd2/$cantidadProducto[$i];
      } else {
        $factorProducto = 0;
      }

      $sqlFichaTecnicaDet = "insert into fichatecnicadet (Id, codigo, Componente, Cantidad, UnidadMedida, Costo, IdFT, Subtotal, Factor, Estado, Tipo, TipoProducto, PesoBruto, PesoNeto) values (NULL, '".$productoFichaTecnicaDet[$i]."', '".$descProductoFichaTecnicaDet."', '".$cantidadProducto[$i]."', '".$unidadMedidaProducto[$i]."', '0', '".$IdFT."', '0', '".$factorProducto."', '0', 'Alimento', 'Alimento', '".$pesoBrutoProducto[$i]."', '".$pesoNetoProducto[$i]."')";

      if ($Link->query($sqlFichaTecnicaDet) === true) {
        $validaRegistro++;
      } else {
        echo "Error : ".$sqlFichaTecnicaDet;
      }
    }
  } else {
  	echo "Error : tipoProducto = ".$tipoProducto.", IdFT = ".$IdFT;
  }

  if ($validaRegistro == sizeof($productoFichaTecnicaDet)) {
  	echo "1";
  } else {
  	echo "Error al completar los registros.";
  }

} else {
	 echo "Error en tipo producto : ".$tipoProducto;
} ?>