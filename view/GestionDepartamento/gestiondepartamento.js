var tabla;

function init() {
    $("#dp_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#dp_form")[0])
    $.ajax({
        url: "../../controller/departamento.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#dp_form")[0].reset();
            $("#pd_nom").html('');
            $('#dp_id').val('');
            $("#modalnuevodepartamento").modal('hide');
            $("#dp_data").DataTable().ajax.reload();
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

    tabla = $('#dp_data').dataTable({
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
            url: '../../controller/departamento.php?op=listar',
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

function editar(dp_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/departamento.php?op=mostrar", {dp_id:dp_id}, function(data) {
        data = JSON.parse(data);
        $('#dp_id').val(data.dp_id);
        $('#dp_nom').val(data.dp_nom);

        

    });    

    $("#modalnuevodepartamento").modal("show");
}
function eliminar(dp_id) {
    swal({
        title: "¿Estas que quieres eliminar esta departamento?",
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
            $.post("../../controller/departamento.php?op=eliminar", {dp_id:dp_id}, function(data) {
                $('#dp_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "departamento eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La departamento no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevodepartamento", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#dp_form")[0].reset();
    $("#modalnuevodepartamento").modal("show");
});

$('#modalnuevodepartamento').on('hidden.bs.modal', function() {
    $("#dp_form")[0].reset();
    $("#pd_nom").html('');
    $('#dp_id').val('');
});

init();