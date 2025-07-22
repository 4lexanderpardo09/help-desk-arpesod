var tabla;

function init() {
    $("#regional_form").on("submit", function(e) {
        guardaryeditar(e);
    });
}

function guardaryeditar(e) {
    e.preventDefault();
    var formData = new FormData($("#regional_form")[0]);
    $.ajax({
        url: "../../controller/regional.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos) {
            $('#regional_form')[0].reset();
            $("#modalnuevaregional").modal('hide');
            $('#regional_data').DataTable().ajax.reload();

            swal({
                title: "Correcto!",
                text: "Completado.",
                type: "success",
                confirmButtonClass: "btn-success"
            });
        }
    });
}

$(document).ready(function() {
    tabla = $('#regional_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: ['copyHtml5', 'excelHtml5', 'csvHtml5', 'pdfHtml5'],
        "ajax": {
            url: '../../controller/regional.php?op=listar',
            type: "post",
            dataType: "json",
            error: function(e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 10,
        "autoWidth": false,
        "language": { "sUrl": "//cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json" }
    }).DataTable();
});

function editar(reg_id) {
    $('#mdltitulo').html('Editar Registro');
    $.post("../../controller/regional.php?op=mostrar", { reg_id: reg_id }, function(data) {
        data = JSON.parse(data);
        $('#reg_id').val(data.reg_id);
        $('#reg_nom').val(data.reg_nom);
    });
    $('#modalnuevaregional').modal('show');
}

function eliminar(reg_id) {
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
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/regional.php?op=eliminar", { reg_id: reg_id }, function(data) {
                $('#regional_data').DataTable().ajax.reload();
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

$('#btnnuevo').click(function() {
    $('#mdltitulo').html('Nuevo Registro');
    $('#regional_form')[0].reset();
    $('#reg_id').val('');
    $('#modalnuevaregional').modal('show');
});

init();