var tabla;

function init() {
    $("#reglaaprobacion_form").on("submit", function(e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#reglaaprobacion_form")[0]);
    $.ajax({
        url: "../../controller/reglaaprobacion.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function() {
            $('#reglaaprobacion_form')[0].reset();
            $("#modalnuevareglaaprobacion").modal('hide');
            $('#reglaaprobacion_data').DataTable().ajax.reload();
            swal("¡Correcto!", "Regla guardada exitosamente.", "success");
        }
    });
}

$(document).ready(function() {
    $('#creador_car_id, #aprobador_usu_id').select2({
        dropdownParent: $('#modalnuevareglaaprobacion')
    });

    // Cargar combo de cargos
    $.post("../../controller/cargo.php?op=combo", function(data) {
        $('#creador_car_id').html(data);
    });

    // Cargar combo de usuarios
    $.post("../../controller/usuario.php?op=usuariosxrol", function(data) {
        $('#aprobador_usu_id').html(data);
    });

    tabla = $('#reglaaprobacion_data').dataTable({
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
            url: '../../controller/reglaaprobacion.php?op=listar',
            type: 'post',
            dataType: 'json',
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
});    

function editar(regla_id) {
    $('#mdltitulo').html('Editar Regla');
    $.post("../../controller/reglaaprobacion.php?op=mostrar", { regla_id: regla_id }, function(data) {
        data = JSON.parse(data);
        $('#regla_id').val(data.regla_id);
        $('#creador_car_id').val(data.creador_car_id).trigger('change');
        $('#aprobador_usu_id').val(data.aprobador_usu_id).trigger('change');
        $('#modalnuevareglaaprobacion').modal('show');
    });
}

function eliminar(regla_id) {
    swal({
        title: "Advertencia",
        text: "¿Está seguro de eliminar esta regla?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/reglaaprobacion.php?op=eliminar", { regla_id: regla_id }, function() {
                $('#reglaaprobacion_data').DataTable().ajax.reload();
                swal("¡Eliminado!", "La regla ha sido eliminada.", "success");
            });
        }
    });
}

$('#btnnuevareglaaprobacion').on('click', function() {
    $('#mdltitulo').html('Nueva Regla');
    $('#reglaaprobacion_form')[0].reset();
    $('#regla_id').val('');
    $('#creador_car_id').val(null).trigger('change');
    $('#aprobador_usu_id').val(null).trigger('change');
    $('#modalnuevareglaaprobacion').modal('show');
});

init();