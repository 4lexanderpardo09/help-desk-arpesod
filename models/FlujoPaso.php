<?php
class FlujoPaso extends Conectar
{


    public function get_flujopaso()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_paso WHERE est = 1";

        $sql = $conectar->prepare($sql);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_pasos_por_flujo($flujo_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT tm_flujo_paso.*,
            tm_cargo.car_nom
            FROM tm_flujo_paso 
            LEFT JOIN tm_cargo ON tm_flujo_paso.cargo_id_asignado = tm_cargo.car_id   
            WHERE flujo_id = ? AND tm_flujo_paso.est = 1 ORDER BY paso_orden ASC";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $flujo_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }


    public function insert_paso($flujo_id, $paso_orden, $paso_nombre, $cargo_id_asignado, $paso_tiempo_habil, $paso_descripcion, $requiere_seleccion_manual, $es_tarea_nacional, $es_aprobacion, $paso_nom_adjunto, $permite_cerrar, $necesita_aprobacion_jefe)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_flujo_paso (flujo_id, paso_orden, paso_nombre, cargo_id_asignado, paso_tiempo_habil, paso_descripcion, requiere_seleccion_manual, es_tarea_nacional, es_aprobacion, paso_nom_adjunto, permite_cerrar, necesita_aprobacion_jefe, est) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $flujo_id);
        $sql->bindValue(2, $paso_orden);
        $sql->bindValue(3, $paso_nombre);
        $sql->bindValue(4, $cargo_id_asignado);
        $sql->bindValue(5, $paso_tiempo_habil);
        $sql->bindValue(6, $paso_descripcion);
        $sql->bindValue(7, $requiere_seleccion_manual);
        $sql->bindValue(8, $es_tarea_nacional);
        $sql->bindValue(9, $es_aprobacion);
        $sql->bindValue(10, $paso_nom_adjunto);
        $sql->bindValue(11, $permite_cerrar);
        $sql->bindValue(12, $necesita_aprobacion_jefe);
        $sql->execute();
        return $conectar->lastInsertId();
    }

    public function delete_paso($paso_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_flujo_paso SET est = 0 WHERE paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function update_paso($paso_id, $paso_orden, $paso_nombre, $cargo_id_asignado, $paso_tiempo_habil, $paso_descripcion, $requiere_seleccion_manual, $es_tarea_nacional, $es_aprobacion, $paso_nom_adjunto, $permite_cerrar, $necesita_aprobacion_jefe)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        // Se añade la nueva columna a la consulta
        $sql = "UPDATE tm_flujo_paso 
                    SET 
                        paso_orden = ?, 
                        paso_nombre = ?, 
                        cargo_id_asignado = ?, 
                        paso_tiempo_habil = ?, 
                        paso_descripcion = ?,
                        requiere_seleccion_manual = ?,
                        es_tarea_nacional = ?,
                        es_aprobacion = ?,
                        paso_nom_adjunto = ?,
                        permite_cerrar = ?,
                        necesita_aprobacion_jefe = ?
                    WHERE 
                        paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_orden);
        $sql->bindValue(2, $paso_nombre);
        $sql->bindValue(3, $cargo_id_asignado);
        $sql->bindValue(4, $paso_tiempo_habil);
        $sql->bindValue(5, $paso_descripcion);
        $sql->bindValue(6, $requiere_seleccion_manual);
        $sql->bindValue(7, $es_tarea_nacional);
        $sql->bindValue(8, $es_aprobacion);
        $sql->bindValue(9, $paso_nom_adjunto);
        $sql->bindValue(10, $permite_cerrar);
        $sql->bindValue(11, $necesita_aprobacion_jefe);
        $sql->bindValue(12, $paso_id);
        $sql->execute();
    }

    public function get_paso_x_id($emp_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT tm_flujo_paso.*,
            tm_usuario.car_id
            FROM tm_flujo_paso 
            INNER JOIN tm_usuario ON tm_flujo_paso.cargo_id_asignado = tm_usuario.car_id 
            WHERE paso_id = ? AND tm_flujo_paso.est = 1;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $emp_id);
        $sql->execute();

        return $resultado = $sql->fetchAll();
    }

    public function get_flujo_id_from_paso($paso_id)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT flujo_id FROM tm_flujo_paso WHERE paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['flujo_id'] : null;
    }

    public function get_paso_por_id($paso_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_paso WHERE paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $resultado['usuarios_especificos'] = $this->get_usuarios_especificos($paso_id);
        }

        return $resultado;
    }

    public function get_siguiente_paso_transicion($paso_actual_id, $condicion_clave, $condicion_nombre)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT paso_destino_id FROM tm_flujo_transiciones WHERE paso_origen_id = ? AND condicion_clave = ? AND condicion_nombre = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_actual_id);
        $sql->bindValue(2, $condicion_clave);
        $sql->bindValue(3, $condicion_nombre);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        if ($resultado && $resultado['paso_destino_id']) {
            // Devolvemos los detalles completos del paso destino
            return $this->get_paso_por_id($resultado['paso_destino_id']);
        }
        return null; // No se encontró transición o es el fin del flujo
    }

    public function get_transiciones_por_paso($paso_origen_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT condicion_clave, condicion_nombre FROM tm_flujo_transiciones WHERE paso_origen_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_origen_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_siguientes_pasos($paso_actual_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_paso 
                WHERE 
                    flujo_id = (SELECT flujo_id FROM tm_flujo_paso WHERE paso_id = ?) 
                    AND 
                    paso_orden = (SELECT paso_orden FROM tm_flujo_paso WHERE paso_id = ?) + 1
                    AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_actual_id);
        $sql->bindValue(2, $paso_actual_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_paso_actual($paso_actual_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT *, paso_nom_adjunto FROM tm_flujo_paso WHERE paso_id = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_actual_id);
        $sql->execute();
        // Usamos fetch() para obtener solo una fila (o false si no hay siguiente paso)
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_regla_aprobacion($creador_cargo_id_asignado)
    {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_regla_aprobacion WHERE aprobador_usu_id = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $creador_cargo_id_asignado);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_regla_mapeo($cats_id, $creador_car_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SELECT
                ra.asignado_car_id
            FROM
                tm_regla_mapeo rm
            INNER JOIN
                regla_creadores rc ON rm.regla_id = rc.regla_id
            INNER JOIN
                regla_asignados ra ON rm.regla_id = ra.regla_id
            WHERE
                rm.cats_id = ? AND rc.creador_car_id = ? AND rm.est = 1
            LIMIT 1"; // Obtenemos solo el primer resultado

        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->bindValue(2, $creador_car_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);

        // Devolvemos solo el ID, o null si no se encontró nada
        return $resultado ? $resultado['asignado_car_id'] : null;
    }

    public function verificar_orden_existente($flujo_id, $paso_orden)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT COUNT(*) as total FROM tm_flujo_paso WHERE flujo_id = ? AND paso_orden = ? AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $flujo_id);
        $sql->bindValue(2, $paso_orden);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }

    public function set_usuarios_especificos($paso_id, $user_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "DELETE FROM tm_flujo_paso_usuarios WHERE paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();

        if (is_array($user_ids)) {
            foreach ($user_ids as $user_id) {
                $sql = "INSERT INTO tm_flujo_paso_usuarios (paso_id, usu_id) VALUES (?, ?)";
                $sql = $conectar->prepare($sql);
                $sql->bindValue(1, $paso_id);
                $sql->bindValue(2, $user_id);
                $sql->execute();
            }
        }
    }

    public function get_usuarios_especificos($paso_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT usu_id FROM tm_flujo_paso_usuarios WHERE paso_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_id);
        $sql->execute();
        return $sql->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function get_paso_anterior($paso_actual_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // Primero, obtener el orden y el flujo_id del paso actual
        $sql_current = "SELECT flujo_id, paso_orden FROM tm_flujo_paso WHERE paso_id = ? AND est = 1";
        $stmt_current = $conectar->prepare($sql_current);
        $stmt_current->bindValue(1, $paso_actual_id);
        $stmt_current->execute();
        $paso_actual = $stmt_current->fetch(PDO::FETCH_ASSOC);

        if (!$paso_actual) {
            return null; // El paso actual no existe o está inactivo
        }

        $orden_actual = (int)$paso_actual['paso_orden'];
        $flujo_id = $paso_actual['flujo_id'];

        if ($orden_actual <= 1) {
            return null; // No hay paso anterior si es el primero o el orden es inválido
        }

        $orden_anterior = $orden_actual - 1;

        // Ahora, buscar el paso con el orden anterior en el mismo flujo
        $sql_prev = "SELECT * FROM tm_flujo_paso WHERE flujo_id = ? AND paso_orden = ? AND est = 1";
        $stmt_prev = $conectar->prepare($sql_prev);
        $stmt_prev->bindValue(1, $flujo_id);
        $stmt_prev->bindValue(2, $orden_anterior);
        $stmt_prev->execute();

        return $stmt_prev->fetch(PDO::FETCH_ASSOC);
    }
}
