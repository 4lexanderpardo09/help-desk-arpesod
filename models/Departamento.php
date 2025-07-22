<?php
    class Departamento extends Conectar {
        public function get_departamento(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_departamento WHERE est = 1;";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }
        public function insert_departamento($dp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_departamento (dp_id, dp_nom, est) VALUES (NULL,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_nom);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_departamento($dp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_departamento SET est = 0 WHERE dp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_departamento($dp_id,$dp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_departamento SET dp_nom = ? WHERE dp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_nom);
            $sql->bindValue(2,$dp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_departamento_x_id($dp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_departamento WHERE dp_id = ? AND est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_id);
            $sql->execute();

            return $sql->fetch(PDO::FETCH_ASSOC);
        }
    }
?>