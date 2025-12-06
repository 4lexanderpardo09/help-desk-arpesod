var tabla;

function init() {
    $("#zona_form").on("submit", function (e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#zona_form")[0]);
    $.ajax({
        url: "../../controller/zona.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            $('#zona_form')[0].reset();
            $("#modalnuevazona").modal('hide');
            $('#zona_data').DataTable().ajax.reload();

            swal({
                title: "Correcto!",
                text: "Completado.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
}

$(document).ready(function () {
    tabla = $('#zona_data').dataTable({
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
            url: '../../controller/zona.php?op=listar',
            type: "post",
            dataType: "json",
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

function editar(zona_id) {
    $('#mdltitulo').html('Editar Registro');
    $.post("../../controller/zona.php?op=mostrar", { zona_id: zona_id }, function (data) {
        data = JSON.parse(data);
        $('#zona_id').val(data.zona_id);
        $('#zona_nom').val(data.zona_nom);
    });
    $('#modalnuevazona').modal('show');
}

function eliminar(zona_id) {
    swal({
        title: "Atención",
        text: "¿Está seguro de eliminar el registro?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "No, cancelar",
        closeOnConfirm: false,
        closeOnCancel: true
    },
        function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/zona.php?op=eliminar", { zona_id: zona_id }, function (data) {
                    $('#zona_data').DataTable().ajax.reload();
                    swal({
                        title: "Correcto!",
                        text: "Registro Eliminado.",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    });
                });
            }
        });
}

$('#btnnuevo').click(function () {
    $('#mdltitulo').html('Nuevo Registro');
    $('#zona_form')[0].reset();
    $('#zona_id').val('');
    $('#modalnuevazona').modal('show');
});

init();
