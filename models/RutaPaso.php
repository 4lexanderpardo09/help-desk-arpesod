<?php
require_once('../config/conexion.php');

class RutaPaso extends Conectar
{

    public function get_paso_por_orden($ruta_id, $orden)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT rp.*, fp.paso_nombre, fp.cargo_id_asignado, fp.es_tarea_nacional, fp.requiere_seleccion_manual
                FROM tm_ruta_paso rp
                JOIN tm_flujo_paso fp ON rp.paso_id = fp.paso_id
                WHERE rp.ruta_id = ? AND rp.orden = ? AND rp.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta_id);
        $sql->bindValue(2, $orden);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_paso_por_id_de_ruta($paso_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT
                    rp.ruta_paso_id,
                    rp.ruta_id,
                    rp.paso_id,
                    fp.paso_nombre,
                    rp.orden
                FROM
                    tm_ruta_paso rp
                INNER JOIN tm_flujo_paso fp ON rp.paso_id = fp.paso_id
                WHERE
                    rp.paso_id = ?
                AND rp.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_paso_en_ruta($ruta_id, $paso_id, $orden)
    {
        $conectar = parent::Conexion();
        $sql = "INSERT INTO tm_ruta_paso (ruta_id, paso_id, orden, est) VALUES (?, ?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta_id);
        $sql->bindValue(2, $paso_id);
        $sql->bindValue(3, $orden);
        $sql->execute();
    }

    public function get_pasos_por_ruta($ruta_id)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT rp.ruta_paso_id, rp.orden, fp.paso_nombre, rp.paso_id 
                FROM tm_ruta_paso rp
                JOIN tm_flujo_paso fp ON rp.paso_id = fp.paso_id
                WHERE rp.ruta_id = ? AND rp.est = 1 
                ORDER BY rp.orden ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_paso_de_ruta($ruta_paso_id)
    {
        $conectar = parent::Conexion();
        $sql = "UPDATE tm_ruta_paso SET est = 0 WHERE ruta_paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $ruta_paso_id);
        $sql->execute();
    }

    // Función para reordenar (opcional pero muy útil)
    public function update_orden_pasos($orden_data)
    {
        $conectar = parent::Conexion();
        foreach ($orden_data as $item) {
            $sql = "UPDATE tm_ruta_paso SET orden = ? WHERE ruta_paso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $item['orden']);
            $sql->bindValue(2, $item['id']);
            $sql->execute();
        }
    }
}
