var tabla;

function init() {
    $("#answer_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#answer_form")[0]);
    formData.append('es_error_proceso', $('#es_error_proceso').is(':checked') ? 1 : 0);

    $.ajax({
        url: "../../controller/respuestarapida.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            console.log(datos);
            $("#answer_form")[0].reset();
            $("#modalnuevarespuesta").modal('hide');
            $("#answer_data").DataTable().ajax.reload();
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

    tabla = $('#answer_data').dataTable({
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
            url: '../../controller/respuestarapida.php?op=listar',
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


})

function editar(answer_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/respuestarapida.php?op=mostrar", {answer_id:answer_id}, function(data) {
        data = JSON.parse(data);
        $('#answer_id').val(data.answer_id);
        $('#answer_nom').val(data.answer_nom);
        $('#es_error_proceso').prop('checked', data.es_error_proceso == 1);

    });    

    $("#modalnuevarespuesta").modal("show");
}
function eliminar(answer_id) {
    swal({
        title: "¿Estas que quieres eliminar esta respuesta?",
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
            $.post("../../controller/respuestarapida.php?op=eliminar", {answer_id:answer_id}, function(data) {
                $('#answer_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "respuesta eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La respuesta no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevarespuesta", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#answer_form")[0].reset();
    $("#modalnuevarespuesta").modal("show");
})

$('#modalnuevarespuesta').on('hidden.bs.modal', function () {
    // Limpiar el formulario al cerrar el modal
    $("#answer_form")[0].reset();
    $("#answer_id").val('');
    $('#answer_nom').html('');
});


init();