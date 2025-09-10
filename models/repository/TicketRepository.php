<?php
namespace models\repository;

use PDO;
use Exception;

class TicketRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->pdo->exec("SET NAMES 'utf8'");
    }

    public function insertTicket($usu_id, $cat_id, $cats_id, $pd_id, $tick_titulo, $tick_descrip, $error_proceso, $usu_asig, $paso_actual_id = null, $how_asig, $emp_id, $dp_id)
    {
        try {
            $sql = "INSERT INTO tm_ticket (tick_id,usu_id,cat_id,cats_id,pd_id,tick_titulo,tick_descrip,tick_estado,error_proceso,fech_crea,usu_asig,paso_actual_id,how_asig,est,emp_id,dp_id) VALUES (NULL,?,?,?,?,?,?,'Abierto',?,NOW(),?,?,?, '1',?,?)";
            $sql = $this->pdo->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->bindValue(2, $cat_id);
            $sql->bindValue(3, $cats_id);
            $sql->bindValue(4, $pd_id);
            $sql->bindValue(5, $tick_titulo);
            $sql->bindValue(6, $tick_descrip);
            $sql->bindValue(7, $error_proceso);
            $sql->bindValue(8, $usu_asig);
            $sql->bindValue(9, $paso_actual_id);
            $sql->bindValue(10, $how_asig);
            $sql->bindValue(11, $emp_id);
            $sql->bindValue(12, $dp_id);
            $sql->execute();

            return (int)$this->pdo->lastInsertId();

        } catch (Exception $e) {
            error_log("TicketRepository::insertTicket() error" . $e->getMessage());
            throw $e;
        }
    }
}
?>