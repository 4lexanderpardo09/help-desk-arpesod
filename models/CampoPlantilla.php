<?php
class CampoPlantilla extends Conectar
{
    // --- tm_campo_plantilla methods ---

    public function insert_campo($paso_id, $campo_nombre, $campo_codigo, $coord_x, $coord_y, $pagina, $campo_tipo = 'text')
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_campo_plantilla (paso_id, campo_nombre, campo_codigo, coord_x, coord_y, pagina, campo_tipo, est, fech_crea) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW())";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->bindValue(2, $campo_nombre);
        $sql->bindValue(3, $campo_codigo);
        $sql->bindValue(4, $coord_x);
        $sql->bindValue(5, $coord_y);
        $sql->bindValue(6, $pagina);
        $sql->bindValue(7, $campo_tipo);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update_campo($campo_id, $campo_nombre, $campo_codigo, $coord_x, $coord_y, $pagina, $campo_tipo = 'text')
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_campo_plantilla SET campo_nombre=?, campo_codigo=?, coord_x=?, coord_y=?, pagina=?, campo_tipo=? WHERE campo_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $campo_nombre);
        $sql->bindValue(2, $campo_codigo);
        $sql->bindValue(3, $coord_x);
        $sql->bindValue(4, $coord_y);
        $sql->bindValue(5, $pagina);
        $sql->bindValue(6, $campo_tipo);
        $sql->bindValue(7, $campo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete_campo($campo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_campo_plantilla SET est=0 WHERE campo_id=?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $campo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_campos_por_paso($paso_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_campo_plantilla WHERE paso_id=? AND est=1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_campos_por_flujo($flujo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT c.* 
                FROM tm_campo_plantilla c
                JOIN tm_flujo_paso p ON c.paso_id = p.paso_id
                WHERE p.flujo_id = ? AND c.est = 1 AND p.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $flujo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    // --- td_ticket_campo_valor methods ---

    public function insert_ticket_valor($tick_id, $campo_id, $valor)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO td_ticket_campo_valor (tick_id, campo_id, valor, est, fech_crea) VALUES (?, ?, ?, 1, NOW())";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $campo_id);
        $sql->bindValue(3, $valor);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_valores_por_ticket($tick_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT v.*, c.campo_nombre, c.campo_codigo, c.coord_x, c.coord_y, c.pagina, c.campo_tipo 
                FROM td_ticket_campo_valor v 
                INNER JOIN tm_campo_plantilla c ON v.campo_id = c.campo_id 
                WHERE v.tick_id=? AND v.est=1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_valor_campo($tick_id, $campo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT valor FROM td_ticket_campo_valor WHERE tick_id=? AND campo_id=? AND est=1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $tick_id);
        $sql->bindValue(2, $campo_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['valor'] : null;
    }
}
