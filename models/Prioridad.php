<?php
    class Prioridad extends Conectar {
        public function get_prioridad(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_prioridad WHERE est = 1;";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }



        public function insert_prioridad($pd_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO td_prioridad (pd_id, pd_nom, est) VALUES (NULL,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pd_nom);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_prioridad($pd_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_prioridad SET est = 0 WHERE pd_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pd_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_prioridad($pd_id,$pd_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_prioridad SET pd_nom = ? WHERE pd_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pd_nom);
            $sql->bindValue(2,$pd_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_prioridad_x_id($pd_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_prioridad WHERE pd_id = ? AND est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$pd_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }
    }
?>