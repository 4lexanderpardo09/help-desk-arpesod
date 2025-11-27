<?php
class TicketParalelo extends Conectar
{

    public function insert_ticket_paralelo($tick_id, $paso_id, $usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_ticket_paralelo (tick_id, paso_id, usu_id, estado, fech_crea, est) VALUES (?, ?, ?, 'Pendiente', NOW(), 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $paso_id);
        $sql->bindValue(3, $usu_id);
        $sql->execute();
        return $conectar->lastInsertId();
    }

    public function get_ticket_paralelo_por_ticket_y_paso($tick_id, $paso_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT tp.*, u.usu_nom, u.usu_ape 
                FROM tm_ticket_paralelo tp
                INNER JOIN tm_usuario u ON tp.usu_id = u.usu_id
                WHERE tp.tick_id = ? AND tp.paso_id = ? AND tp.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $paso_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_ticket_paralelo_por_usuario($tick_id, $paso_id, $usu_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_ticket_paralelo WHERE tick_id = ? AND paso_id = ? AND usu_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $paso_id);
        $sql->bindValue(3, $usu_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function update_estado($paralelo_id, $estado, $comentario, $estado_tiempo_paso = null)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_ticket_paralelo SET estado = ?, comentario = ?, estado_tiempo_paso = ?, fech_cierre = NOW() WHERE paralelo_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $estado);
        $sql->bindValue(2, $comentario);
        $sql->bindValue(3, $estado_tiempo_paso);
        $sql->bindValue(4, $paralelo_id);
        $sql->execute();
    }

    public function check_todos_aprobados($tick_id, $paso_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        // Contar cuantos están pendientes
        $sql = "SELECT COUNT(*) as pendientes FROM tm_ticket_paralelo WHERE tick_id = ? AND paso_id = ? AND estado = 'Pendiente' AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $paso_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        return $resultado['pendientes'] == 0;
    }
}
