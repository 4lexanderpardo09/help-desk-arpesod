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
                    r.ruta_nombre,
                    pd.paso_nombre as paso_destino_nombre,
                    t.condicion_clave,
                    t.condicion_nombre,
                    t.paso_origen_id,
                    t.paso_destino_id,
                    t.ruta_id,
                    p_dest.requiere_seleccion_manual AS manual_paso,
                    p_ruta.requiere_seleccion_manual AS manual_ruta,
                    rp.paso_id AS ruta_first_step_id
                FROM
                    tm_flujo_transiciones t
                INNER JOIN
                    tm_flujo_paso po ON t.paso_origen_id = po.paso_id
                LEFT JOIN
                    tm_ruta r ON t.ruta_id = r.ruta_id
                LEFT JOIN
                    tm_flujo_paso pd ON t.paso_destino_id = pd.paso_id
                LEFT JOIN 
                    tm_flujo_paso p_dest ON t.paso_destino_id = p_dest.paso_id
                LEFT JOIN 
                    tm_ruta_paso rp ON t.ruta_id = rp.ruta_id AND rp.orden = 1
                LEFT JOIN 
                    tm_flujo_paso p_ruta ON rp.paso_id = p_ruta.paso_id
                WHERE
                    t.paso_origen_id = ? AND t.est = 1";

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->execute();
        $results = $sql->fetchAll(PDO::FETCH_ASSOC);

        // Process results to unify the flag and target step
        foreach ($results as &$row) {
            $row['requiere_seleccion_manual'] = 0;
            $row['target_step_id'] = null;

            if (!empty($row['ruta_id'])) {
                $row['target_step_id'] = $row['ruta_first_step_id'];
                if ($row['manual_ruta'] == 1) {
                    $row['requiere_seleccion_manual'] = 1;
                }
            } elseif (!empty($row['paso_destino_id'])) {
                $row['target_step_id'] = $row['paso_destino_id'];
                if ($row['manual_paso'] == 1) {
                    $row['requiere_seleccion_manual'] = 1;
                }
            }
            // Clean up temporary columns if desired, or leave them
            unset($row['manual_paso']);
            unset($row['manual_ruta']);
            unset($row['ruta_first_step_id']);
        }

        return $results;
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

    public function insert_transicion($paso_origen_id, $ruta_id, $paso_destino_id, $condicion_clave, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // Si ruta_id es vacío, lo guardamos como NULL
        $ruta_id = empty($ruta_id) ? null : $ruta_id;
        // Si paso_destino_id es vacío, lo guardamos como NULL
        $paso_destino_id = empty($paso_destino_id) ? null : $paso_destino_id;

        $sql = "INSERT INTO tm_flujo_transiciones (paso_origen_id, ruta_id, paso_destino_id, condicion_clave, condicion_nombre, est) VALUES (?, ?, ?, ?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->bindValue(2, $ruta_id);
        $sql->bindValue(3, $paso_destino_id);
        $sql->bindValue(4, $condicion_clave);
        $sql->bindValue(5, $condicion_nombre);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update_transicion($transicion_id, $paso_origen_id, $ruta_id, $paso_destino_id, $condicion_clave, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // Si ruta_id es vacío, lo guardamos como NULL
        $ruta_id = empty($ruta_id) ? null : $ruta_id;
        // Si paso_destino_id es vacío, lo guardamos como NULL
        $paso_destino_id = empty($paso_destino_id) ? null : $paso_destino_id;

        $sql = "UPDATE tm_flujo_transiciones SET 
                paso_origen_id = ?, 
                ruta_id = ?, 
                paso_destino_id = ?,
                condicion_clave = ?, 
                condicion_nombre = ?
            WHERE 
                transicion_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->bindValue(2, $ruta_id);
        $sql->bindValue(3, $paso_destino_id);
        $sql->bindValue(4, $condicion_clave);
        $sql->bindValue(5, $condicion_nombre);
        $sql->bindValue(6, $transicion_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
