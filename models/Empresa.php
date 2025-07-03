<?php
    class Empresa extends Conectar {
        public function get_empresa(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_empresa WHERE est = 1";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_empresa_x_usu($usu_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT 
                e.emp_id, 
                e.emp_nom
            FROM 
                empresa_usuario AS eu
            INNER JOIN 
                td_empresa AS e ON eu.emp_id = e.emp_id
            WHERE 
                eu.usu_id = ? 
            AND 
                eu.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$usu_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_empresatodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_empresa
            WHERE est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_empresa($emp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO td_empresa (emp_id, emp_nom, est) VALUES (NULL,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_nom);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_empresa($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_empresa SET est = 0 WHERE emp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_empresa($emp_id,$emp_nom){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE td_empresa SET emp_nom = ? WHERE emp_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_nom);
            $sql->bindValue(2,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_empresa_x_id($emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM td_empresa WHERE emp_id = ? AND est = 1;";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_empresa_for_usu($usu_id, $emp_id){
        $conectar = parent::Conexion();
        parent::set_names();

        // Eliminar asociaciones anteriores
        $del = $conectar->prepare("DELETE FROM empresa_usuario WHERE usu_id = ?");
        $del->execute([$usu_id]);

        // Convertir emp_id en array si viene como string
        if (!is_array($emp_id)) {
            $emp_id = explode(',', $emp_id);
        }

        $sql = $conectar->prepare("INSERT INTO empresa_usuario (usu_id, emp_id, fech_crea, est) VALUES (?, ?, NOW(), 1)");

        foreach ($emp_id as $id) {
            $id = trim($id);
            if ($id === "") continue;

            $sql->bindValue(1, $usu_id, PDO::PARAM_INT);
            $sql->bindValue(2, $id, PDO::PARAM_INT);

            $sql->execute();
        
        }

        return true;
}

    }
?>