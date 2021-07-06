
var numMedida = $('.unidadMedidaPresentacion').length;
var numProducto = $('.productoFichaTecnicaDet').length;
var numProductoInicial = $('.productoFichaTecnicaDet').length;
var borrarFTD = 0;
var productos;
var tipoDespachos;

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
      "showDuration": "400",
      "hideDuration": "1000",
      "timeOut": "2000",
      "extendedTimeOut": "1000",
      "showEasing": "swing",
      "hideEasing": "linear",
      "showMethod": "fadeIn",
      "hideMethod": "fadeOut"
    }
});

$( "#tipoProductoBuscar" ).change(function() {

    var tipoProducto = $( "#tipoProductoBuscar" ).val();
    $.ajax({
    type: "POST",
    url: "functions/fn_menus_buscar_sub_codigo.php",
    data: {"tipoProducto" : tipoProducto},
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#subtipoProducto').html(data+"<option value=''>Todos</option>");
    }
    });

});

$( "#tipoProducto" ).change(function() {

   if (tipoDespachos == null) {
      $.ajax({
      type: "POST",
      url: "functions/fn_menus_buscar_tipos_despachos.php",
      beforeSend: function(){},
      success: function(data){
        //$('#debug').html(data);
        $('#tipoDespacho').html(data);
      }
      });
   }

  var tipoProducto = $( "#tipoProducto" ).val();
    $.ajax({
    type: "POST",
    url: "functions/fn_menus_buscar_sub_codigo.php",
    data: {"tipoProducto" : tipoProducto},
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      $('#subtipoProducto').html(data);
    }
    });

  if (tipoProducto == "01" || tipoProducto == "02") {
    if (tipoProducto == "01") {
      $('#descripcion').val('Menú ').attr('readOnly', true);
      $('#divTipoDespacho').css('display', 'none');
      $('#tipoDespacho').removeAttr('required');
      $('#divTipoComplemento').css('display', '');
      $('#divSubTipoProducto').css('display', 'none');
      $('#tipoComplemento').attr('required', true);
      $('#subtipoProducto').removeAttr('required');
    } else if (tipoProducto == "02") {
      $('#descripcion').val('').removeAttr('readOnly');
      $('#divTipoDespacho').css('display', '');
      $('#tipoDespacho').attr('required', true);
      $('#divTipoComplemento').css('display', 'none');
      $('#divSubTipoProducto').css('display', '');
      $('#tipoComplemento').removeAttr('required');
      $('#subtipoProducto').attr('required', true);
    }
    $('#unidadMedida').html('<option value="u">Unidad</option>');
    $('#unidadMedida').attr('readOnly', true);
    $('#cantPresentacion').val('1');
    $('#cantPresentacion').attr('readOnly', true);
    $('#divGrupoEtario').css('display', '');
    $('#Cod_Grupo_Etario').attr('required', true);
    $('#gestionMedidas').css('display', 'none');
    $('#divUnidadMedidaPresentacion').css('display', 'none');
    $('#unidadMedidaPresentacion').removeAttr('required');
    $('#aportesCalyNutPanel').css('display', 'none');
    $('#fichaTecnicaPanel').css('display', '');
    $('#divVariacionMenu').css('display', '');
    $('#variacionMenu').attr('required', true);
    ocultarDatosDetPreparado(tipoProducto);
    /*fichaTecnicaDet*/
    obtenerProductos(1);
    /*fichaTecnicaDet*/
  } else if (tipoProducto == "03" || tipoProducto == "04"){
    $('#unidadMedida').html('<option value="">Seleccione...</option><option value="u">Unidad</option><option value="g">Gramos</option><option value="cc">Centímetros Cúbicos</option>');
    $('#unidadMedida').removeAttr('readOnly');
    $('#cantPresentacion').removeAttr('readOnly');
    $('#divGrupoEtario').css('display', 'none');
    $('#divUnidadMedidaPresentacion').css('display', '');
    $('#unidadMedidaPresentacion').attr('required', true);
    $('#Cod_Grupo_Etario').removeAttr('required');
    $('#aportesCalyNutPanel').css('display', '');
    $('#fichaTecnicaPanel').css('display', 'none');
    $('#descripcion').val('').removeAttr('readOnly');
    $('#divVariacionMenu').css('display', 'none');
    $('#divTipoDespacho').css('display', '');
    $('#tipoDespacho').attr('required', true);
    $('#divSubTipoProducto').css('display', '');
    $('#divTipoComplemento').css('display', 'none');
    $('#variacionMenu').removeAttr('required');
    $('#subtipoProducto').attr('required', true);

    if (tipoProducto == "04") {
      $('#divCantPreparacion').css('display', '');
      $('#cantidad_preparacion').attr('required', true);
    } else if (tipoProducto == "03") {
      $('#divCantPreparacion').css('display', 'none');
      $('#cantidad_preparacion').attr('required', false);
    }

  }

  if (tipoProducto == "01" ) {
    $('#divOrdenCiclo').css('display', '');
  } else if (tipoProducto != "01") {
    $('#divOrdenCiclo').css('display', 'none');
  }
});

function ocultarDatosDetPreparado(tipoProducto){
  if (tipoProducto == 1) {
  $('.datoPreparado').css('display', 'none');
  } else if (tipoProducto == 2) {
  $('.datoPreparado').css('display', '');
  }
}

$('#descripcion').on('keyup', function(){
  validar_existe_producto($(this));
});

 /*fichaTecnicaDet*/
function obtenerUnidadMedidaProducto(select, num){
    var producto = $(select).val();
        tipoProducto = $( "#tipoProducto" ).val();
   $.ajax({
    type: "POST",
    url: "functions/fn_menus_obtener_productos.php",
    data: {"producto" : producto, "respuesta" : "2"},
    beforeSend: function(){},
    success: function(data){
      $('#unidadMedidaProducto'+num).val(data);
      //$('#debug').html(data);
    }
    });
   if (tipoProducto == "01") {
      $('#cantidadProducto'+num).val(1).attr('readOnly', true);
      $('#pesoBrutoProducto'+num).val(1).attr('readOnly', true);
      $('#pesoNetoProducto'+num).val(1).attr('readOnly', true);
   } else if (tipoProducto == "02") {
      $('#cantidadProducto'+num).val(0).removeAttr('readOnly');
      $('#pesoBrutoProducto'+num).val(0).removeAttr('readOnly');
      $('#pesoNetoProducto'+num).val(0).removeAttr('readOnly');
   }
}

function obtenerProductos(num){
  var tipoProducto = $( "#tipoProducto" ).val();
      grupoEtario = $('#Cod_Grupo_Etario').val();
      variacionMenu = $('#variacionMenu').val();
      tipoComplemento = $('#tipoComplemento').val();
      //console.log(tipoProducto+" "+grupoEtario+" "+variacionMenu);
   if (tipoProducto != "" && grupoEtario != "" && variacionMenu != "") {
    $.ajax({
    type: "POST",
    url: "functions/fn_menus_obtener_productos.php",
    data: {"tipoProducto" : tipoProducto, "grupoEtario": grupoEtario, "variacionMenu" : variacionMenu, "tipoComplemento" : tipoComplemento,"respuesta" : "1"},
    beforeSend: function(){},
    success: function(data){
      console.log(data);
      // $('#productoFichaTecnicaDet'+num+' option').each(function(){$(this).remove()});

      //console.log(data);
      $('#productoFichaTecnicaDet'+num).append(data);
      productos = data;
      //$('#debug').html(data);
      // $('#productoFichaTecnicaDet'+num).select2();
      productos = data;
      //$('#debug').html(data);
    }
    });
   } else {

   }
}

function anadirProducto(){
    numProducto++;
    $('#borrarProducto').css('display', '');
    var tipoProducto = $( "#tipoProducto" ).val();
    if (tipoProducto == "02") {
      tbody = '<tr id="filaProductoFichaTecnicaDet'+numProducto+'" class="productoFichaTecnicaDet"><td><select name="productoFichaTecnicaDet['+numProducto+']" id="productoFichaTecnicaDet'+numProducto+'" class="form-control" onchange="obtenerUnidadMedidaProducto(this, '+numProducto+');" required>'+productos+'</select></td><td><input type="text" name="unidadMedidaProducto['+numProducto+']" id="unidadMedidaProducto'+numProducto+'" class="form-control" readOnly></td><td><input type="text" name="cantidadProducto['+numProducto+']" id="cantidadProducto'+numProducto+'" class="form-control" onchange="cambiarPesos(this, '+numProducto+');" required></td><td><input type="text" name="pesoBrutoProducto['+numProducto+']" id="pesoBrutoProducto'+numProducto+'" class="form-control" required></td><td><input type="text" name="pesoNetoProducto['+numProducto+']" id="pesoNetoProducto'+numProducto+'" class="form-control" required></td></tr>';
    } else if (tipoProducto == "01" || tipoProducto == "03") {
      tbody = '<tr id="filaProductoFichaTecnicaDet'+numProducto+'" class="productoFichaTecnicaDet"><td><select name="productoFichaTecnicaDet['+numProducto+']" id="productoFichaTecnicaDet'+numProducto+'" class="form-control" required>'+productos+'</select></td></tr>';
    }

    if (numProducto == 1 && tipoProducto != "04") {
      obtenerProductos(1);
    }
    console.log(numProducto);
  $('#tbodyProductos').append(tbody);

  $('.productoFichaTecnicaDet:last select').select2({
      width : "100%"
    });
}

function borrarProducto(){
  if (numProducto > 1) {
    $('#filaProductoFichaTecnicaDet'+numProducto).remove();
    numProducto--;
    if (numProducto == numProductoInicial) {
      $('#borrarProducto').css('display', 'none');
    }
  }
}

function borrarProductos(){
  for (var i = numProducto; i > 1; i--) {
    $('#filaProductoFichaTecnicaDet'+numProducto).remove();
    numProducto--;
  }
}

function cambiarPesos(input, num){
  var valor = $(input).val();
  $('#pesoBrutoProducto'+num).val(valor);
  $('#pesoNetoProducto'+num).val(valor);
}

function validar_existe_producto(input){
  if ($("#tipoProducto").val() != "01") {
    $.ajax({
      type: "POST",
      url: "functions/fn_menus_validar_nombre_producto.php",
      data: {"descripcion" : $(input).val(), "tipoProducto" : $("#tipoProducto").val(), "grupoEtario" : $("#Cod_Grupo_Etario").val()},
      beforeSend: function(){},
      success: function(data){
        console.log("Valida nombre : "+data);
        if (data == "1") {
          $('#existeDesc').css('display', '');
          $('#botonSiguiente').attr('disabled', true);
          $(input).css('border-color', '#cc5965');
        } else {
          $('#existeDesc').css('display', 'none');
          $('#botonSiguiente').removeAttr('disabled');
          $(input).css('border-color', '');
        }
      }
    });
  }
}

 /*fichaTecnicaDet*/

$( "#Cod_Grupo_Etario" ).change(function() {


validar_existe_producto($('#descripcion'));

  obtenerCiclo();
/*fichaTecnicaDet*/
obtenerProductos(1);
/*fichaTecnicaDet*/
});

$( "#tipoComplemento" ).change(function() {
  obtenerCiclo();
});

$( "#variacionMenu" ).change(function() {
  obtenerCiclo();
/*fichaTecnicaDet*/
obtenerProductos(1);
/*fichaTecnicaDet*/
});

$('#unidadMedida').change(function(){
  borrarMedidas();
  var unidadMedida = $('#unidadMedida').val();
  if (unidadMedida == "g" || unidadMedida == "cc") {
    if (unidadMedida == "g") {
       if ($( "#tipoProducto" ).val() == "03") {
        $('#unidadMedidaPresentacion').html('<option value="">Seleccione...</option><option value="u">Unidad</option><option value="kg">KiloGramo</option><option value="lb">Libra</option><option value="g">Gramos</option>');
       } else if ($( "#tipoProducto" ).val() == "04") {
        $('#unidadMedidaPresentacion').html('<option value="u">Unidad</option>');
       }
    } else if (unidadMedida == "cc") {
       if ($( "#tipoProducto" ).val() == "03") {
       $('#unidadMedidaPresentacion').html('<option value="">Seleccione...</option><option value="u">Unidad</option><option value="lt">Litro</option><option value="cc">Centimetros cúbicos</option>');
       } else if ($( "#tipoProducto" ).val() == "04") {
        $('#unidadMedidaPresentacion').html('<option value="u">Unidad</option>');
       }
    }
    $('#divCantPresentacion').css('display', '');
    $('#divUnidadMedidaPresentacion').css('display', '');
    $('#cantPresentacion').val(0);
    $('#cantPresentacion').removeAttr('readOnly');
  } else if (unidadMedida == "u"){
    $('#divCantPresentacion').css('display', '');
    $('#divUnidadMedidaPresentacion').css('display', 'none');
    $('#cantPresentacion').val('1');
    $('#cantPresentacion').attr('readOnly', true);
  }
});

$('#unidadMedidaPresentacion').change(function(){
  var unidadMedidaPresentacion = $('#unidadMedidaPresentacion').val();
  if (unidadMedidaPresentacion == "g" || unidadMedidaPresentacion == "cc") {
    $('#divCantPresentacion').css('display', '');
    $('#cantPresentacion').val('').removeAttr('readOnly');
    $('#gestionMedidas').css('display', '');
  } else if (unidadMedidaPresentacion == "u" || unidadMedidaPresentacion == "lb" || unidadMedidaPresentacion == "kg" || unidadMedidaPresentacion == "lt"){

    if (unidadMedidaPresentacion != "u") {
      $('#cantPresentacion').val('1').attr('readOnly', true);
    } else if (unidadMedidaPresentacion == "u") {
      $('#cantPresentacion').val('').removeAttr('readOnly');
    }
    $('#divCantPresentacion').css('display', '');
    borrarMedidas();
  }
});

/*Submit de formularios*/
$('#formProducto').submit(function(event){
 var datos = $('#formProducto').serialize();
      tipoproducto = $('#tipoProducto').val();
  $.ajax({
    type: "POST",
    url: "functions/fn_menus_ingresar_producto.php",
    data : datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      console.log(data);
      data = JSON.parse(data);
      if (data.respuesta[0].exitoso == 1) {
        $('#IdFT').val(data.respuesta[0].IdFT);
        if ($('#tipoProducto').val()== "01" || $('#tipoProducto').val()== "02") {
          $('#idProducto').val(data.respuesta[0].idProducto);
          $('#idProductoCalyNut').val(data.respuesta[0].idProducto);
        } else if ($('#tipoProducto').val()== "03" || $('#tipoProducto').val()== "04") {
          $('#idProducto').val(data.respuesta[0].nuevoCodigo);
          $('#idProductoCalyNut').val(data.respuesta[0].nuevoCodigo);
        }

        $('#tipoProductoCalyNut').val(tipoproducto);
        $('#TipoProductoFT').val(tipoproducto);
        $('#'+FormSubmit).submit();
      } else {
        //console.log(data.respuesta[0].respuesta);
        Command: toastr.error("Hubo un error al crear producto.", "Error", {onHidden : function(){console.log(data);}})
      }
    }
    });
  event.preventDefault();
});

$('#formFichaTecnica').submit(function(event){
    var datos = $('#formFichaTecnica').serialize();
    console.log(datos);
$.ajax({
    type: "POST",
    url: "functions/fn_menus_ingresar_ficha_tecnica.php",
    data : datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      if (data == "1") {
        Command: toastr.success("Se creó con éxito.", "Creado", {onHidden : function(){location.reload();}})
      } else {
        Command: toastr.error("Hubo un error al crear.", "Error", {onHidden : function(){console.log(data);}})
      }
    }
    });
event.preventDefault();
});


$('#formCalyNut').submit(function(event){
$('#loader').fadeIn();
var datos = $('#formCalyNut').serialize();
$.ajax({
    type: "POST",
    url: "functions/fn_menus_ingresar_calynut.php",
    data : datos,
    beforeSend: function(){},
    success: function(data){
      //$('#debug').html(data);
      if (data == "1") {
        Command: toastr.success("Se creó con éxito.", "Creado", {onHidden : function(){location.reload();}})
      } else {
        Command: toastr.error("Hubo un error al crear.", "Error", {onHidden : function(){console.log(data);}})
      }
    }
  });
event.preventDefault();
});
/*Submit de formularios*/

function obtenerCiclo(){
var grupoEtario = $( "#Cod_Grupo_Etario" ).val();
    subtipoProducto = $( "#tipoComplemento" ).val();
    variacionMenu = $( "#variacionMenu" ).val();
    console.log(grupoEtario+" - "+subtipoProducto+" - "+variacionMenu);
  if (grupoEtario != "" && subtipoProducto != "" && variacionMenu != "") {
    $.ajax({
    type: "POST",
    url: "functions/fn_menus_obtener_num_ciclo.php",
    data: {"subtipoProducto" : subtipoProducto, "grupoEtario" : grupoEtario, "variacionMenu" : variacionMenu},
    beforeSend: function(){},
    success: function(data){
      console.log(data);
      if (data != 0) {
        $('#ordenCiclo').val(data);
      } else {
        Command: toastr.error("Hubo un error al obtener ciclo.", "Error", {onHidden : function(){console.log(data);}})
      }
    }
    });
  }
}

function validarMedida(){
  if ($('#unidadMedida').val() != "u" && $('#unidadMedida').val() != ""){
    return true;
  } else {
    return false;
  }
}

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


function editarProducto(idProducto){
  $('#idProductoEditar').val(idProducto);
  $('#editar_producto').submit();
}

function validarForm(formulario, panel1, panel2){
  if ($("#"+formulario).valid()) {
      $('#segundoBtnSubmit').css('display', '');
      $('#'+panel1).collapse('hide');
      $('#'+panel2).collapse('show');
  }
      var heights = $(".col-sm-3").map(function() {
        return $(this).height();
    }).get(),

    maxHeight = Math.max.apply(null, heights);

    $(".col-sm-3").height(maxHeight);
}

/*Editar producto*/

$('#formEditarProducto').submit(function(event){

var datos = $('#formEditarProducto').serialize();
$.ajax({
type: "POST",
url: "functions/fn_menus_editar_producto.php",
data: datos,
beforeSend: function(){},
success: function(data){
  if (data == "1") {
    Command: toastr.success("Se actualizó con éxito.", "Actualizado", {onHidden : function(){
    $('#ver_producto').submit();}})
  } else {
    Command: toastr.error("Hubo un error al actualizar.", "Error", {onHidden : function(){console.log(data);}})
  }
}
});

event.preventDefault();
});


function loadTipoProducto(valor){
  $('#tipoProducto').val(valor);
  $('#tipoProducto').change();
}

function loadSubTipoProducto(valor){
  $('#subtipoProducto').val(valor);
  $('#subtipoProducto').change();
}

/*Editar producto*/

var FormSubmit = "";

function submitForm(form){

  if ($('#'+form).valid()) {
    $('#loader').fadeIn();

    if (form != 'formEditarProducto') {
      $('#formProducto').submit();
      FormSubmit = form;
    } else if (form == 'formEditarProducto') {
      $('#formEditarProducto').submit();
    }


    // setTimeout(function() { $('#'+form).submit(); }, 2000);
  } else {
    console.log('Campos vacíos');
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
      $(input).val(0).focus();
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
      $(input).val(0).focus();
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


  $('#box-table tbody td:nth-child(-n+5)').on('click', function(){
    $('#idProducto').val($(this).parent().attr("idproducto"));
    $('#ver_producto').submit();
  });

  $('#tablaMenus tbody td:nth-child(-n+5)').on('click', function(){
    $('#descripcion').val($(this).parent().attr("descripcion"));
    $('#codigo').val($(this).parent().attr("codigo"));
    $('#idProducto').val($(this).parent().attr("idproducto"));
    $('#menus_analisis').submit();
  });

/*ELIMINAR PRODUCTO*/
$('#modalEliminar').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      codigoProducto = button.data('codigo');
      ordenCiclo = button.data('ordenciclo');
      tipoComplemento = button.data('tipocomplemento');
      $('#codigoProductoEli').val(codigoProducto);
      $('#ordenCicloEli').val(ordenCiclo);
      $('#tipoComplementoEli').val(tipoComplemento);
});

function eliminarProducto(){
  $('#modalEliminar').modal('hide');
  $('#loader').fadeIn();
  var codigoProducto = $('#codigoProductoEli').val();
      ordenCiclo = $('#ordenCicloEli').val();
      tipoComplemento = $('#tipoComplementoEli').val();

 $.ajax({
  type: "POST",
  url: "functions/fn_menus_eliminar_producto.php",
  data: {"codigoProducto" : codigoProducto, "tipoComplemento" : tipoComplemento, "ordenCiclo" : ordenCiclo},
  beforeSend: function(){},
  success: function(data){
  console.log(data);
    data = JSON.parse(data);
    if (data.respuesta[0].exitoso == "1") {
      Command: toastr.success("El producto fue "+data.respuesta[0].Accion+" con éxito.", "Producto "+data.respuesta[0].Accion,
                                {onHidden : function(){
                                  if (data.respuesta[0].TipoProducto == "01") {
                                    window.location.href='index.php';
                                  } else if (data.respuesta[0].TipoProducto == "02") {
                                    window.location.href='ver_preparaciones.php';
                                  } else if (data.respuesta[0].TipoProducto == "03") {
                                    window.location.href='ver_alimentos.php';
                                  } else if (data.respuesta[0].TipoProducto == "04") {
                                    window.location.href='ver_alimentos.php';
                                  }
                                }})
    } else if (data.respuesta[0].exitoso == "0") {
      Command: toastr.success(data.respuesta[0].Nota, "Error al eliminar", {onHidden : function(){location.reload();}})
    } else {
      console.log(data);
    }
  }
});
}

$('#modalEliminarFTDet').on('show.bs.modal', function (event) {
  var button = $(event.relatedTarget);
      idftd = button.data('idftd');
      numFTD = button.data('numftd');
      idproducto = button.data('idproducto');
      $('#idftd').val(idftd);
      $('#numFTD').val(numFTD);
      $('#idproducto').val(idproducto);
});

function eliminarFTDet(){
  $('#loader').fadeIn();
  $('#modalEliminarFTDet').modal('hide');
  var idftd = $('#idftd').val();
      numFTD = $('#numFTD').val();
      idproducto = $('#idproducto').val();
  $.ajax({
  type: "POST",
  url: "functions/fn_menus_eliminar_ftdet.php",
  data: {"idftd" : idftd, "idproducto" : idproducto},
  beforeSend: function(){},
  success: function(data){
    if (data == "1") {
      // $('#loader').fadeOut();
      // $('#filaProductoFichaTecnicaDet'+numFTD).remove();
      Command: toastr.success("El producto se eliminó con éxito.", "Producto eliminado", {onHidden : function(){location.reload();}})
    } else if (data == "0"){
      Command: toastr.warning("El menú al que está relacionado el producto, está despachado.", "No se puede desasociar el producto.", {onHidden : function(){console.log(data);}})
    } else {
      Command: toastr.error("Error al eliminar producto.", "Error.", {onHidden : function(){console.log(data);$('#loader').fadeOut();}})
    }
  }
});
}

function exportarProducto(id){
  $('#exportar_producto #idProductoExportar').val(id);
  $('#exportar_producto').submit();
}

/*ELIMINAR PRODUCTO*/

