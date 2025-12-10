var tabla;

function init() {
    $("#perfil_form").on("submit", function (e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#perfil_form")[0]);
    $.ajax({
        url: "../../controller/perfil.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            $('#perfil_form')[0].reset();
            $("#modalnuevoperfil").modal('hide');
            $('#perfil_data').DataTable().ajax.reload();
            swal({
                title: "¡Correcto!",
                text: "Completado.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
}

$(document).ready(function () {
    tabla = $('#perfil_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
        "ajax": {
            url: '../../controller/perfil.php?op=listar',
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
        "order": [[0, "asc"]],
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

function editar(per_id) {
    $('#mdltitulo').html('Editar Registro');
    $.post("../../controller/perfil.php?op=mostrar", { per_id: per_id }, function (data) {
        data = JSON.parse(data);
        $('#per_id').val(data.per_id);
        $('#per_nom').val(data.per_nom);
        $('#modalnuevoperfil').modal('show');
    });
}

function eliminar(per_id) {
    swal({
        title: "Advertencia",
        text: "¿Está seguro de eliminar el Perfil?",
        type: "error",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si",
        cancelButtonText: "No",
        closeOnConfirm: false
    },
        function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/perfil.php?op=eliminar", { per_id: per_id }, function (data) {
                    $('#perfil_data').DataTable().ajax.reload();
                    swal({
                        title: "¡Correcto!",
                        text: "Registro eliminado.",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    });
                });
            }
        });
}

$(document).on("click", "#btnnuevoperfil", function () {
    $('#mdltitulo').html('Nuevo Registro');
    $('#perfil_form')[0].reset();
    $('#modalnuevoperfil').modal('show');
});

init();
