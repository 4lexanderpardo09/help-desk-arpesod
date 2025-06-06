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
    }
?>