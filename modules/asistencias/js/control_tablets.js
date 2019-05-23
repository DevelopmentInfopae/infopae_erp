$(document).ready(function(){
	$('#btnBuscarControl').click(function(){
		if($('#form_control_asistencia').valid()){
			$('#form_control_asistencia').submit()	
		}
	});	
});