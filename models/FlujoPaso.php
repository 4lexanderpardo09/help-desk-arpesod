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
            tm_usuario.usu_nom,
            tm_usuario.usu_ape
            FROM tm_flujo_paso 
            INNER JOIN tm_usuario ON tm_flujo_paso.usu_asig = tm_usuario.usu_id   
            WHERE flujo_id = ? AND tm_flujo_paso.est = 1 ORDER BY paso_orden ASC";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }


        public function insert_paso($flujo_id, $paso_orden, $paso_nombre, $usu_asig){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_flujo_paso (paso_id, flujo_id, paso_orden, paso_nombre, usu_asig, est) VALUES (NULL, ?, ?, ?, ?, 1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->bindValue(2, $paso_orden);
            $sql->bindValue(3, $paso_nombre);
            $sql->bindValue(4, $usu_asig);
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

        public function update_paso($paso_id,$flujo_id, $paso_orden, $paso_nombre, $usu_asig){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo_paso SET flujo_id = ?, paso_orden = ?, paso_nombre = ?, usu_asig = ? WHERE paso_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->bindValue(2, $paso_orden);
            $sql->bindValue(3, $paso_nombre);
            $sql->bindValue(4, $usu_asig);    
            $sql->bindValue(5, $paso_id);
            $sql->execute();
            return $conectar->lastInsertId();
        }

        public function get_paso_x_id($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT tm_flujo_paso.*,
            tm_usuario.usu_id
            FROM tm_flujo_paso 
            INNER JOIN tm_usuario ON tm_flujo_paso.usu_asig = tm_usuario.usu_id 
            WHERE paso_id = ? AND tm_flujo_paso.est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }


    }
?>