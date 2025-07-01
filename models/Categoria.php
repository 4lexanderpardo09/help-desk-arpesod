<?php
    class Categoria extends Conectar {
        public function get_categoria($dp_id,$emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_categoria WHERE dp_id = ? AND emp_id = ? and est = 1;";

            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_id);
            $sql->bindValue(2,$emp_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_categoriatodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT 
            tm_categoria.cat_id,
            tm_categoria.dp_id,
            tm_categoria.cat_nom,
            tm_departamento.dp_nom, 
            td_empresa.emp_nom
            FROM tm_categoria
            INNER JOIN tm_departamento ON tm_departamento.dp_id = tm_categoria.dp_id
            INNER JOIN td_empresa ON td_empresa.emp_id = tm_categoria.emp_id 
            WHERE tm_categoria.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_categoria($cat_nom,$dp_id,$emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_categoria (cat_id, dp_id, emp_id, cat_nom, est) VALUES (NULL,?,?,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$dp_id);
            $sql->bindValue(2, $emp_id);
            $sql->bindValue(3,$cat_nom);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_categoria($cat_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_categoria SET est = 0 WHERE cat_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cat_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_categoria($cat_id,$cat_nom,$dp_id,$emp_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_categoria SET cat_nom = ?, dp_id = ?, emp_id = ? WHERE cat_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cat_nom);
            $sql->bindValue(2,$dp_id);
            $sql->bindValue(3,$emp_id);
            $sql->bindValue(4,$cat_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_categoria_x_id($cat_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_categoria 
            INNER JOIN td_empresa as e ON e.emp_id = tm_categoria.emp_id 
            INNER JOIN tm_departamento as d ON d.dp_id = tm_categoria.dp_id
            WHERE cat_id = ? AND tm_categoria.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cat_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }
    }
?>