$(document).ready(function() {
    const usu_id = $('#user_idx').val();

    if (!usu_id) {
        console.error("No se pudo encontrar el ID del usuario (user_idx).");
        return;
    }

    // --- CONFIGURACIÓN DEL WEBSOCKET ---

    const isLocal = ['localhost', '127.0.0.1'].includes(window.location.hostname);
    const wsProtocol = window.location.protocol === 'https:' ? 'wss' : 'ws';

    let wsHost, wsPath;

    if (isLocal) {
        wsHost = 'localhost:8080';
        wsPath = '/';
    } else {
        wsHost = 'mesadeayuda.electrocreditosdelcauca.com';
        wsPath = '/websocket';
    }

    const socket_url = `${wsProtocol}://${wsHost}${wsPath}?userId=${usu_id}`;

    const conn = new WebSocket(socket_url);

    // --- MANEJO DE EVENTOS DEL WEBSOCKET ---

    conn.onmessage = function(e) {
        const data = JSON.parse(e.data);

        if (data.type === 'new_ticket_notification') {
            $.notify({
                icon: 'glyphicon glyphicon-star',
                message: data.not_mensaje,
                url: '/view/DetalleTicket/?ID=' + data.tick_id
            });

            actualizarContadorDeNotificaciones();
            actualizarMenuDeNotificaciones();
        }
    };

    // --- FUNCIONES AUXILIARES ---

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

    function actualizarMenuDeNotificaciones() {
        $.post("../../controller/notificacion.php?op=notificacionespendientes", { usu_id: usu_id }, function(data) {
            $('#lblmenulist').html(data);
        });
    }

    actualizarContadorDeNotificaciones();
    actualizarMenuDeNotificaciones();
});

function verNotificacion(not_id) {
    $.post("../../controller/notificacion.php?op=leido", { not_id: not_id });
}
