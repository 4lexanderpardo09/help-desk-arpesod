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

        public function get_flujopaso_x_usu($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT tm_flujo_paso.*,
                           tm_flujo.flujo_id, 
                           tm_subcategoria.cats_id
            FROM tm_flujo_paso 
            INNER JOIN tm_flujo ON tm_flujo_paso.flujo_id = tm_flujo.flujo_id
            INNER JOIN tm_subcategoria ON tm_flujo.cats_id = tm_subcategoria.cats_id
            WHERE tm_flujo_paso.flujo_id = ? AND tm_flujo_paso.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujotodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_flujo
            WHERE est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_flujo($emp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO td_flujo (emp_id, emp_nom, est) VALUES (NULL,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_nom);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_flujo($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_flujo SET est = 0 WHERE emp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_flujo($emp_id,$emp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_flujo SET emp_nom = ? WHERE emp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_nom);
            $sql->bindValue(2,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujo_x_id($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_flujo WHERE emp_id = ? AND est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }


    }
?>