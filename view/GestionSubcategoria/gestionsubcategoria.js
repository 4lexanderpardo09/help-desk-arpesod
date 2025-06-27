var tabla;

function init() {
    $("#cats_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#cats_form")[0])
    $.ajax({
        url: "../../controller/subcategoria.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            console.log(datos);
            $("#cats_nom").html('');
            $("#modalnuevasubcategoria").modal('hide');
            $("#cats_data").DataTable().ajax.reload();
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

    mostrarcategoria();

    tabla = $('#cats_data').dataTable({
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
            url: '../../controller/subcategoria.php?op=listar',
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

function editar(cats_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/subcategoria.php?op=mostrar", {cats_id:cats_id}, function(data) {
        data = JSON.parse(data);
        console.log(data);
 
        $("#cat_id").val(data.cat_id);
        $('#cats_id').val(data.cats_id);
        $('#cats_nom').val(data.cats_nom);

        

    });    

    $("#modalnuevasubcategoria").modal("show");
}
function eliminar(cats_id) {
    swal({
        title: "¿Estas que quieres eliminar esta subcategoria?",
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
            $.post("../../controller/subcategoria.php?op=eliminar", {cats_id:cats_id}, function(data) {
                $('#cats_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "subcategoria eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La subcategoria no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

function mostrarcategoria(){
    $.post("../../controller/categoria.php?op=getcombo", function (data) {
        $('#cat_id').html(data);
    });
}

$(document).on("click", "#btnnuevasubcategoria", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#cats_form")[0].reset();
    $("#modalnuevasubcategoria").modal("show");
})

init();