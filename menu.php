<?php
	/*
	0	SuperAdministrador
	1	Administrador
	2	Operario
	3	Auxiliar
	4	Manipuladora
	5	Auditor
	6	Rector
	7	Coordinador
	8	Aux Asistencia
	*/

	$consultaMenuSide = " SELECT 	modulo, 
									sub_modulo, 
									nivel, 
									nombre, 
									ruta, 
									label, 
									permisos, 
									icon,
									nombre_submodulo
							FROM menu_sidebar 
							ORDER BY modulo, sub_modulo, nivel
							";
	$respuestaMenuSide = $Link->query($consultaMenuSide);
	if ($respuestaMenuSide->num_rows > 0) {
		while ($dataMenuSide = $respuestaMenuSide->fetch_assoc()) {
			$MenuSide[$dataMenuSide['modulo']][] = $dataMenuSide;
		}
	}	
	// exit(var_dump($MenuSide));
?>

<li class='li_inicio' >
	<a href="<?php echo $baseUrl; ?>"><i class="fa fa-home fa-lg"></i> <span class="nav-label">Inicio</span></a>
</li>
<?php
	$banderaUl2 = 0;
	foreach ($MenuSide as $key => $value) {
		if ($nombreP = $value[0]['permisos'] == 'instituciones' && $nombreP = $value[0]['nombre'] == 'sedes' ) {
			$nombreP = 'sedes';
		}else{
			$nombreP = $value[0]['permisos'];
		}
		$li = "<li class='li_$nombreP'>";
		$ul = "<ul class='nav nav-second-level collapse'>";
		$ul2 = "<ul class='nav nav-third-level collapse'>";
		$banderaUl = 0;
		$banderaUl2 = 0;
		
		foreach ($value as $key2 => $value2) {
			$permiso = $value2['permisos'];
			if ($permisos[$permiso] == 1 || $permisos[$permiso] == 2) {
				$nivel = $value2['nivel'];
				$nombre = $value2['nombre'];
				$ruta = $value2['ruta'];
				$label = $value2['label'];
				$icon = $value2['icon'];
				$nombreSubmodulos = $value2['nombre_submodulo'];
				if ($nivel == 1) {
					if ($ruta == '') {
						$li .= "<a href='#'>
									<i class='$icon'></i> 
									<span class='nav-label'>$label</span>
									<span class='fa arrow'></span>
								</a>";
	
					}else if ($ruta != '') {
						$li .= "<a href='$baseUrl$ruta'>
									<i class='$icon'></i> 
									<span class='nav-label'>$label</span>
								</a>";
					}
				}if ($nivel == 2) {
					if ($banderaUl2 == 1) {
						$ul2 .= "</ul>";
						$ul .= $ul2."</li>";
						$banderaUl2 = 0;
						$ul2 = "<ul class='nav nav-third-level collapse'>";
					}
					if ($ruta == '') {
						$ul .= "<li class = 'li_$nombreSubmodulos'>
									<a href='#'> $label 
										<span class='fa arrow'></span> 
									</a>
								";
					}else if ($ruta != '') {
						if ($ruta != '#') {
							if ($nombreSubmodulos == 'menus') {
								$nombreSubmodulos = 'menus_sub';
							}
							if ($nombre == 'despachos') {
								if ($nombreSubmodulos == 'alimentos') {
									$nombreSubmodulos = 'despacho_alimentos';
								}
							}
							if ($nombreSubmodulos == 'asistencia') {
								$nombreSubmodulos = 'asistencia_submenu';
							}
							$ul .= "<li class='li_$nombreSubmodulos'>";
							$ul .= " <a  href='$baseUrl$ruta'> "; 
							$ul .= "$label 
										</a>
									</li>";		
							$banderaUl = 1;	
						}else if ($ruta == '#') {
							$ul .= '<li>';
							$ul .= " <a  href='$baseUrl$ruta' class = 'sinDesarrollar' >"; 
							$ul .= "$label 
										</a>
									</li>";		
							$banderaUl = 1;	
						}
					
					}
				}if ($nivel == 3) {
					if ($ruta != '#') {
						if ($nombreSubmodulos == 'controlAsistencia') {
							$nombreSubmodulos = 'controlAsistencia_submenu';
						}
						if ($nombreSubmodulos == 'focalizacion') {
							$nombreSubmodulos = 'focalizacion_submenu';
						}
						if ($nombreSubmodulos == 'suplentes') {
							$nombreSubmodulos = 'suplentes_novedades';
						}
						if ($nombreSubmodulos == 'rutas') {
							$nombreSubmodulos = 'rutas_submenu';
						}
						$banderaUl2 = 1;
						$ul2 .= "<li class='li_$nombreSubmodulos'>";
						$ul2 .= " <a  href='$baseUrl$ruta'> "; 
						$ul2 .= "$label	
									</a>
								</li>";	
					}else if ($ruta == '#') {
						$banderaUl2 = 1;
						$ul2 .= "<li>";
						$ul2 .= " <a  href='$baseUrl$ruta' class = 'sinDesarrollar'> "; 
						$ul2 .= "$label	
									</a>
								</li>";	
					}					
				}	
			}	
		}
		if($banderaUl == 1) { $ul .= "</ul>"; $li .= $ul; }
		$li .= "</li>";	
		echo $li;
	}
?>

<li>
	<a href="<?php echo $baseUrl; ?>/cerrar_sesion.php"><i class="fas fa-sign-out-alt fa-lg"></i> <span class="nav-label">Cerrar Sesión</span></a>
</li>

