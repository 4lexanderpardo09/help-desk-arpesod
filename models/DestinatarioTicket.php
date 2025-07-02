<?php
    class DestinatarioTicket extends Conectar {
        public function get_destinatarioticket($answer_id,$dp_id,$cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT d.*,
                            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario

                    FROM tm_destinatario as d 
                    INNER JOIN tm_usuario as u ON d.usu_id = u.usu_id
                    WHERE answer_id = ? AND d.dp_id = ? AND cats_id = ? AND d.est = 1";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_id);
            $sql->bindValue(2,$dp_id);
            $sql->bindValue(3,$cats_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_destinatariotickettodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT d.*,
                            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario,
                            af.answer_nom,
                            sc.cats_nom,
                            dp.dp_nom
                    FROM tm_destinatario as d 
                    INNER JOIN tm_usuario as u ON d.usu_id = u.usu_id
                    INNER JOIN tm_fast_answer as af ON d.answer_id = af.answer_id
                    INNER JOIN tm_subcategoria as sc ON d.cats_id = sc.cats_id
                    INNER JOIN tm_departamento as dp ON d.dp_id = dp.dp_id
                    WHERE d.est = 1";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_destinatarioticket($answer_id,$usu_id,$dp_id,$cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_destinatario (answer_id, usu_id, dp_id, cats_id, fech_crea, est) VALUES (?,?,?,?,NOW(),1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_id);
            $sql->bindValue(2,$usu_id);
            $sql->bindValue(3,$dp_id);
            $sql->bindValue(4,$cats_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_destinatarioticket($dest_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_destinatario SET est = 0, fech_elim = NOW() WHERE dest_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dest_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_destinatarioticket($dest_id,$answer_id,$usu_id,$dp_id,$cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_destinatario SET answer_id = ?, usu_id = ?, dp_id = ?, cats_id = ?, fech_modi = NOW() WHERE dest_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$answer_id);
            $sql->bindValue(2,$usu_id);
            $sql->bindValue(3,$dp_id);
            $sql->bindValue(4,$cats_id);
            $sql->bindValue(5,$dest_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_destinatarioticket_x_id($dest_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT d.*,
                            CONCAT(u.usu_nom, ' ', u.usu_ape) AS nombre_usuario,
                            af.answer_id,
                            sc.cats_id,
                            dp.dp_id,
                            c.cat_id
                    FROM tm_destinatario as d 
                    INNER JOIN tm_usuario as u ON d.usu_id = u.usu_id
                    INNER JOIN tm_fast_answer as af ON d.answer_id = af.answer_id
                    INNER JOIN tm_subcategoria as sc ON d.cats_id = sc.cats_id
                    INNER JOIN tm_departamento as dp ON d.dp_id = dp.dp_id
                    INNER JOIN tm_categoria as c ON sc.cat_id = c.cat_id
                    WHERE d.dest_id = ? AND d.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dest_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }
    }
?>