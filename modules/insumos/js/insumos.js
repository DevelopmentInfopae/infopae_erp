var numMedida = $('.unidadMedidaPresentacion').length;

var numProducto = $('.productodesp').length;
var numProductoInicial = $('.productodesp').length;

$(document).ready(function(){
	jQuery.extend(jQuery.validator.messages, {//Configuración jquery valid
	    required: "Este campo es obligatorio.",
	    remote: "Por favor, rellena este campo.",
	    email: "Por favor, escribe una dirección de correo válida",
	    url: "Por favor, escribe una URL válida.",
	    date: "Por favor, escribe una fecha válida.",
	    dateISO: "Por favor, escribe una fecha (ISO) válida.",
	    number: "Por favor, escribe un número entero válido.",
	    digits: "Por favor, escribe sólo dígitos.",
	    creditcard: "Por favor, escribe un número de tarjeta válido.",
	    equalTo: "Por favor, escribe el mismo valor de nuevo.",
	    accept: "Por favor, escribe un valor con una extensión aceptada.",
	    maxlength: jQuery.validator.format("Por favor, no escribas más de {0} caracteres."),
	    minlength: jQuery.validator.format("Por favor, no escribas menos de {0} caracteres."),
	    rangelength: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1} caracteres."),
	    range: jQuery.validator.format("Por favor, escribe un valor entre {0} y {1}."),
	    max: jQuery.validator.format("Por favor, escribe un valor menor o igual a {0}."),
	    min: jQuery.validator.format("Por favor, escribe un valor mayor o igual a {0}.")
	});

	 toastr.options = {
      "closeButton": true,
      "debug": false,
      "progressBar": true,
      "preventDuplicates": false,
      "positionClass": "toast-top-right",
      "onclick": null,
      "showDuration": "3000",
      "hideDuration": "1000",
      "timeOut": "7000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }

    if (numProductoInicial == 0 || numProductoInicial == "" || numProductoInicial == null) {
        $.ajax({
         type: "POST",
         url: "functions/fn_insumos_obtener_productos_para_despacho.php",
         data : {"num_producto" : numProducto},
         beforeSend: function(){},
         success: function(data){
          $('#productosDesp').append(data);
          $('.productodesp:last').select2();
         }
       });
    }
});


function submitForm(form, accion){
	if ($('#'+form).valid()) {
		$('#loader').fadeIn();
		var datos = $('#'+form).serialize();

		if (accion == 1) {
			$.ajax({
			    type: "POST",
			    url: "functions/fn_insumos_insertar_insumo.php",
			    data: datos,
			    beforeSend: function(){},
			    success: function(data){
			    	if (data == "1") {
			    		Command: toastr.success("Creado con éxito.", "Creado", {onHidden : function(){
			      				location.reload();}})
			    	} else {
			    		Command: toastr.error("Ha ocurrido un error al crear.", "Error al crear", {onHidden : function(){
			      				console.log(data);}})
			    	}
			    }
			  });
		} else if (accion == 2) {
			$.ajax({
			    type: "POST",
			    url: "functions/fn_insumos_editar_insumo.php",
			    data: datos,
			    beforeSend: function(){},
			    success: function(data){
			    	data = JSON.parse(data);
			    	if (data.respuesta[0].exitoso == "1") {
			    		Command: toastr.success("Actualizado con éxito.", "Actualizado", {onHidden : function(){
			      				$('#codigoinsumover').val(data.respuesta[0].codigo);
			      				$('#ver_insumo').submit();
			      			}})
			    	} else {
			    		Command: toastr.error("Ha ocurrido un error al actualizar.", "Error al actualizar", {onHidden : function(){
			      				console.log(data);}})
			    	}
			    }
			  });
		}

	}
}

 $('#tablaInsumos tbody td:nth-child(-n+4)').on('click', function(){
    $('#codigoinsumover').val($(this).parent().attr("codigoinsumo"));
    $('#ver_insumo').submit();
  });

$('#descripcion').on('keyup', function(){
    $.ajax({
      type: "POST",
      url: "functions/fn_insumos_validar_nombre_producto.php",
      data: {"descripcion" : $(this).val()},
      beforeSend: function(){},
      success: function(data){
        console.log("Valida nombre : "+data);
        if (data == "1") {
          $('#existeDesc').css('display', '');
          $('.guardar').attr('disabled', true);
          $(this).css('border-color', '#cc5965');
        } else {
          $('#existeDesc').css('display', 'none');
          $('.guardar').removeAttr('disabled');
          $(this).css('border-color', '');
        }
      }
    });
});

$('#modalEliminar').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      codigoProducto = button.data('codigo');
      $('#codigoProductoEli').val(codigoProducto);
});

function editarInsumo(codIns){
	$('#codigoinsumoeditar').val(codIns);
	$('#editar_insumo').submit();
}

function eliminarProducto(){
  $('#modalEliminar').modal('hide');
  $('#loader').fadeIn();
  var codigoProducto = $('#codigoProductoEli').val();

 $.ajax({
  type: "POST",
  url: "functions/fn_insumos_eliminar_insumo.php",
  data: {"codigoProducto" : codigoProducto},
  beforeSend: function(){},
  success: function(data){
  console.log(data);
    data = JSON.parse(data);
    if (data == "1") {
      Command: toastr.success("Eliminado con éxito.", "Eliminado", {onHidden : function(){
			      				location.href="index.php";}})
    } else {
      Command: toastr.error("Ha ocurrido un error al actualizar.", "Error al actualizar", {onHidden : function(){
			      				console.log(data);}})
    }
  }
});
}

$('#unidadMedida').change(function(){
  borrarMedidas();
  var unidadMedida = $('#unidadMedida').val();
  if (unidadMedida == "g" || unidadMedida == "cc") {
  // $('#gestionMedidas').css('display', '');
    if (unidadMedida == "g") {
        $('#unidadMedidaPresentacion').html('<option value="u">Unidad</option><option value="kg">KiloGramo</option><option value="lb">Libra</option><option value="g">Gramos</option>');
    } else if (unidadMedida == "cc") {
       $('#unidadMedidaPresentacion').html('<option value="u">Unidad</option><option value="lt">Litro</option><option value="cc">Centimetros cúbicos</option>');
    }
    // $('#divCantPresentacion').css('display', '');
    $('#divUnidadMedidaPresentacion').css('display', '');
  	$('#cantPresentacion').val('');
  	$('#cantPresentacion').removeAttr('readonly');
  } else if (unidadMedida == "u"){
    $('#unidadMedidaPresentacion').html('<option value="u">Unidad</option>');
  	$('#cantPresentacion').val(1);
  	$('#cantPresentacion').attr('readonly', true);
  	$('#gestionMedidas').css('display', 'none');
    // $('#divCantPresentacion').css('display', '');
    $('#divUnidadMedidaPresentacion').css('display', 'none');
  }
});

$('#unidadMedidaPresentacion').change(function(){
  var unidadMedidaPresentacion = $('#unidadMedidaPresentacion').val();
  if (unidadMedidaPresentacion == "g" || unidadMedidaPresentacion == "cc" || unidadMedidaPresentacion == "u") {
    // $('#divCantPresentacion').css('display', '');
    $('#cantPresentacion').val('').removeAttr('readOnly');
    $('#gestionMedidas').css('display', '');
    $('#cantPresentacion').val('').removeAttr('readOnly');
  } else if (unidadMedidaPresentacion == "lb" || unidadMedidaPresentacion == "kg" || unidadMedidaPresentacion == "lt"){
    $('#cantPresentacion').val('1').attr('readOnly', true);
    borrarMedidas();
  }
});

function anadirMedida(){
  var unidadMedidaPrincipal = $('#unidadMedida').val();
  options = '<option>No disponible</option>';

  if (numMedida < 4 && validarMedida()) {
    numMedida++;

    if (unidadMedidaPrincipal == "g") {
      options = '<option value="g">Gramos</option>';
      input = '<div class="form-group col-sm-3"><label>Cantidad presentación '+numMedida+'</label><input type="number" name="cantPresentacion['+numMedida+']" id="cantPresentacion'+numMedida+'" class="form-control" onkeyup="validaCantPresentacion('+numMedida+');" required><em id="msgcp'+numMedida+'" style="display: none;">Ordenar de mayor a menor</em></div></div>';
    } else if (unidadMedidaPrincipal == "cc") {
      options = '<option value="cc">Centímetros Cúbicos</option>';
      input = '<div class="form-group col-sm-3"><label>Cantidad presentación '+numMedida+'</label><input type="number" name="cantPresentacion['+numMedida+']" id="cantPresentacion'+numMedida+'" class="form-control" onkeyup="validaCantPresentacion('+numMedida+');" required><em id="msgcp'+numMedida+'" style="display: none;">Ordenar de mayor a menor</em></div></div>';
    }

    $('#unidadMedida').attr('onchange', '$(this).val(\''+$('#unidadMedida').val()+'\');');
    console.log(numMedida);
    html = '<div id="medida_'+numMedida+'"><div class="form-group col-sm-3"><label>Unidad Medida presentación '+numMedida+'</label><select class="form-control unidadMedidaPresentacion" name="unidadMedidaPresentacion['+numMedida+']" id="unidadMedidaPresentacion'+numMedida+'" required>'+options+'</select></div>'+input;
    $('#medidasPresentacion').append(html);
  }
}

function quitarMedida(){
    $('#medida_'+numMedida).remove();
    if (numMedida > 1) {
      numMedida--;
      if (numMedida == 1) {
        $('#unidadMedida').removeAttr('onchange');
      }
    }
    console.log(numMedida);
}

function borrarMedidas(){
  $('#gestionMedidas').css('display', 'none');
    for(var i = 1; i <= numMedida; i++){
      $('#medida_'+i).remove();
      $('#unidadMedida').removeAttr('onchange');
    }
    numMedida = 1;
}

function validarMedida(){
  if ($('#unidadMedida').val() != "u" && $('#unidadMedida').val() != ""){
    return true;
  } else {
    return false;
  }
}

function validaCantPresentacion(num){
  var valida = 0;
  if (num > 1) {
    input = "#cantPresentacion"+num;
  } else if (num == 1) {
    input = "#cantPresentacion";
  }
  for (var i = num+1; i <= numMedida; i++) {
    if (parseInt($(input).val()) <= parseInt($('#cantPresentacion'+i).val())) {
      $(input).val('').focus();
      valida++;
    }
  }
  for (var i = num-1; i >= 1; i--) {
    if (i > 1) {
      input2 = "#cantPresentacion"+i;
    } else if (i == 1) {
      input2 = "#cantPresentacion";
    }
    if (parseInt($(input).val()) >= parseInt($(input2).val())) {
      $(input).val('').focus();
      valida++;
    }
  }
  if (valida > 0) {
    $('#msgcp'+num).css('display', '');
    var heights = $(".col-sm-3").map(function() {
        return $(this).height();
    }).get(),
    maxHeight = Math.max.apply(null, heights);
    $(".col-sm-3").height(maxHeight);

  } else {
    $('#msgcp'+num).css('display', 'none');
  }
}

//JS PARA DESPACHOS DE INSUMOS

$('#tipo_despacho').on('change', function(){
  $('#nomTipoMov').val($('#tipo_despacho option:selected').text());
  $('#loader').fadeIn();
  var tdoc = $('#tipo_despacho option:selected').text();
  $.ajax({
     type: "POST",
     url: "functions/fn_insumos_obtener_responsables.php",
     data: { "tipo_documento" : tdoc },
     beforeSend: function(){},
     success: function(data){
       $('#proveedor').html(data);
       $('#loader').fadeOut();
     }
   });
});

$('#municipio_desp').on('change', function(){
  $('#loader').fadeIn();
  var cod_municipio = $(this).val();
  $.ajax({
     type: "POST",
     url: "functions/fn_insumos_obtener_instituciones.php",
     data: { "cod_municipio" : cod_municipio },
     beforeSend: function(){},
     success: function(data){
       $('#institucion_desp').html(data);
       $('#loader').fadeOut();
     }
   });
});

$('#institucion_desp').on('change', function(){
  $('#loader').fadeIn();
  var cod_inst = $(this).val();
  $.ajax({
     type: "POST",
     url: "functions/fn_insumos_obtener_sedes.php",
     data: { "cod_inst" : cod_inst },
     beforeSend: function(){},
     success: function(data){
       $('#sede_desp').html(data);
       $('#loader').fadeOut();
     }
   });
});


function añadirSedes(){
  $('#loader').fadeIn();
  var ruta = $('#ruta_desp').val();
      nom_municipio = $('#municipio_desp option:selected').text();
      municipio = $('#municipio_desp').val();
      cod_inst = $('#institucion_desp').val();
      cod_sede = $('#sede_desp').val();

      $.ajax({
       type: "POST",
       url: "functions/fn_insumos_armar_tabla.php",
       data: { "ruta" : ruta, "municipio" : municipio, "cod_inst" : cod_inst, "cod_sede" : cod_sede, "nom_municipio" : nom_municipio},
       beforeSend: function(){},
       success: function(data){
        $('#tbodySedesDespachos').append(data);
        $('#loader').fadeOut();
       }
     });

}

function seleccionarTodos(check){
  if ($(check).prop('checked')==true) {
    $('.checkInst').each(function(){
      $(this).prop('checked', true);
    });
  } else if ($(check).prop('checked')==false){
     $('.checkInst').each(function(){
      $(this).prop('checked', false);
    });
  }
}

function eliminarSedes(){
  $('.checkInst:checked').each(function(){
      $(this).parents('tr').remove();
    });

  $('#seleccionar_todos').prop('checked', false);
}

$('#proveedor').on('change', function(){
  proveedor = $(this).val();
  $('#nombre_proveedor').val($('#proveedor option:selected').text());
  tipodespacho = $('#tipo_despacho').val();
  $.ajax({
       type: "POST",
       url: "functions/fn_insumos_obtener_bodega_origen.php",
       data: { "proveedor" : proveedor, "tipodespacho" : tipodespacho},
       beforeSend: function(){},
       success: function(data){
        $('#bodega_origen').html(data);
       }
     });
});

function anadirProducto(){
  numProducto++;

  $.ajax({
   type: "POST",
   url: "functions/fn_insumos_obtener_productos_para_despacho.php",
   data : {"num_producto" : numProducto},
   beforeSend: function(){},
   success: function(data){
    $('#productosDesp').append(data);
    $('.productodesp:last').select2();
   }
  });

}

function borrarProducto(){
  if (numProducto > numProductoInicial) {
    $('#producto_'+numProducto).remove();
    numProducto--;
  }
}

function validaProductos(select, num){
  var produc = $(select).val();
  $('#descIns_'+num).val($("#"+$(select).prop('id')+" option:selected").text());

      cnt=0;
  $('.productodesp').each(function(){
    if ($(this).val() == produc) {
      cnt++;
    }
  });
  setTimeout(function() {
    if (cnt>1) {
      $(select).val('').focus();
      Command: toastr.warning("No puede escoger el mismo Insumo dos veces para el mismo despacho.", "Insumo escogido dos veces.", {onHidden : function(){ }})
    }
  }, 800);
}

function submitDespacho(accion){

      if (accion == 1) {
        urlDespacho = "functions/fn_insumos_generar_despacho.php";
        titleNotif = "Despachado con éxito.";
        descNotif = "El despacho se registró con éxito.";
      } else if (accion == 2) {
        urlDespacho = "functions/fn_insumos_editar_despacho.php";
        titleNotif = "Actualizado con éxito.";
        descNotif = "El despacho se actualizó con éxito.";
      }

      mesErr = "";
      sedes = [];
      meses = [];
      productos = [];

      $('input[name="sede[]"]').each(function(){
        if ($(this).prop('checked')) {
          sedes.push($(this).val());
        }
      });
      $('select[name="meses_despachar[]"] option:selected').each(function(){
        meses.push($(this).val());
      });
      $('select[name="productoDespacho[]"]').each(function(){
          productos.push($(this).val());
      });

      valida = 0;

      $('input[name="sede[]"]').each(function(){
        if ($(this).prop('checked')) {
          valida++;
        }
      });

  if (valida > 0) {
      if ($('#formDespachoInsumo').valid()) {

        $('#loader').fadeIn();

        $.ajax({
         type: "POST",
         url: "functions/fn_insumos_validar_despacho.php",
         data : {"sedes" : JSON.stringify(sedes), "meses" : JSON.stringify(meses), "productos" : JSON.stringify(productos)},
         beforeSend: function(){},
         success: function(data){

          data = JSON.parse(data);

          if (data.respuesta[0].respuesta == "1") {
              btn = '<button type="button" class="close" onclick="$(\'#errDespachos\').fadeOut();" aria-label="Close">'+
                      '<span aria-hidden="true">&times;</span>'+
                    '</button>';
               Command: toastr.error("Se encontró una inconsistencia durante la validación del despacho.", "Inconsistencia encontrada.", {onHidden : function(){ $('#loader').fadeOut();}})
               $('#errDespachos').html(btn+"<b>Detalles de inconsistencia : </b> </br>"+data.respuesta[0].coincide);
               $('#errDespachos').show();
          } else {
              Command: toastr.info("El proceso de validación y registro de despacho es un poco extenso, por favor espere.", "Aguarde un momento.", {onHidden : function(){}})

              if (data.respuesta[0].coincide.length > 1) {
                Command: toastr.warning("Se encontró una inconsistencia con las manipuladoras, pero no afecta al despacho.", "Inconsistencia de manipuladoras.", {onHidden : function(){}})
                $('#errDespachos').html("<b>Detalles de inconsistencia : </b> </br>"+data.respuesta[0].coincide);
                $('#errDespachos').show();
              }

              datos = $('#formDespachoInsumo').serialize();
              $.ajax({
               type: "POST",
               url: urlDespacho,
               data : datos,
               beforeSend: function(){},
               success: function(data){
                if (data == "1") {
                  Command: toastr.success(descNotif, titleNotif, {onHidden : function(){ location.reload();}})
                } else if (data == "0") {
                  Command: toastr.error("Hubo un error al registrar el despacho.", "Ocurrió un error.", {onHidden : function(){ $('#loader').fadeOut();}})
                } else {
                  Command: toastr.error("Hubo un error al registrar el despacho.", "Ocurrió un Error.", {onHidden : function(){ console.log(data); }})
                  $('#loader').fadeOut();
                }
               }
              });
          }
         }
        });
      }
  } else {
    Command: toastr.warning("Debe seleccionar al menos una sede para generar el despacho.", "No hay sedes seleccionadas.", {onHidden : function(){ $('#loader').fadeOut();}})
  }
}

$('#modalEliminarProductoDespacho').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      iddet = button.data('iddet');
      numdoc = button.data('numdoc');
      mestabla = button.data('mestabla');
      numdet = button.data('numdet');
      $('#id_det_despacho').val(iddet);
      $('#numdoc_eliminar_det').val(numdoc);
      $('#mes_tabla_eliminar_det').val(mestabla);
      $('#num_det').val(numdet);
      if ($('.productodesp').length == 1) {
        $('#mensajeConfirm').css('display','');
        $('#tipoCabecera').removeClass('text-info').addClass('text-warning');
        $('#tipoBoton').removeClass('btn-primary').addClass('btn-warning');
      }
});

function eliminarProductoDespacho(){
    $('#modalEliminarProductoDespacho').modal('hide');
    $('#loader').fadeIn();
    var iddet = $('#id_det_despacho').val();
       numdoc = $('#numdoc_eliminar_det').val();
       mestabla = $('#mes_tabla_eliminar_det').val();
       num_det = $('#num_det').val();
     $.ajax({
      type : "POST",
      url : "functions/fn_insumos_eliminar_producto_despacho.php",
      data : {"iddet" : iddet, "num_doc" : numdoc, "mestabla" : mestabla},
      success: function(data){
        if (data == "1") {
          if ($('.productodesp').length == 1) {
            location.href='../trazabilidad_insumos/despachos.php';
          } else {
            $('#producto_'+num_det).remove();
            $('#loader').fadeOut();
          }
        } else {
           Command: toastr.error("Hubo un error al tratar de eliminar el producto.", "Ocurrió un error.", {onHidden : function(){ console.log(data) }})
           $('#loader').fadeOut();
        }
      }
     });
}

$('#tipo_conteo').on('change', function(){
  val = $(this).val();

  if (val == "04") {
    $('#divCantPresentacion').css('display', 'none');
    $('#cantidadMes').removeAttr('required');
    $('#cantidadMes').val('');
  } else {
    $('#divCantPresentacion').css('display', '');
    $('#cantidadMes').prop('required', true);
  }

});
