<?php
namespace models\repository;

use PDO;
use Exception;

class NotificationRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec("SET NAMES 'utf8'");
    }

    public function insertNotification($usu_id, $not_mensaje, $tick_id)
    {
        try {
            $sql = "INSERT INTO tm_notificacion (usu_id, not_mensaje, tick_id, fech_not, est) VALUES (?, ?, ?, NOW(), 2)";
            $sql = $this->pdo->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->bindValue(2, $not_mensaje);
            $sql->bindValue(3, $tick_id);
            $sql->execute();

            return (int)$this->pdo->lastInsertId();

        } catch (Exception $e) {
            error_log("NotificationRepository::insertNotification() error" . $e->getMessage());
            throw $e;
        }
    }

}

?>