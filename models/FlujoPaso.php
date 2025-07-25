<?php 
    class FlujoPaso extends Conectar {

        
        public function get_flujopaso(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_flujo_paso WHERE est = 1";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_pasos_por_flujo($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT tm_flujo_paso.*,
            tm_cargo.car_nom
            FROM tm_flujo_paso 
            INNER JOIN tm_cargo ON tm_flujo_paso.cargo_id_asignado = tm_cargo.car_id   
            WHERE flujo_id = ? AND tm_flujo_paso.est = 1 ORDER BY paso_orden ASC";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }


        public function insert_paso($flujo_id, $paso_orden, $paso_nombre, $cargo_id_asignado, $paso_tiempo_habil, $paso_descripcion){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_flujo_paso (paso_id, flujo_id, paso_orden, paso_nombre, cargo_id_asignado,  paso_tiempo_habil, paso_descripcion, est) VALUES (NULL, ?, ?, ?, ?, ?, ?, 1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->bindValue(2, $paso_orden);
            $sql->bindValue(3, $paso_nombre);
            $sql->bindValue(4, $cargo_id_asignado);
            $sql->bindValue(5, $paso_tiempo_habil);
            $sql->bindValue(6, $paso_descripcion);
            $sql->execute();
            return $conectar->lastInsertId();
        }

        public function delete_paso($paso_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo_paso SET est = 0 WHERE paso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$paso_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_paso($paso_id,$flujo_id, $paso_orden, $paso_nombre, $cargo_id_asignado, $paso_tiempo_habil, $paso_descripcion){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo_paso SET flujo_id = ?, paso_orden = ?, paso_nombre = ?, cargo_id_asignado = ?, paso_tiempo_habil = ?, paso_descripcion = ?  WHERE paso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->bindValue(2, $paso_orden);
            $sql->bindValue(3, $paso_nombre);
            $sql->bindValue(4, $cargo_id_asignado);    
            $sql->bindValue(5, $paso_id);
            $sql->execute();
            return $conectar->lastInsertId();
        }

        public function get_paso_x_id($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT tm_flujo_paso.*,
            tm_usuario.car_id
            FROM tm_flujo_paso 
            INNER JOIN tm_usuario ON tm_flujo_paso.cargo_id_asignado = tm_usuario.car_id 
            WHERE paso_id = ? AND tm_flujo_paso.est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujo_id_from_paso($paso_id) {
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
        // Usamos fetch() porque solo esperamos un resultado
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_siguiente_paso($paso_actual_id)
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
        // Usamos fetch() para obtener solo una fila (o false si no hay siguiente paso)
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_paso_actual($paso_actual_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_flujo_paso 
                WHERE 
                    flujo_id = (SELECT flujo_id FROM tm_flujo_paso WHERE paso_id = ?) 
                    AND 
                    paso_orden = (SELECT paso_orden FROM tm_flujo_paso WHERE paso_id = ?)
                    AND est = 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $paso_actual_id);
        $sql->bindValue(2, $paso_actual_id);
        $sql->execute();
        // Usamos fetch() para obtener solo una fila (o false si no hay siguiente paso)
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_regla_aprobacion($creador_cargo_id_asignado) {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_regla_aprobacion WHERE aprobador_usu_id = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $creador_cargo_id_asignado);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function get_regla_mapeo($cats_id, $creador_cargo_id_asignado) {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_flujo_mapeo WHERE cats_id = ? AND creador_cargo_id_asignado = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->bindValue(2, $creador_cargo_id_asignado);
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }


    }
?>