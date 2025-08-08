var tabla;

function init() {
    $("#usuario_form").on("submit", function (e) {
        console.log("Enviando empresas: ", $('#emp_id_string').val());

        guardaryeditar(e);
    })
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#usuario_form")[0])
    $.ajax({
        url: "../../controller/usuario.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function (datos) {
            $("#usuario_form")[0].reset();
            $('#usu_id').val('');
            $('#usu_nom').html('');
            $('#usu_ape').html('');
            $('#usu_correo').html('');
            $('#emp_id').val('').trigger('change')
            $('#rol_id').val('').trigger('change');
            $('#reg_id').val('').trigger('change');
            $('#car_id').val('').trigger('change');
            $('#dp_id').val('').trigger('change');
            $("#modalnuevousuario").modal('hide');
            $("#user_data").DataTable().ajax.reload();
            swal({
                title: "Guardado!",
                text: "Se ha guardado correctamente el nuevo registro.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    })
}


$(document).ready(function () {

    $('#rol_id,#emp_id,#car_id,#reg_id, #creador_car_id').select2({
        dropdownParent: $('#modalnuevousuario'),
        placeholder: "Seleccionar",
    });

    tabla = $('#user_data').dataTable({
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
            url: '../../controller/usuario.php?op=listar',
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

    $.post("../../controller/departamento.php?op=combo", function (data) {
        $('#dp_id').html('<option value="">Seleccionar</option>' + data);
    });

    toggleDepartamento()

    $('#rol_id').on('change', toggleDepartamento);

    $.post("../../controller/empresa.php?op=combo", function (data) {
        $('#emp_id').html(data);
    });
    $.post("../../controller/cargo.php?op=combo", function (data) {
        $('#car_id').html(data);
    });
    $.post("../../controller/regional.php?op=combo", function (data) {
        console.log(data);
        
        $('#reg_id').html(data);
    });

})

function editar(usu_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/usuario.php?op=mostrar", { usu_id: usu_id }, function (data) {
        data = JSON.parse(data);
        $('#usu_id').val(data.usu_id);
        $('#usu_nom').val(data.usu_nom);
        $('#usu_ape').val(data.usu_ape);
        $('#usu_correo').val(data.usu_correo);
        $('#emp_id').val(data.emp_ids.split(',')).trigger('change')
        $('#rol_id').val(data.rol_id).trigger('change');
        $('#dp_id').val(data.dp_id).trigger('change');
        $('#reg_id').val(data.reg_id).trigger('change');
        $('#car_id').val(data.car_id).trigger('change');
        if (data.es_nacional == 1) {
            $('#es_nacional').prop('checked', true);
        } else {
            $('#es_nacional').prop('checked', false);
        }
    });

    $("#modalnuevousuario").modal("show");
}
function eliminar(usu_id) {
    swal({
        title: "¿Estas que quieres eliminar este usuario?",
        text: "Una vez eliminado no podrás volver a recuperarlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, eliminar usuario!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
        function (isConfirm) {
            if (isConfirm) {
                $.post("../../controller/usuario.php?op=eliminar", { usu_id: usu_id }, function (data) {
                    $('#user_data').DataTable().ajax.reload();
                    swal({
                        title: "Eliminado!",
                        text: "Usuario eliminado correctamente",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    });
                });
            } else {
                swal({
                    title: "Cancelado",
                    text: "El ticket sigue abierto.",
                    type: "error",
                    confirmButtonClass: "btn-danger"

                });
            }
        });
}

function toggleDepartamento() {
    var rol = $('#rol_id').val();
    if (rol === '2') {
        $('#dp_id_group').show();
    } else {
        $('#dp_id_group').hide();
        $('#dp_id').val(null).trigger('change'); // Limpiar selección
    }
}

$(document).on("click", "#btnnuevoregistro", function () {
    $("#mdltitulo").html('Nuevo registro');
    $("#usuario_form")[0].reset();
    $("#modalnuevousuario").modal("show");
    $('#reg_id').val('').trigger('change');
    $('#car_id').val('').trigger('change');
});

$('#modalnuevousuario').on('hidden.bs.modal', function() {
    $("#usuario_form")[0].reset();
    $('#usu_id').val('');
    $('#usu_nom').html('');
    $('#usu_ape').html('');
    $('#usu_correo').html('');
    $('#emp_id').val('').trigger('change')
    $('#rol_id').val('').trigger('change');
    $('#reg_id').val('').trigger('change');
    $('#car_id').val('').trigger('change');
    $('#dp_id').val('').trigger('change');
});


init();