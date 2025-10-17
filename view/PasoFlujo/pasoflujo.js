var tabla;

function init() {
    $("#paso_form").on("submit", function(e){
        guardaryeditar(e);
    });

    // Nuevo: Submit para el form dentro del modal de transiciones
    $("#transicion_form").on("submit", function(e){
        e.preventDefault();
        var formData = new FormData($("#transicion_form")[0]);
        $.ajax({
            url: "../../controller/flujotransicion.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(datos) {
                $('#transicion_form')[0].reset();
                // Recargar la lista de transiciones en el modal
                abrirModalTransiciones(formData.get('paso_origen_id'), $("#nombre_paso_origen").text());
                swal("Correcto!", "Transición guardada.", "success");
            }
        });
    });
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#paso_form")[0])
    formData.append("es_aprobacion", $("#es_aprobacion").is(":checked") ? 1 : 0);
    $.ajax({
        url: "../../controller/flujopaso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#paso_form")[0].reset();
            $("#modalnuevopaso").modal('hide');
            $("#paso_data").DataTable().ajax.reload();
            swal("Guardado!", "Se ha guardado correctamente el registro.", "success");          
        }
    })
}

// ==================================================================
// == LÓGICA PARA GESTIONAR LOS PASOS DE UNA RUTA ==
// ==================================================================

$(document).ready(function() {
    // Inicialmente, el botón de gestionar pasos está deshabilitado
    $('#btnGestionarPasos').prop('disabled', true);

    // Cuando el usuario selecciona una ruta en el dropdown...
    $('#ruta_id_modal').on('change', function() {
        var rutaSeleccionada = $(this).val();
        // Si se ha seleccionado una ruta válida, habilita el botón. Si no, lo deshabilita.
        if (rutaSeleccionada && rutaSeleccionada !== '') {
            $('#btnGestionarPasos').prop('disabled', false);
        } else {
            $('#btnGestionarPasos').prop('disabled', true);
        }
    });

    // Cuando el usuario hace clic en el botón de gestionar pasos...
    $(document).on('click', '#btnGestionarPasos', function() {
        var ruta_id = $('#ruta_id_modal').val();
        // Obtenemos el texto de la opción seleccionada
        var ruta_nombre = $('#ruta_id_modal option:selected').text();

        if (ruta_id && ruta_id !== '') {
            // Ocultamos el modal actual
            $('#modalGestionTransiciones').modal('hide');
            
            // Llamamos a la función que abre el segundo modal (esta función la creamos en el mensaje anterior)
            gestionarPasosRuta(ruta_id, ruta_nombre);
        } else {
            swal("Atención", "Por favor, seleccione una ruta primero.", "warning");
        }
    });

    // Cuando el modal de gestionar pasos se cierre, volvemos a mostrar el de transiciones
    $('#modalGestionPasosRuta').on('hidden.bs.modal', function () {
        $('#modalGestionTransiciones').modal('show');
    });

});

function gestionarPasosRuta(ruta_id, ruta_nombre) {
    // 1. Llenar datos del modal
    $('#gestion_ruta_id').val(ruta_id);
    $('#gestion_ruta_nombre').text(ruta_nombre);

    // 2. Llenar el dropdown con todos los pasos del flujo actual
    //    Necesitamos una nueva operación 'combo' en el controlador de FlujoPaso
    $.post("../../controller/flujopaso.php?op=combo_por_flujo", { flujo_id: flujo_id }, function(data) {
        $('#paso_id_para_ruta').html(data);
        // Opcional: Refrescar si usas un plugin
        // $('#paso_id_para_ruta').selectpicker('refresh'); 
    });

    // 3. Cargar la tabla con los pasos que ya están en la ruta
    cargarTablaRutaPasos(ruta_id);

    // 4. Mostrar el modal
    $('#modalGestionPasosRuta').modal('show');
}

// Función para recargar la tabla de pasos de la ruta
function cargarTablaRutaPasos(ruta_id) {
    $('#rutapasos_data tbody').html(''); // Limpiar tabla
    $.post("../../controller/rutapaso.php?op=listar", { ruta_id: ruta_id }, function(data) {
        var pasos = JSON.parse(data);
        if (pasos.length > 0) {
            pasos.forEach(function(paso) {
                var fila = '<tr>';
                fila += '<td><span class="badge badge-pill badge-primary">' + paso.orden + '</span></td>';
                fila += '<td>' + paso.paso_nombre + '</td>';
                fila += '<td><button type="button" onClick="eliminarPasoDeRuta(' + paso.ruta_paso_id + ')" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></td>';
                fila += '</tr>';
                $('#rutapasos_data tbody').append(fila);
            });
        } else {
            // Mensaje si no hay pasos
            var fila = '<tr><td colspan="3" class="text-center">Aún no se han añadido pasos a esta ruta.</td></tr>';
            $('#rutapasos_data tbody').append(fila);
        }
    });
}

// Handler para el formulario de añadir paso a la ruta
$(document).on('submit', '#rutapaso_form', function(e) {
    e.preventDefault();
    var ruta_id = $('#gestion_ruta_id').val();
    
    $.ajax({
        url: "../../controller/rutapaso.php?op=guardar",
        type: "POST",
        data: {
            ruta_id: ruta_id,
            paso_id: $('#paso_id_para_ruta').val(),
            orden: $('#orden_del_paso').val()
        },
        success: function() {
            // Limpiar formulario y recargar tabla
            $('#orden_del_paso').val('');
            cargarTablaRutaPasos(ruta_id);
            swal("¡Éxito!", "Paso añadido a la ruta.", "success");
        },
        error: function() {
            swal("Error", "No se pudo añadir el paso.", "error");
        }
    });
});

// Función para eliminar un paso de la ruta
function eliminarPasoDeRuta(ruta_paso_id) {
    swal({
        title: "Confirmar",
        text: "¿Está seguro de quitar este paso de la ruta?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, quitar",
        cancelButtonText: "No",
    }, function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/rutapaso.php?op=eliminar", { ruta_paso_id: ruta_paso_id }, function() {
                // Recargamos la tabla para ver el cambio
                cargarTablaRutaPasos($('#gestion_ruta_id').val());
                swal("Eliminado", "El paso ha sido quitado de la ruta.", "success");
            });
        }
    });
}

// ==================================================================
// == CÓDIGO FINAL Y CONSOLIDADO PARA MODAL DE TRANSICIONES ==
// ==================================================================
flujo_id = getUrlParameter('ID'); // Asegúrate de que esta variable esté disponible globalmente en tu script.

function abrirModalTransiciones(paso_id, paso_nombre) {
    // 1. Preparamos el contenido estático del modal.
    $('#modalGestionTransicionesLabel').html('Gestionar Transiciones para: ' + paso_nombre);
    $('#paso_origen_id_modal').val(paso_id);
    $('#nombre_paso_origen').text(paso_nombre);
    $('#areaNuevaRuta').hide();
    $('#nueva_ruta_nombre').val('');

    // 2. Cargamos la tabla de transiciones existentes.
    cargarTablaTransiciones(paso_id);

    // 3. Cargamos las rutas y LUEGO mostramos el modal.
    cargarYReconstruirSelect().then(function() {
        // Una vez que el select está listo y reconstruido, mostramos el modal.
        $('#modalGestionTransiciones').modal('show');
    });
}

/**
 * Función centralizada y segura para actualizar el select de rutas.
 * Devuelve una Promesa para saber cuándo ha terminado.
 */
function cargarYReconstruirSelect(ruta_a_seleccionar) {
    var d = $.Deferred(); // Creamos una promesa para controlar el flujo asíncrono.
    var select = $('#ruta_id_modal');

    // Hacemos la llamada AJAX para obtener las rutas.
    $.post("../../controller/ruta.php?op=listar_para_select", { flujo_id: flujo_id }, function(data) {
        
        // Actualizamos el HTML del select.
        select.html(data);

        // Si se nos pasó una ruta para seleccionar, la marcamos como seleccionada.
        if (ruta_a_seleccionar) {
             select.val(ruta_a_seleccionar); // .val() es seguro aquí porque el plugin está destruido.
        }
        // Resolvemos la promesa para indicar que hemos terminado.
        d.resolve();
    });
    
    return d.promise();
}


// Botón para mostrar/ocultar el área de nueva ruta
$(document).on("click", "#btnNuevaRuta", function() {
    $('#areaNuevaRuta').toggle();
});


// Handler para GUARDAR LA NUEVA RUTA (CORREGIDO)
$(document).on("click", "#btnGuardarRuta", function() {
    var nombreRuta = $('#nueva_ruta_nombre').val();
    if (nombreRuta.trim() === '') {
        swal("Error", "El nombre de la ruta no puede estar vacío.", "error");
        return;
    }

    $.ajax({
        url: "../../controller/ruta.php?op=guardaryeditar",
        type: "POST",
        data: {
            flujo_id: flujo_id,
            ruta_nombre: nombreRuta
        },
        success: function(new_ruta_id) {
            if (new_ruta_id) {
                $('#nueva_ruta_nombre').val('');
                $('#areaNuevaRuta').hide();
                swal("Correcto!", "Ruta creada exitosamente.", "success");

                // Usamos la nueva función centralizada para recargar el select
                // y pre-seleccionar la ruta que acabamos de crear.
                cargarYReconstruirSelect(new_ruta_id);
            } else {
                 swal("Error", "No se pudo crear la ruta.", "error");
            }
        }
    });
});

// El resto de las funciones (cargarTablaTransiciones, submit del formulario, etc.)
// pueden permanecer como en la respuesta anterior, ya que no tocan el select.

function cargarTablaTransiciones(paso_id) {
    $('#transiciones_data tbody').html(''); 
    $.post("../../controller/flujotransicion.php?op=listar_por_paso", { paso_origen_id: paso_id }, function(data) {
        var response = JSON.parse(data);
        if (response.aaData) {
            response.aaData.forEach(function(row) {
                var fila = '<tr>';
                fila += '<td>' + row[0] + '</td>'; // ruta_nombre
                fila += '<td>' + row[1] + '</td>'; // condicion_clave
                fila += '<td>' + row[2] + '</td>'; // condicion_nombre
                fila += '<td>' + row[3] + ' ' + row[4] + '</td>';
                fila += '</tr>';
                $('#transiciones_data tbody').append(fila);
            });
        }
    });
}

$(document).ready(function () {

    descripcionPaso();
    cargarUsuarios();

    $('#flujo_id').val(getUrlParameter('ID'));

    tabla = $('#paso_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        "ajax": {
            url: '../../controller/flujopaso.php?op=listar',
            type: 'post',
            data: {flujo_id: getUrlParameter('ID')},
            dataType: 'json',
            "dataSrc": function(json) {
                // Añadir el botón de transiciones a cada fila
                json.aaData.forEach(function(row) {
                    var paso_id = row[6].match(/editar\((\d+)\)/)[1];
                    var paso_nombre = row[1];
                    row.splice(6, 0, '<button type="button" onClick="abrirModalTransiciones(' + paso_id + ',\'' + paso_nombre + '\');" class="btn btn-inline btn-info btn-sm"><i class="fa fa-eye"></i></button>');
                });
                return json.aaData;
            },
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": {
            "sProcessing": "Procesando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        }
    }).DataTable();


})

function editar(paso_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/flujopaso.php?op=mostrar", {paso_id:paso_id}, function(data) {
        data = JSON.parse(data);
        $('#paso_id').val(data.paso_id);
        $('#paso_orden').val(data.paso_orden);
        $('#paso_nombre').val(data.paso_nombre);
        $('#cargo_id_asignado').val(data.cargo_id_asignado);
        $('#paso_tiempo_habil').val(data.paso_tiempo_habil);
        if (data.requiere_seleccion_manual == 1) {
            $('#requiere_seleccion_manual').prop('checked', true);
        } else {
            $('#requiere_seleccion_manual').prop('checked', false);
        }

        if (data.es_tarea_nacional == 1) {
            $('#es_tarea_nacional').prop('checked', true);
        } else {
            $('#es_tarea_nacional').prop('checked', false);
        }

        if (data.es_aprobacion == 1) {
            $('#es_aprobacion').prop('checked', true);
        } else {
            $('#es_aprobacion').prop('checked', false);
        }

        $('#paso_descripcion').summernote('code', data.paso_descripcion);

        // Handle attachment display
        if (data.paso_nom_adjunto) {
            $('#current_paso_nom_adjunto').val(data.paso_nom_adjunto);
            var attachmentLink = '<a href="../../public/document/paso/' + data.paso_nom_adjunto + '" target="_blank">Ver Adjunto Actual</a>';
            $('#paso_attachment_display').html(attachmentLink);
        } else {
            $('#current_paso_nom_adjunto').val('');
            $('#paso_attachment_display').html('');
        }

    });    

    $("#modalnuevopaso").modal("show");
}
function eliminar(paso_id) {
    swal({
        title: "¿Estas seguro que quieres eliminar este paso?",
        text: "Una vez eliminado no podrás volver a recuperarlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/flujopaso.php?op=eliminar", {paso_id:paso_id}, function(data) {
                $('#paso_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "Paso eliminado correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "El paso no fue eliminado",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevopaso", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#paso_form")[0].reset();
    $('#requiere_seleccion_manual').prop('checked', false);
    $('#paso_nom_adjunto').val('');
    $('#paso_attachment_display').html('');
    $("#modalnuevopaso").modal("show");
});

function cargarUsuarios() {
    $.post("../../controller/cargo.php?op=combo", function(data) {
        $('#cargo_id_asignado').html('<option value="">Seleccionar un cargo</option>' + data);
    });

}

function descripcionPaso(){
    $('#paso_descripcion').summernote({
        height: 200,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function (image) {
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                console.log("Text detect...");
            }
        }
    });
}



$('#modalnuevopaso').on('hidden.bs.modal', function () {
    $("#paso_form")[0].reset();
    $("#paso_id").val('');
    $("#paso_orden").val('');
    $('#paso_nombre').val('');
    $('#cargo_id_asignado').val('');
    $('#requiere_seleccion_manual').prop('checked', false);
    $('#paso_tiempo_habil').val('');
    $('#paso_descripcion').summernote('code', '');
    $('#paso_nom_adjunto').val('');
    $('#paso_attachment_display').html('');

});

init();