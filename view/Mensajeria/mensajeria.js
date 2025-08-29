var tabla;
var conn;
var current_conv_id;
var current_para_usu_id;

function init(){

}

function actualizar_badge(){
    $.post("../../controller/mensaje.php?op=get_total_mensajes_no_leidos", function(data){
        var total = JSON.parse(data).total;
        if(total > 0){
            $('.label-danger').text(total).show();
        }else{
            $('.label-danger').hide();
        }
    });
}

$(document).ready(function(){
    Notification.requestPermission();
    actualizar_badge();

    var usu_id = $('#usu_id').val();
    conn = new WebSocket('ws://localhost:8080?userId=' + usu_id);

    conn.onopen = function(e) {
        console.log("Connection established!");
    };

    conn.onmessage = function(e) {
        var data = JSON.parse(e.data);
        if (data.type === 'chat_message' && data.conv_id == current_conv_id) {
            var html = $('#mensajes_body').html();
            var bubble_class = (data.de_usu_id == usu_id) ? "sent" : "received";
            html += "<div class='chat-bubble " + bubble_class + "'><strong>" + data.de_usu_nom + ":</strong> " + data.mensaje + " <span class='text-muted'>" + data.fech_crea + "</span></div>";
            $('#mensajes_body').html(html);
            actualizar_badge();
        }
    };

    tabla=$('#conversacion_data').dataTable({
		"aProcessing": true,
        "aServerSide": true,
        dom: 'Bfrtip',
        "searching": true,
        lengthChange: false,
        colReorder: true,
        buttons: [				  
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdfHtml5'
				],
        "ajax":{
            url: '../../controller/mensaje.php?op=listar_conversaciones',
            type : "post",
			dataType : "json",					
			
            error: function(e){
				console.log(e.responseText);	
            }
        },
		"bDestroy": true,
		"responsive": true,
		"bInfo":true,
		"iDisplayLength": 10,
		"autoWidth": false,
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
		}	
    }).DataTable();

    $.post("../../controller/usuario.php?op=usuariosxrol", function (data) {
        $('#usuario_id').html(data);
    });
});

$('#btnnuevo').on('click', function(){
    $('#modalnuevomensaje').modal('show');
});

$('#btncrear').on('click', function(){
    var para_usu_id = $('#usuario_id').val();
    $.ajax({
        url: '../../controller/mensaje.php?op=crear_conversacion',
        type: 'POST',
        data: {para_usu_id: para_usu_id},
        dataType: 'json',
        success: function(response){
            ver_mensajes(response.conv_id, para_usu_id, $('#usuario_id option:selected').text());
            $('#modalnuevomensaje').modal('hide');
        }
        });
});

function ver_mensajes(conv_id, para_usu_id, nombre_conversacion){
    current_conv_id = conv_id;
    current_para_usu_id = para_usu_id;

    $('#mensajes_view').show();
    $('#nombre_conversacion').text(nombre_conversacion);

    var usu_id = $('#usu_id').val();

    function cargar_mensajes(){
        $.post("../../controller/mensaje.php?op=listar_mensajes", {conv_id: conv_id}, function(data){
            var mensajes = JSON.parse(data);
            var html = "";
            for(var i = 0; i < mensajes.length; i++){
                var bubble_class = (mensajes[i].de_usu_id == usu_id) ? "sent" : "received";
                html += "<div class='chat-bubble " + bubble_class + "'><strong>" + mensajes[i].de_usu_nom + ":</strong> " + mensajes[i].mensaje + " <span class='text-muted'>" + mensajes[i].fech_crea + "</span></div>";
            }
            $('#mensajes_body').html(html);
        });
    }

    cargar_mensajes();

    $('#enviar_mensaje').off('click').on('click', function(){
        var mensaje_texto = $('#mensaje_texto').val();
        if (mensaje_texto.trim() === '') return;

        $.post("../../controller/mensaje.php?op=enviar_mensaje", {conv_id: conv_id, mensaje: mensaje_texto}, function(){
            // Enviar mensaje por WebSocket
            var message_data = {
                type: 'chat_message',
                conv_id: conv_id,
                de_usu_id: usu_id,
                para_usu_id: current_para_usu_id,
                mensaje: mensaje_texto,
                de_usu_nom: 'Tú', // O obtener el nombre del usuario actual
                fech_crea: new Date().toISOString().slice(0, 19).replace('T', ' ')
            };
            conn.send(JSON.stringify(message_data));

            // Añadir el mensaje enviado a la vista localmente
            var html = $('#mensajes_body').html();
            var bubble_class = "sent";
            html += "<div class='chat-bubble " + bubble_class + "'><strong>Tú:</strong> " + mensaje_texto + " <span class='text-muted'>Ahora</span></div>";
            $('#mensajes_body').html(html);

            $('#mensaje_texto').val("");
            actualizar_badge();
        });
    });
}

init();
