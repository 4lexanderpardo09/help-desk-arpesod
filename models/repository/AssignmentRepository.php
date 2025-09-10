<?php
namespace models\repository;

use PDO;
use Exception;

class AssignmentRepository
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec("SET NAMES 'utf8'");
    }

    public function insertAssignment($tick_id, $usu_asig, $how_asig, $paso_id)
    {
        try {
            $sql = "INSERT INTO th_ticket_asignacion (tick_id, usu_asig, how_asig, paso_id, fech_asig, asig_comentario, est)
                    VALUES (?, ?, ?, ?, NOW(), 'Ticket abierto', 1);";
            $sql = $this->pdo->prepare($sql);
            $sql->bindValue(1, $tick_id);
            $sql->bindValue(2, $usu_asig);
            $sql->bindValue(3, $how_asig);
            $sql->bindValue(4, $paso_id);
            $sql->execute();

            return (int)$this->pdo->lastInsertId();

        } catch (Exception $e) {
            error_log("AssignmentRepository::insertAssignment() error" . $e->getMessage());
            throw $e;
        }
    }
}

?>