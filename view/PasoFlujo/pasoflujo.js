var tabla;

function init() {
    $("#paso_form").on("submit", function(e){
        guardaryeditar(e);
    })
}

function guardaryeditar(e){
    e.preventDefault();
    var formData = new FormData($("#paso_form")[0])
    $.ajax({
        url: "../../controller/flujopaso.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(datos){
            $("#paso_form")[0].reset();
            $("#paso_id").val('');
            $("#paso_orden").val('');
            $('#paso_nombre').val('');
            $('#usu_asig').val('');
            $("#modalnuevopaso").modal('hide');
            $("#paso_data").DataTable().ajax.reload();
            swal({
                title: "Guardado!",
                text: "Se ha guardado correctamente el nuevo registro.",
                type: "success",
                confirmButtonClass: "btn-success"
            });          
        }
    })
}

function ver(flujo_id) {
    window.location.href = '/view/PasoFlujo/?ID='+ flujo_id
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
}

$(document).ready(function () {

    cargarUsuarios();

    $('#flujo_id').val(getUrlParameter('ID'));

    tabla = $('#paso_data').dataTable({
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
            url: '../../controller/flujopaso.php?op=listar',
            type: 'post',
            data: {flujo_id: getUrlParameter('ID')},
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

function editar(paso_id) {
    $("#mdltitulo").html('Editar registro');

    $.post("../../controller/flujopaso.php?op=mostrar", {paso_id:paso_id}, function(data) {
        data = JSON.parse(data);
        $('#paso_id').val(data.paso_id);
        $('#paso_orden').val(data.paso_orden);
        $('#paso_nombre').val(data.paso_nombre);
        $('#usu_asig').val(data.usu_id);

    });    

    $("#modalnuevopaso").modal("show");
}
function eliminar(paso_id) {
    swal({
        title: "¿Estas que quieres eliminar esta flujo?",
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
            $.post("../../controller/flujopaso.php?op=eliminar", {paso_id:paso_id}, function(data) {
                $('#paso_data').DataTable().ajax.reload(); 
                swal({
                    title: "Eliminado!",
                    text: "flujo eliminada correctamente",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "La flujo no fue eliminada",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}

$(document).on("click", "#btnnuevopaso", function(){
    $("#mdltitulo").html('Nuevo registro');
    $("#paso_form")[0].reset();
    $("#modalnuevopaso").modal("show");
});

function cargarUsuarios() {
    $.post("../../controller/usuario.php?op=usuariosxrol", function(data) {
        $('#usu_asig').html('<option value="">Seleccionar un usuario</option>' + data);
    });

}


$('#modalnuevopaso').on('hidden.bs.modal', function () {
    $("#paso_form")[0].reset();
    $("#paso_id").val('');
    $("#paso_orden").val('');
    $('#paso_nombre').val('');
    $('#usu_asig').val('');
});

init();