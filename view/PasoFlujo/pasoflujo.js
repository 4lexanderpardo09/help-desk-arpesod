var tabla;

function init() {
    $("#paso_form").on("submit", function(e){
        guardaryeditar(e);
    });

    // Nuevo: Submit para el form dentro del modal de transiciones
    $("#transicion_form_modal").on("submit", function(e){
        e.preventDefault();
        var formData = new FormData($("#transicion_form_modal")[0]);
        $.ajax({
            url: "../../controller/flujotransicion.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function(datos) {
                $('#transicion_form_modal')[0].reset();
                // Recargar la lista de transiciones en el modal
                abrirModalTransiciones(formData.get('paso_origen_id'), $("#nombre_paso_origen").text());
                swal("Correcto!", "Transición guardada.", "success");
            }
        });
    });
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

// --- NUEVA FUNCIONALIDAD PARA TRANSICIONES ---
function abrirModalTransiciones(paso_id, paso_nombre) {
    $('#modalGestionTransicionesLabel').html('Gestionar Transiciones para: ' + paso_nombre);
    $('#paso_origen_id_modal').val(paso_id);
    $('#nombre_paso_origen').text(paso_nombre);

    // Cargar pasos para el dropdown de destino
    $.post("../../controller/flujotransicion.php?op=combo_pasos", { flujo_id: getUrlParameter('ID') }, function(data) {
        $('#paso_destino_id_modal').html('<option value="">-- Fin del Flujo --</option>' + data);
    });

    // Cargar tabla de transiciones existentes
    $('#transiciones_data tbody').html('');
    $.post("../../controller/flujotransicion.php?op=listar_por_paso", { paso_origen_id: paso_id }, function(data) {
        var transiciones = JSON.parse(data);
        transiciones.aaData.forEach(function(row) {
            var fila = '<tr>';
            fila += '<td>' + row[1] + '</td>'; // paso_destino
            fila += '<td>' + row[2] + '</td>'; // condicion_clave
            fila += '<td>' + row[3] + '</td>'; // condicion_nombre
            fila += '<td>' + row[5] + '</td>'; // Botón eliminar
            fila += '</tr>';
            $('#transiciones_data tbody').append(fila);
        });
    });

    $('#modalGestionTransiciones').modal('show');
}

function eliminarTransicion(transicion_id, paso_id, paso_nombre) {
    swal({
        title: "Confirmar",
        text: "¿Está seguro de eliminar la transición?",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No",
    }, function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/flujotransicion.php?op=eliminar", { transicion_id: transicion_id }, function() {
                // Recargar la lista de transiciones en el modal
                abrirModalTransiciones(paso_id, paso_nombre);
                swal("Eliminado!", "La transición se eliminó.", "success");
            });
        }
    });
}
// --- FIN NUEVA FUNCIONALIDAD ---

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

});

init();