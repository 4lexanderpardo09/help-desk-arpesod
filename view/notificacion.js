// Asegúrate de que este archivo se carga DESPUÉS de que jQuery esté disponible.

$(document).ready(function() {

    // 1. Obtenemos el ID del usuario una sola vez.
    const usu_id = $('#user_idx').val();

    // Si no tenemos un ID de usuario, no hacemos nada más.
    if (!usu_id) {
        console.error("No se pudo encontrar el ID del usuario (user_idx).");
        return;
    }

    // --- CONFIGURACIÓN DEL WEBSOCKET ---

    // 2. Definimos la dirección de nuestro servidor WebSocket.
    // Usa 'ws://localhost:8080' para pruebas en tu PC.
    // Usa 'wss://mesadeayuda.electrocreditosdelcauca.com:8080' para producción (wss es para HTTPS).
    // El puerto :8080 es un ejemplo, podría ser otro si lo configuraste diferente.
    // La URL final, segura y profesional
    const socket_url = 'wss://mesadeayuda.electrocreditosdelcauca.com/websocket?userId=' + usu_id;
    const conn = new WebSocket(socket_url);

    // --- MANEJO DE EVENTOS DEL WEBSOCKET ---

    // Este evento se dispara cuando la conexión se establece con éxito.
    conn.onopen = function(e) {
        console.log("Conexión WebSocket establecida exitosamente.");
    };

    // ¡ESTE ES EL EVENTO MÁS IMPORTANTE!
    // Se dispara cada vez que el servidor nos envía un mensaje.
    conn.onmessage = function(e) {
        console.log("Mensaje recibido del servidor: ", e.data);

        // Convertimos el mensaje (que es un string JSON) a un objeto JavaScript.
        const data = JSON.parse(e.data);

        // Verificamos el tipo de mensaje que nos envió el servidor.
        if (data.type === 'new_ticket_notification') {

            // Reutilizamos tu código para mostrar la notificación emergente.
            $.notify({
                icon: 'glyphicon glyphicon-star',
                message: data.not_mensaje,
                url: 'https://mesadeayuda.electrocreditosdelcauca.com/view/DetalleTicket/?ID=' + data.tick_id
            });

            // Después de mostrar la notificación, actualizamos el menú y el contador.
            actualizarContadorDeNotificaciones();
            actualizarMenuDeNotificaciones();
        }
    };

    // Este evento se dispara si la conexión se cierra.
    conn.onclose = function(e) {
        console.log("Conexión WebSocket cerrada. Puede que necesites recargar la página.");
        // Aquí se podría implementar una lógica para intentar reconectar automáticamente.
    };

    // Este evento se dispara si hay un error en la conexión.
    conn.onerror = function(e) {
        console.error("Ha ocurrido un error en la conexión WebSocket.", e);
    };


    // --- FUNCIONES AUXILIARES ---

    // Función para actualizar el contador de notificaciones (el número rojo).
    function actualizarContadorDeNotificaciones() {
        $.post("../../controller/notificacion.php?op=contar", { usu_id: usu_id }, function(data) {
            const result = JSON.parse(data);
            $('#lblcontar').html(result.totalnotificaciones);

            if (result.totalnotificaciones > 0) {
                $('#dd-notification').addClass('active');
            } else {
                $('#dd-notification').removeClass('active');
            }
        });
    }

    // Función para actualizar la lista desplegable de notificaciones.
    function actualizarMenuDeNotificaciones() {
        $.post("../../controller/notificacion.php?op=notificacionespendientes", { usu_id: usu_id }, function(data) {
            $('#lblmenulist').html(data);
        });
    }


    // --- CARGA INICIAL DE DATOS ---

    // Al cargar la página, llenamos el contador y el menú con los datos actuales.
    actualizarContadorDeNotificaciones();
    actualizarMenuDeNotificaciones();

});


/**
 * Esta función se mantiene igual. Se llamará cuando el usuario
 * haga clic en una notificación específica del menú para marcarla como leída.
 * El 'onclick' debe estar en el HTML que genera 'notificacionespendientes'.
 */
function verNotificacion(not_id) {
    // Le decimos al servidor que esta notificación ha sido leída.
    $.post("../../controller/notificacion.php?op=leido", { not_id: not_id });

    // Aquí podrías, opcionalmente, volver a llamar a las funciones de actualizar
    // para que el contador y la lista se actualicen inmediatamente sin recargar la página.
}