var tabla;

function init() {
    $("#dest_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#dest_form")[0])
    $.ajax({
        url: "../../controller/destinatarioticket.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#dest_form")[0].reset();
            $('#dest_id').val('');
            $('#answer_id').val('').trigger('change');
            $('#usu_id').val('').trigger('change');
            $('#dp_id').val('').trigger('change');
            $('#cat_id').val('').trigger('change');
            $('#cats_id').val('').trigger('change');
            $("#modalnuevodestinatario").modal('hide');
            $("#dest_data").DataTable().ajax.reload();
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

    tabla = $('#dest_data').dataTable({
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
            url: '../../controller/destinatarioticket.php?op=listar',
            type: 'post',
            dataType: 'json',
            error: function (e) {
                console.error(e.responseText);
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

    getCombos()


})

function getCombos(){

    $.post("../../controller/respuestarapida.php?op=combo",function (data) {
        $('#answer_id').html('<option value="">Seleccionar</option>' + data);
    });


    $.post("../../controller/departamento.php?op=combo",function (data) {
        $('#dp_id').html('<option value="">Seleccionar</option>' + data);
    });

    $('#usu_id').html('<option value="">Seleccionar</option>');
    $("#dp_id").off('change').on('change', function () {
        var dp_id = $(this).val();

        if(dp_id == 0){
            $('#usu_id').html('<option value="">Seleccionar</option>');
        }else{
            $.post("../../controller/usuario.php?op=usuariosxdepartamento", { dp_id: dp_id }, function (data) {
                $("#usu_id").html('<option value="">Seleccionar</option>' + data);
            });
        }

    });


    $.post("../../controller/categoria.php?op=getcombo",function (data) {
        $('#cat_id').html('<option value="">Seleccionar</option>' + data);
    });

    $('#cats_id').html('<option value="">Seleccionar</option>');
    $("#cat_id").off('change').on('change', function () {
            cat_id = $(this).val();
            if(cat_id == 0){
                $('#cats_id').html('<option value="">Seleccionar</option>');
            }else{
                $.post("../../controller/subcategoria.php?op=combo", {cat_id:cat_id}, function (data) {
                    $('#cats_id').html('<option value="">Seleccionar</option>' + data);
                });
            }
    });


}

function editar(dest_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/destinatarioticket.php?op=mostrar", {dest_id:dest_id}, function(data) {
        data = JSON.parse(data);
        $('#dest_id').val(data.dest_id);
        console.log(data.dest_id);
        
        $("#answer_id").val(data.answer_id).trigger('change');
        $('#usu_id').val(data.usu_id).trigger('change');
        $('#dp_id').val(data.dp_id).trigger('change');
        $('#cat_id').val(data.cat_id).trigger('change');
        setTimeout(function () {
            $('#cats_id').val(data.cats_id).trigger('change');
        }, 300);
    });    

    $("#modalnuevodestinatario").modal("show");
}
function eliminar(dest_id) {
    swal({
        title: "¿Estas que quieres eliminar esta destinatarioticket?",
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
            $.post("../../controller/destinatarioticket.php?op=eliminar", {dest_id:dest_id}, function(data) {
                $('#dest_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "destinatarioticket eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La destinatarioticket no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevodestinatario", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#dest_form")[0].reset();
    $("#modalnuevodestinatario").modal("show");
})

$('#modalnuevodestinatario').on('hidden.bs.modal', function () {
    // Limpiar el formulario al cerrar el modal
    $("#dest_form")[0].reset();
    $('#dest_id').val('');
    $('#answer_id').val('').trigger('change');
    $('#usu_id').val('').trigger('change');
    $('#dp_id').val('').trigger('change');
    $('#cat_id').val('').trigger('change');
    $('#cats_id').val('').trigger('change');
});


init();