<?php
    class Notificacion extends Conectar {
        public function get_notificacion_x_usu($usu_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_notificacion WHERE usu_id = ? AND est = 2 LIMIT 1";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);    
            $sql->execute();
            

            return $resultado = $sql->fetchAll();
        }

        public function get_notificacion_x_usu_todas($usu_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_notificacion WHERE usu_id = ? AND est = 1 ORDER BY fech_not DESC";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);    
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_notificacion_estado($not_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_notificacion SET est = 1 WHERE not_id = ?";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $not_id);    
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_notificacion_estado_leido($not_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_notificacion SET est = 0 WHERE not_id = ?";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $not_id);    
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function contar_notificaciones_x_usu($usu_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) as totalnotificaciones FROM tm_notificacion WHERE usu_id = ? and est = 1";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $usu_id);    
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_nuevas_notificaciones_para_enviar(){
            $conectar = parent::Conexion();
            parent::set_names();
            // Buscamos notificaciones con estado 2 (nuevas, no enviadas)
            $sql = "SELECT * FROM tm_notificacion WHERE est = 2";
            $sql = $conectar->prepare($sql);
            $sql->execute();
            return $resultado = $sql->fetchAll();
        }

    }
?>