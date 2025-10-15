<?php
class FlujoTransicion extends Conectar
{
    public function get_transiciones()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                t.transicion_id, 
                po.paso_nombre as paso_origen, 
                pd.paso_nombre as paso_destino, 
                t.condicion_clave, 
                t.condicion_nombre, 
                t.est
            FROM 
                tm_flujo_transiciones t
            INNER JOIN 
                tm_flujo_paso po ON t.paso_origen_id = po.paso_id
            LEFT JOIN 
                tm_flujo_paso pd ON t.ruta_id = pd.paso_id
            WHERE t.est = 1";

        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_transicion_por_decision($paso_origen_id, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_transiciones WHERE paso_origen_id = ? AND condicion_nombre = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->bindValue(2, $condicion_nombre);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_transiciones_por_paso($paso_origen_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // --- CONSULTA CORREGIDA ---
        $sql = "SELECT 
                    t.transicion_id,
                    po.paso_nombre as paso_origen,
                    r.ruta_nombre,  -- Traemos el nombre de la ruta
                    t.condicion_clave,
                    t.condicion_nombre,
                    t.paso_origen_id
                FROM
                    tm_flujo_transiciones t
                INNER JOIN
                    tm_flujo_paso po ON t.paso_origen_id = po.paso_id
                LEFT JOIN
                    tm_ruta r ON t.ruta_id = r.ruta_id -- La unión correcta es con la tabla tm_ruta
                WHERE
                    t.paso_origen_id = ? AND t.est = 1";
                    
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->execute();
        return $sql->fetchAll();
    }

    public function get_transicion_por_id($transicion_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_transiciones WHERE transicion_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $transicion_id);
        $sql->execute();
        return $resultado = $sql->fetch();
    }

    public function delete_transicion($transicion_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_flujo_transiciones SET est = 0 WHERE transicion_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $transicion_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert_transicion($paso_origen_id, $ruta_id, $condicion_clave, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_flujo_transiciones (paso_origen_id, ruta_id, condicion_clave, condicion_nombre, est) VALUES (?, ?, ?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->bindValue(2, $ruta_id);
        $sql->bindValue(3, $condicion_clave);
        $sql->bindValue(4, $condicion_nombre);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update_transicion($transicion_id, $paso_origen_id, $ruta_id, $condicion_clave, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_flujo_transiciones SET 
                paso_origen_id = ?, 
                ruta_id = ?, 
                condicion_clave = ?, 
                condicion_nombre = ?
            WHERE 
                transicion_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->bindValue(2, $ruta_id);
        $sql->bindValue(3, $condicion_clave);
        $sql->bindValue(4, $condicion_nombre);
        $sql->bindValue(5, $transicion_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
?>
