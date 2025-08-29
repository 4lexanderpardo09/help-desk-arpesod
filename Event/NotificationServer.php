<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use React\EventLoop\LoopInterface;

// Asegúrate de incluir tu modelo y la clase de conexión
require dirname(__DIR__) . '/config/conexion.php';
require dirname(__DIR__) . '/models/Notificacion.php';

class NotificationServer implements MessageComponentInterface {
    protected $clients;
    protected $userConnections;
    protected $loop;
    protected $notificacionModel;

    // El constructor ahora recibe el Loop y crea una instancia de tu modelo
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->userConnections = [];
        $this->notificacionModel = new \Notificacion(); // Creamos una instancia de tu clase
        echo "Servidor de notificaciones iniciado...\n";
    }

    // CAMBIO 2: Añade esta nueva función
    public function setLoop(LoopInterface $loop){
        $this->loop = $loop;
    }

    // Este es el nuevo método que se ejecutará cada segundo
    public function checkNewNotifications() {
        // 1. Llama al nuevo método en tu modelo para buscar notificaciones con est=2
        $nuevasNotificaciones = $this->notificacionModel->get_nuevas_notificaciones_para_enviar();

        if (count($nuevasNotificaciones) > 0) {
            echo "Se encontraron " . count($nuevasNotificaciones) . " notificaciones nuevas.\n";
        }

        // 2. Recorre cada notificación encontrada
        foreach ($nuevasNotificaciones as $notificacion) {
            $userId = $notificacion['usu_id'];

            // 3. Revisa si el usuario está conectado
            if (isset($this->userConnections[$userId])) {
                $conn = $this->userConnections[$userId];

                // 4. Prepara y envía los datos al cliente a través del WebSocket
                $dataToSend = [
                    'type' => 'new_ticket_notification',
                    'not_id' => $notificacion['not_id'],
                    'tick_id' => $notificacion['tick_id'],
                    'not_mensaje' => $notificacion['not_mensaje']
                ];
                $conn->send(json_encode($dataToSend));
                echo "Notificación #{$notificacion['not_id']} enviada al usuario {$userId}.\n";

                // 5. Actualiza el estado de la notificación a 1 (mostrada) para no volver a enviarla
                $this->notificacionModel->update_notificacion_estado($notificacion['not_id']);
                echo "Notificación #{$notificacion['not_id']} actualizada a estado 1.\n";
            }
        }
    }

    // ... onOpen, onMessage, onClose, onError, etc. se mantienen como antes ...
    // Solo asegúrate de que onOpen siga guardando las conexiones en $this->userConnections
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        parse_str($conn->httpRequest->getUri()->getQuery(), $queryParams);
        $userId = $queryParams['userId'] ?? null;
        if ($userId) {
            $this->userConnections[$userId] = $conn;
            echo "Nueva conexión para el usuario {$userId}! ({$conn->resourceId})\n";
        }
    }
    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);
        $type = $data['type'] ?? '';

        // Reenviar el mensaje a los destinatarios apropiados
        if ($type === 'chat_message') {
            $para_usu_id = $data['para_usu_id'];
            if (isset($this->userConnections[$para_usu_id])) {
                $this->userConnections[$para_usu_id]->send($msg);
            }
        }
    }
    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        foreach ($this->userConnections as $userId => $connection) {
            if ($connection === $conn) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
        echo "Conexión {$conn->resourceId} se ha desconectado\n";
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ha ocurrido un error: {$e->getMessage()}\n";
        $conn->close();
    }
}