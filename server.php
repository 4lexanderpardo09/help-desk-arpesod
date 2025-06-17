<?php
// server.php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

// Carga el autoloader de Composer. ¡Esta línea es vital!
require dirname(__FILE__) . '/vendor/autoload.php';
require dirname(__FILE__) . '/Event/NotificationServer.php';


// --- Inicia la configuración del servidor ---

// 1. Creamos una instancia de nuestra aplicación de notificaciones
$notificationApp = new NotificationServer();

// 2. Creamos el servidor WebSocket que envuelve nuestra aplicación
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $notificationApp
        )
    ),
    8080 // El puerto donde escuchará
);

// 3. Pasamos el "Loop de Eventos" del servidor a nuestra aplicación
// para que pueda usarlo para las tareas periódicas.
$notificationApp->setLoop($server->loop);

// 4. Añadimos la tarea periódica que revisará la base de datos cada 2 segundos.
$server->loop->addPeriodicTimer(2, function() use ($notificationApp) {
    $notificationApp->checkNewNotifications();
});


echo "Servidor WebSocket iniciado y escuchando en el puerto 8080...\n";

// 5. Corremos el servidor (esto mantiene el script vivo)
$server->run();