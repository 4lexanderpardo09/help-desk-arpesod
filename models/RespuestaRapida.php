<?php
    class RespuestaRapida extends Conectar {
        public function get_respuestarapida(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_fast_answer WHERE est = 1";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_respuestarapida($answer_nom, $es_error_proceso){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_fast_answer (answer_nom, es_error_proceso, fech_crea, est) VALUES (?,?,NOW(),1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_nom);
            $sql->bindValue(2,$es_error_proceso);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_respuestarapida($answer_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_fast_answer SET est = 0, fech_elim = NOW() WHERE answer_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_respuestarapida($answer_id,$answer_nom, $es_error_proceso){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_fast_answer SET answer_nom = ?, es_error_proceso = ?, fech_modi = NOW() WHERE answer_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_nom);
            $sql->bindValue(2,$es_error_proceso);
            $sql->bindValue(3,$answer_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_respuestarapida_x_id($answer_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_fast_answer WHERE answer_id = ? AND est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_id);
            $sql->execute();

            return $resultado = $sql->fetch();
        }
    }
?>