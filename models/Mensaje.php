<?php
    class Mensaje extends Conectar{
        public function get_conversaciones($usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT 
                c.conv_id,
                c.de_usu_id,
                c.para_usu_id,
                u.usu_nom AS de_usu_nom,
                u.usu_ape AS de_usu_ape,
                u2.usu_nom AS para_usu_nom,
                u2.usu_ape AS para_usu_ape
                FROM tm_conversacion c
                JOIN tm_usuario u ON c.de_usu_id = u.usu_id
                JOIN tm_usuario u2 ON c.para_usu_id = u2.usu_id
                WHERE c.de_usu_id = ? OR c.para_usu_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->bindValue(2, $usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_mensajes($conv_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT * FROM td_mensaje WHERE conv_id = ? ORDER BY fech_crea ASC";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $conv_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function insert_mensaje($conv_id, $de_usu_id, $mensaje){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="INSERT INTO td_mensaje (men_id, conv_id, de_usu_id, mensaje, fech_crea, est) VALUES (NULL, ?, ?, ?, now(), '1');";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $conv_id);
            $sql->bindValue(2, $de_usu_id);
            $sql->bindValue(3, $mensaje);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function create_conversacion($de_usu_id, $para_usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="INSERT INTO tm_conversacion (conv_id, de_usu_id, para_usu_id, fech_crea, est) VALUES (NULL, ?, ?, now(), '1');";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $de_usu_id);
            $sql->bindValue(2, $para_usu_id);
            $sql->execute();
            return $conectar->lastInsertId();
        }

        public function get_conversacion_por_usuarios($de_usu_id, $para_usu_id) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_conversacion WHERE (de_usu_id = ? AND para_usu_id = ?) OR (de_usu_id = ? AND para_usu_id = ?)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $de_usu_id);
            $sql->bindValue(2, $para_usu_id);
            $sql->bindValue(3, $para_usu_id);
            $sql->bindValue(4, $de_usu_id);
            $sql->execute();
            return $sql->fetch();
        }

        public function get_usuario_nombre($usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql="SELECT usu_nom, usu_ape FROM tm_usuario WHERE usu_id = ?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->execute();
            $resultado = $sql->fetch();
            return $resultado["usu_nom"] . " " . $resultado["usu_ape"];
        }

        public function get_total_mensajes_no_leidos($usu_id){
            $conectar= parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) as total FROM td_mensaje m
                    JOIN tm_conversacion c ON m.conv_id = c.conv_id
                    WHERE c.para_usu_id = ? AND m.est = 1";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);
            $sql->execute();
            $resultado = $sql->fetch();
            return $resultado['total'];
        }
    }
?>