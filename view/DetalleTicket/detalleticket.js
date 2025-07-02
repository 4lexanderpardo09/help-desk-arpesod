function init(){

}


$(document).ready(function() {

    var rol_id = $("#rol_idx").val();

    var tick_id = getUrlParameter('ID'); 

    listarDetalle(tick_id);

    $('#tickd_descrip').summernote({
        height: 200,
        lang: "es-ES",
        callbacks: {
            onImageUpload: function(image) {
                console.log("Image detect...");
                myimagetreat(image[0]);
            },
            onPaste: function (e) {
                console.log("Text detect...");
            }
        },
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]   
    });

    $('#tickd_descripusu').summernote({
        height: 200,
        lang: 'es-ES',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough', 'superscript', 'subscript']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']]
        ]
    });

    $('#tickd_descripusu').summernote('disable');

    tabla = $('#documentos_data').dataTable({
        "aProcessing": true,
        "aServerSide": true,
        "searching": false,  
        "info": false,    
        lengthChange: false,
        colReorder: true,
        "buttons": [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5',
        ],
        "ajax": {
            url: '../../controller/documento.php?op=listar',
            type: 'post',
            data: {tick_id: tick_id},
            dataType: 'json',
            error: function (e) {
                console.log(e.responseText);
            }
        },
        "bDestroy": true,
        "responsive": true,
        "bInfo": true,
        "iDisplayLength": 3,
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

    getRespuestasRapidas();

});

function getRespuestasRapidas(){

    $.post("../../controller/respuestarapida.php?op=combo",function(data) {
        $('#answer_id').html('<option value="">Seleccionar</option>' + data);
        
    });

}

function getDestinatarios(cats_id){
    
    var dp_idx = $('#dp_idx').val();

    $("#answer_id").off('change').on('change', function () {
    answer_id = $(this).val();

    if(answer_id == 0){
        $('#dest_id').html('<option value="">Seleccionar</option>');
    }else{
        $.post("../../controller/destinatarioticket.php?op=combo", {answer_id:answer_id,dp_id:dp_idx,cats_id:cats_id}, function(data) {
            $('#dest_id').html('<option value="">Seleccionar</option>' + data);
            
        });
    }


    });

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

$(document).on('click', '#btnenviar', function() {

    if($('#tickd_descrip').summernote('isEmpty')) {
        swal("Atención", "Debe ingresar una respuesta", "warning");
        return false;
    }

    var tick_id = getUrlParameter('ID');
    var usu_id = $('#user_idx').val();
    var tickd_descrip = $('#tickd_descrip').val();  

    var formData = new FormData($('#detalle_form')[0])

    formData.append("tick_id", tick_id);
    formData.append("usu_id", usu_id);
    formData.append("tickd_descrip", tickd_descrip);

    var totalFile = $('#fileElem').val().length;
    for (var i = 0; i < totalFile; i++) {
        formData.append('files[]', $('#fileElem')[0].files[i]);
     }

        $.ajax({
            url: "../../controller/ticket.php?op=insertdetalle",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                console.log(data);
                var resultado = JSON.parse(data);

                listarDetalle(tick_id);

                swal("Correcto", "Respuesta enviada correctamente", "success");

                // Limpiar el formulario 
                $('#tickd_descrip').summernote('reset');
                $('#fileElem').val('');
            }
        })
         


});

$(document).on('click', '#btncerrarticket', function() {
    swal({
        title: "¿Estas seguro que quieres cerrar el ticket?",
        text: "Una vez cerrado no podrás volver a abrirlo",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Si, cerrar ticket!",
        cancelButtonText: "No, cancelar!",
        closeOnConfirm: false,
        closeOnCancel: false
    },
    function(isConfirm) {
        if (isConfirm) {
            var tick_id = getUrlParameter('ID');
            var usu_id = $('#user_idx').val();
            updateTicket(tick_id, usu_id);

            setTimeout(function() {
                $.post("../../controller/email.php?op=ticket_cerrado", { tick_id: tick_id }, function(resp) {
                }).fail(function(err) {
                    console.error("Error al enviar el correo:", err.responseText);
                });
            }, 0);

        } else {
            swal({
                title: "Cancelado",
                text: "El ticket sigue abierto.",
                type: "error",
                confirmButtonClass: "btn-danger"
                
            });
        }
    });

});

function updateTicket(tick_id, usu_id) {
    $.post("../../controller/ticket.php?op=update", {tick_id: tick_id, usu_id: usu_id}, function(data) {
        swal({
            title: "Cerrado!",
            text: "El ticket ha sido cerrado correctamente.",
            type: "success",
            confirmButtonClass: "btn-success"
        },function(){
            listarDetalle(tick_id);
        }
    );
    });

}

function listarDetalle(tick_id){

    $.post("../../controller/ticket.php?op=listardetalle",{tick_id: tick_id}, function(data) {
        $('#lbldetalle').html(data);
        
    });

    $.post("../../controller/ticket.php?op=mostrar",{tick_id: tick_id}, function(data) {
        data = JSON.parse(data);
        
        $('#lbltickestado').html(data.tick_estado);
        $('#lblprioridad').html(data.pd_nom);
        $('#lblnomusuario').html(data.usu_nom + ' ' + data.usu_ape);    
        $('#lblfechacrea').html(data.fech_crea);
        $('#lblticketid').html("Detalle del tikect #" + data.tick_id);
        $('#cat_id').val(data.cat_nom);
        $('#cats_id').val(data.cats_nom);
        $('#tick_titulo').val(data.tick_titulo);
        $('#tickd_descripusu').summernote('code',data.tick_descrip);
        if(data.tick_estado_texto == 'Cerrado'){
            $('#boxdetalleticket').hide();
        };

        var usu_id = $('#user_idx').val();
        if(usu_id != data.usu_asig){
            $("#btncerrarticket").addClass('hidden');
        };

        getDestinatarios(data.cats_id);

    });

}


init();