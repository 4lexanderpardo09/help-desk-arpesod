var tabla;

function init() {
    $("#emp_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#emp_form")[0])
    $.ajax({
        url: "../../controller/empresa.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#emp_form")[0].reset();
            $("#emp_nom").html('');
            $("#emp_id").val('');
            $("#modalnuevaempresa").modal('hide');
            $("#emp_data").DataTable().ajax.reload();
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

    tabla = $('#emp_data').dataTable({
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
            url: '../../controller/empresa.php?op=listar',
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

function editar(emp_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/empresa.php?op=mostrar", {emp_id:emp_id}, function(data) {
        data = JSON.parse(data);
        $('#emp_id').val(data.emp_id);
        $('#emp_nom').val(data.emp_nom);

        

    });    

    $("#modalnuevaempresa").modal("show");
}
function eliminar(emp_id) {
    swal({
        title: "¿Estas que quieres eliminar esta empresa?",
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
            $.post("../../controller/empresa.php?op=eliminar", {emp_id:emp_id}, function(data) {
                $('#emp_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "empresa eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La empresa no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevoempresa", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#emp_form")[0].reset();
    $("#modalnuevaempresa").modal("show");
})

$('#modalnuevaempresa').on('hidden.bs.modal', function () {
    $("#emp_form")[0].reset();
    $("#emp_nom").html('');
    $("#emp_id").val('');
});

init();