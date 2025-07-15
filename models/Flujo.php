<?php 
    class Flujo extends Conectar {

        public function get_flujo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_flujo WHERE est = 1";

            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujo_x_usu($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_flujo WHERE flujo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujotodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT *,
                        cats_nom
            FROM tm_flujo 
            INNER JOIN tm_subcategoria ON tm_flujo.cats_id = tm_subcategoria.cats_id
            WHERE tm_flujo.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_flujo($flujo_nom,$cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_flujo (flujo_nom, cats_id, est) VALUES (?,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_nom);
            $sql->bindValue(2,$cats_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_flujo($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo SET est = 0 WHERE flujo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_flujo($flujo_id,$flujo_nom,$cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo SET flujo_nom = ?, cats_id = ? WHERE flujo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_nom);
            $sql->bindValue(2,$cats_id);
            $sql->bindValue(3,$flujo_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_flujo_x_id($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT tm_flujo.*,
                    tm_subcategoria.cats_id,
                    tm_categoria.cat_id,
                    tm_categoria.emp_id,
                    tm_categoria.dp_id
            FROM tm_flujo 
            INNER JOIN tm_subcategoria ON tm_flujo.cats_id = tm_subcategoria.cats_id
            INNER JOIN tm_categoria ON tm_subcategoria.cat_id = tm_categoria.cat_id
            WHERE flujo_id = ? and  tm_subcategoria.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$flujo_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }


    }
?>