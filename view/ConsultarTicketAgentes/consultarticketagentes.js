var tabla;
var usu_id = $('#user_idx').val()
var usu_asig = $('#user_idx').val()
var rol_id = $('#rol_idx').val()

function init(){
}


$(document).ready(function() {
    if(rol_id == 2){

        tabla=$('#ticket_data').dataTable({
            "aProcessing": true,
            "aServerSide": true,
            dom: 'Bfrtip',
            "searching": true,
            lengthChange: false,
            colRecorder: true,
            "buttons":[
                    'copyHtml5',
                    'excelHtml5',
                    'csvHtml5',
                    'pdfHtml5',
                    ],
            "ajax": {
                url: '../../controller/ticket.php?op=listar_x_usu',
                type: 'post',
                dataType: 'json',
                data:{usu_id: usu_id},
                error: function(e){
                    console.log(e.responseText);
                }
            },
            "bDestroy": true,
            "responsive": true,
            "bInfo": true,
            "isDisplayLength": 10,
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
    }else {

    }

})


function ver(tick_id){
    window.location.href = '/view/DetalleTicket/?ID='+ tick_id
}

function cambiarEstado(tick_id){
    swal({
        title: "Re-abrir ticket",
        text: "¿Estas que quieres re abrir el ticket?",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-warning",
        confirmButtonText: "Si, re-abir!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            $.post("../../controller/ticket.php?op=reabrir", {tick_id:tick_id, usu_id:usu_id}, function(data) {
                $('#ticket_data').DataTable().ajax.reload(); 
                swal({
                    title: "Ticket!",
                    text: "Ticket nuevamente abierto",
                    type: "success",
                    confirmButtonClass: "btn-success"
                }); 
            });
        } else {
            swal({
                title: "Cancelado",
                text: "El ticket no se abrio.",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });
}


init();