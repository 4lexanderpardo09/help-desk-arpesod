<?php
    class Subcategoria extends Conectar {
        public function get_subcategoria($cat_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_subcategoria WHERE cat_id = ? and est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cat_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function get_subcategoriatodo(){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT 
            tm_subcategoria.cat_id,
            tm_subcategoria.cats_id,
            tm_subcategoria.cats_nom,
            tm_categoria.cat_nom,
            td_prioridad.pd_nom 
            FROM tm_subcategoria
            INNER JOIN tm_categoria ON tm_subcategoria.cat_id = tm_categoria.cat_id
            INNER JOIN td_prioridad ON tm_subcategoria.pd_id = td_prioridad.pd_id
            WHERE tm_subcategoria.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }


        public function insert_subcategoria($cat_id,$pd_id,$cats_nom,$cats_descrip){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_subcategoria (cats_id, cat_id, pd_id, cats_nom, cats_descrip, est) VALUES (NULL,?,?,?,?,1)";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cat_id);
            $sql->bindValue(2,$pd_id);
            $sql->bindValue(3,$cats_nom);
            $sql->bindValue(4,$cats_descrip);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function delete_subcategoria($cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_subcategoria SET est = 0 WHERE cats_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1,$cats_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function update_subcategoria($cats_id,$cat_id,$pd_id,$cats_nom,$cats_descrip){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "UPDATE tm_subcategoria SET cat_id = ?, pd_id = ?, cats_nom = ?, cats_descrip = ? WHERE cats_id = ?";
            $sql = $conectar->prepare($sql);    
            $sql->bindValue(1,$cat_id);
            $sql->bindValue(2,$pd_id);
            $sql->bindValue(3,$cats_nom);
            $sql->bindValue(4,$cats_descrip);
            $sql->bindValue(5,$cats_id);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

       public function get_subcategoria_x_id($cats_id) {
        $conectar = parent::Conexion();
        parent::set_names();

        $output = array();

        // 1. Obtener los datos básicos de la subcategoría
        $sql_subcat = "SELECT * FROM tm_subcategoria WHERE cats_id = ? AND est = 1";
        $sql_subcat = $conectar->prepare($sql_subcat);
        $sql_subcat->bindValue(1, $cats_id);
        $sql_subcat->execute();
        $subcategoria_data = $sql_subcat->fetch(PDO::FETCH_ASSOC);

        // Si encontramos la subcategoría, buscamos sus relaciones
        if ($subcategoria_data) {
            $output['subcategoria'] = $subcategoria_data;
            $cat_id = $subcategoria_data['cat_id']; // ID de la categoría padre

            // 2. Obtener la lista de IDs de empresas asociadas a la categoría padre
            $sql_emp = "SELECT emp_id FROM categoria_empresa WHERE cat_id = ?";
            $sql_emp = $conectar->prepare($sql_emp);
            $sql_emp->bindValue(1, $cat_id);
            $sql_emp->execute();
            // array_column crea un array simple con solo los IDs. Ej: [10, 11]
            $output['empresas'] = array_column($sql_emp->fetchAll(PDO::FETCH_ASSOC), 'emp_id');

            // 3. Obtener la lista de IDs de departamentos asociados a la categoría padre
            $sql_dp = "SELECT dp_id FROM categoria_departamento WHERE cat_id = ?";
            $sql_dp = $conectar->prepare($sql_dp);
            $sql_dp->bindValue(1, $cat_id);
            $sql_dp->execute();
            $output['departamentos'] = array_column($sql_dp->fetchAll(PDO::FETCH_ASSOC), 'dp_id');
        }

        return $output;
    }

        public function get_subcategoria_por_nombre($cats_nom) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_subcategoria WHERE cats_nom = ? AND est = 1 LIMIT 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, trim($cats_nom));
            $sql->execute();
            return $sql->fetch(PDO::FETCH_ASSOC);
        }

        
    }
    
?>