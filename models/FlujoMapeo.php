<?php
class FlujoMapeo extends Conectar
{

    /**
     * Inserta una nueva regla de mapeo y sus relaciones con cargos creadores y asignados.
     * @param int   $cats_id            El ID de la subcategoría.
     * @param array $creador_car_ids    Un array con los IDs de los cargos creadores.
     * @param array $asignado_car_ids   Un array con los IDs de los cargos asignados.
     */
    public function insert_flujo_mapeo($cats_id, $creador_car_ids, $asignado_car_ids, $creador_per_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Insertar la regla principal para obtener el ID
        $sql = "INSERT INTO tm_regla_mapeo (cats_id, est) VALUES (?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->execute();
        $regla_id = $conectar->lastInsertId();

        // 2. Insertar las relaciones con los cargos CREADORES
        if (is_array($creador_car_ids)) {
            foreach ($creador_car_ids as $car_id) {
                $sql_creador = "INSERT INTO regla_creadores (regla_id, creador_car_id) VALUES (?, ?)";
                $sql_creador = $conectar->prepare($sql_creador);
                $sql_creador->bindValue(1, $regla_id);
                $sql_creador->bindValue(2, $car_id);
                $sql_creador->execute();
            }
        }

        // 3. Insertar las relaciones con los perfiles CREADORES
        if (is_array($creador_per_ids)) {
            foreach ($creador_per_ids as $per_id) {
                $sql_perfil = "INSERT INTO regla_creadores_perfil (regla_id, creator_per_id, est) VALUES (?, ?, 1)";
                $sql_perfil = $conectar->prepare($sql_perfil);
                $sql_perfil->bindValue(1, $regla_id);
                $sql_perfil->bindValue(2, $per_id);
                $sql_perfil->execute();
            }
        }

        // 4. Insertar las relaciones con los cargos ASIGNADOS
        if (is_array($asignado_car_ids)) {
            foreach ($asignado_car_ids as $car_id) {
                $sql_asignado = "INSERT INTO regla_asignados (regla_id, asignado_car_id) VALUES (?, ?)";
                $sql_asignado = $conectar->prepare($sql_asignado);
                $sql_asignado->bindValue(1, $regla_id);
                $sql_asignado->bindValue(2, $car_id);
                $sql_asignado->execute();
            }
        }
    }

    /**
     * Actualiza una regla de mapeo. El método es "borrar y volver a insertar" las relaciones.
     */
    public function update_flujo_mapeo($regla_id, $cats_id, $creador_car_ids, $asignado_car_ids, $creador_per_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Actualizar la regla principal
        $sql = "UPDATE tm_regla_mapeo SET cats_id = ? WHERE regla_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->bindValue(2, $regla_id);
        $sql->execute();

        // 2. Borrar todas las relaciones antiguas para esta regla
        $sql_del_creador = "DELETE FROM regla_creadores WHERE regla_id = ?";
        $sql_del_creador = $conectar->prepare($sql_del_creador);
        $sql_del_creador->bindValue(1, $regla_id);
        $sql_del_creador->execute();

        $sql_del_perfil = "DELETE FROM regla_creadores_perfil WHERE regla_id = ?";
        $sql_del_perfil = $conectar->prepare($sql_del_perfil);
        $sql_del_perfil->bindValue(1, $regla_id);
        $sql_del_perfil->execute();

        $sql_del_asignado = "DELETE FROM regla_asignados WHERE regla_id = ?";
        $sql_del_asignado = $conectar->prepare($sql_del_asignado);
        $sql_del_asignado->bindValue(1, $regla_id);
        $sql_del_asignado->execute();

        // 3. Re-insertar las nuevas relaciones
        if (is_array($creador_car_ids)) {
            foreach ($creador_car_ids as $car_id) {
                $sql_creador = "INSERT INTO regla_creadores (regla_id, creador_car_id) VALUES (?, ?)";
                $sql_creador = $conectar->prepare($sql_creador);
                $sql_creador->bindValue(1, $regla_id);
                $sql_creador->bindValue(2, $car_id);
                $sql_creador->execute();
            }
        }

        if (is_array($creador_per_ids)) {
            foreach ($creador_per_ids as $per_id) {
                $sql_perfil = "INSERT INTO regla_creadores_perfil (regla_id, creator_per_id, est) VALUES (?, ?, 1)";
                $sql_perfil = $conectar->prepare($sql_perfil);
                $sql_perfil->bindValue(1, $regla_id);
                $sql_perfil->bindValue(2, $per_id);
                $sql_perfil->execute();
            }
        }

        if (is_array($asignado_car_ids)) {
            foreach ($asignado_car_ids as $car_id) {
                $sql_asignado = "INSERT INTO regla_asignados (regla_id, asignado_car_id) VALUES (?, ?)";
                $sql_asignado = $conectar->prepare($sql_asignado);
                $sql_asignado->bindValue(1, $regla_id);
                $sql_asignado->bindValue(2, $car_id);
                $sql_asignado->execute();
            }
        }
    }

    /**
     * Obtiene los datos de una regla de mapeo y las listas de sus cargos asociados.
     */
    public function get_regla_mapeo_por_id($regla_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $output = array();

        // 1. Obtener datos de la regla principal
        $sql_regla = "SELECT rm.*, s.cat_id
                  FROM tm_regla_mapeo rm
                  INNER JOIN tm_subcategoria s ON rm.cats_id = s.cats_id
                  WHERE rm.regla_id = ?";
        $sql_regla = $conectar->prepare($sql_regla);
        $sql_regla->bindValue(1, $regla_id);
        $sql_regla->execute();
        $output['regla'] = $sql_regla->fetch(PDO::FETCH_ASSOC);

        // 2. Obtener lista de IDs de cargos creadores asociados
        $sql_creadores = "SELECT creador_car_id FROM regla_creadores WHERE regla_id = ?";
        $sql_creadores = $conectar->prepare($sql_creadores);
        $sql_creadores->bindValue(1, $regla_id);
        $sql_creadores->execute();
        $output['creadores'] = array_column($sql_creadores->fetchAll(PDO::FETCH_ASSOC), 'creador_car_id');

        // 2.1 Obtener lista de IDs de perfiles creadores asociados
        $sql_perfiles = "SELECT creator_per_id FROM regla_creadores_perfil WHERE regla_id = ? AND est = 1";
        $sql_perfiles = $conectar->prepare($sql_perfiles);
        $sql_perfiles->bindValue(1, $regla_id);
        $sql_perfiles->execute();
        $output['creadores_perfiles'] = array_column($sql_perfiles->fetchAll(PDO::FETCH_ASSOC), 'creator_per_id');

        // 3. Obtener lista de IDs de cargos asignados asociados
        $sql_asignados = "SELECT asignado_car_id FROM regla_asignados WHERE regla_id = ?";
        $sql_asignados = $conectar->prepare($sql_asignados);
        $sql_asignados->bindValue(1, $regla_id);
        $sql_asignados->execute();
        $output['asignados'] = array_column($sql_asignados->fetchAll(PDO::FETCH_ASSOC), 'asignado_car_id');

        return $output;
    }

    /**
     * Obtiene todas las reglas de mapeo con listas de sus cargos asociados.
     * Usa GROUP_CONCAT para traer las listas en una sola consulta.
     */
    public function get_reglas_mapeo()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                    rm.regla_id,
                    s.cats_nom,
                    (SELECT GROUP_CONCAT(c.car_nom SEPARATOR ', ') FROM regla_creadores rc JOIN tm_cargo c ON rc.creador_car_id = c.car_id WHERE rc.regla_id = rm.regla_id) AS creadores_cargos,
                    (SELECT GROUP_CONCAT(p.per_nom SEPARATOR ', ') FROM regla_creadores_perfil rcp JOIN tm_perfil p ON rcp.creator_per_id = p.per_id WHERE rcp.regla_id = rm.regla_id AND rcp.est = 1) AS creadores_perfiles,
                    (SELECT GROUP_CONCAT(c.car_nom SEPARATOR ', ') FROM regla_asignados ra JOIN tm_cargo c ON ra.asignado_car_id = c.car_id WHERE ra.regla_id = rm.regla_id) AS asignados
                FROM 
                    tm_regla_mapeo rm
                LEFT JOIN 
                    tm_subcategoria s ON rm.cats_id = s.cats_id
                WHERE 
                    rm.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    /**
     * Elimina lógicamente una regla de mapeo.
     */
    public function delete_regla_mapeo($regla_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_regla_mapeo SET est = 0 WHERE regla_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $regla_id);
        $sql->execute();
    }

    public function get_regla_por_subcategoria($cats_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_regla_mapeo WHERE cats_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }
}
