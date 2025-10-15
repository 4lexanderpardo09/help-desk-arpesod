<?php
class Ruta extends Conectar
{
    /**
     * Obtener todas las rutas de un flujo (activas).
     * @param int $flujo_id
     * @return array
     */
    public function get_rutas_por_flujo($flujo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT ruta_id, flujo_id, ruta_nombre, est
                FROM tm_ruta
                WHERE flujo_id = ? AND est = 1
                ORDER BY ruta_id ASC";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $flujo_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una ruta por su id.
     * @param int $ruta_id
     * @return array|false
     */
    public function get_ruta_por_id($ruta_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT ruta_id, flujo_id, ruta_nombre, est
                FROM tm_ruta
                WHERE ruta_id = ? AND est = 1";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ruta_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insertar una nueva ruta. Devuelve el ID insertado o false.
     * @param int $flujo_id
     * @param string $ruta_nombre
     * @return int|false
     */
    public function insert_ruta($flujo_id, $ruta_nombre)
    {
        try {
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_ruta (flujo_id, ruta_nombre, est) VALUES (?, ?, 1)";
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $flujo_id, PDO::PARAM_INT);
            $stmt->bindValue(2, $ruta_nombre, PDO::PARAM_STR);
            $stmt->execute();
            return (int)$conectar->lastInsertId();
        } catch (Exception $e) {
            // opcional: log($e->getMessage());
            return false;
        }
    }

    /**
     * Actualizar nombre de ruta.
     * @param int $ruta_id
     * @param string $ruta_nombre
     * @return bool
     */
    public function update_ruta($ruta_id, $ruta_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_ruta SET ruta_nombre = ? WHERE ruta_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ruta_nombre, PDO::PARAM_STR);
        $stmt->bindValue(2, $ruta_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Borrado lógico (soft delete) de una ruta.
     * @param int $ruta_id
     * @return bool
     */
    public function delete_ruta($ruta_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_ruta SET est = 0 WHERE ruta_id = ?";
        $stmt = $conectar->prepare($sql);
        $stmt->bindValue(1, $ruta_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
