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
            $sql = "SELECT tm_flujo.*,
                        cats_nom
            FROM tm_flujo 
            INNER JOIN tm_subcategoria ON tm_flujo.cats_id = tm_subcategoria.cats_id
            WHERE tm_flujo.est = 1";
            $sql = $conectar->prepare($sql);
            $sql->execute();

            return $resultado = $sql->fetchAll();
        }

        public function insert_flujo($flujo_nom, $cats_id, $req_aprob_jefe) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "INSERT INTO tm_flujo (flujo_nom, cats_id, requiere_aprobacion_jefe, est) VALUES (?, ?, ?, '1')";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_nom);
            $sql->bindValue(2, $cats_id);
            $sql->bindValue(3, $req_aprob_jefe);
            $sql->execute();
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

        public function update_flujo($flujo_id, $flujo_nom, $cats_id, $req_aprob_jefe) {
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "UPDATE tm_flujo SET
                        flujo_nom = ?,
                        cats_id = ?,
                        requiere_aprobacion_jefe = ?
                    WHERE
                        flujo_id = ?";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_nom);
            $sql->bindValue(2, $cats_id);
            $sql->bindValue(3, $req_aprob_jefe);
            $sql->bindValue(4, $flujo_id);
            $sql->execute();
        }

        public function get_flujo_x_id($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();

            $output = array();

            // 1. Obtener los datos del flujo y el ID de la categoría padre
            $sql_flujo = "SELECT f.*, s.cat_id 
                        FROM tm_flujo f
                        INNER JOIN tm_subcategoria s ON f.cats_id = s.cats_id
                        WHERE f.flujo_id = ?";
            $sql_flujo = $conectar->prepare($sql_flujo);
            $sql_flujo->bindValue(1, $flujo_id);
            $sql_flujo->execute();
            $flujo_data = $sql_flujo->fetch(PDO::FETCH_ASSOC);

            // Si encontramos el flujo, buscamos sus relaciones
            if ($flujo_data) {
                $output['flujo'] = $flujo_data;
                $cat_id = $flujo_data['cat_id']; // ID de la categoría padre

                // 2. Obtener la lista de IDs de empresas asociadas a esa categoría
                $sql_emp = "SELECT emp_id FROM categoria_empresa WHERE cat_id = ?";
                $sql_emp = $conectar->prepare($sql_emp);
                $sql_emp->bindValue(1, $cat_id);
                $sql_emp->execute();
                $output['empresas'] = array_column($sql_emp->fetchAll(PDO::FETCH_ASSOC), 'emp_id');

                // 3. Obtener la lista de IDs de departamentos asociados a esa categoría
                $sql_dp = "SELECT dp_id FROM categoria_departamento WHERE cat_id = ?";
                $sql_dp = $conectar->prepare($sql_dp);
                $sql_dp->bindValue(1, $cat_id);
                $sql_dp->execute();
                $output['departamentos'] = array_column($sql_dp->fetchAll(PDO::FETCH_ASSOC), 'dp_id');
            }

            return $output;
        }

        public function get_paso_inicial_por_flujo($flujo_id){
            $conectar = parent::Conexion();
            parent::set_names();
            // Busca el paso con el orden más bajo (generalmente 1)
            $sql = "SELECT * FROM tm_flujo_paso 
                    WHERE flujo_id = ? AND est = 1 
                    ORDER BY paso_orden ASC 
                    LIMIT 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $flujo_id);
            $sql->execute();
            return $resultado = $sql->fetch(PDO::FETCH_ASSOC); // Usamos fetch para un solo resultado
        }

        /**
         * LÓGICA CLAVE 2: Obtener el siguiente paso en un flujo.
         * Esto se usa para mostrarle al agente cuál es la siguiente fase.
         */
        public function get_siguiente_paso($paso_actual_id){
            $conectar = parent::Conexion();
            parent::set_names();
            // La consulta busca el paso cuyo orden es +1 al del paso actual.
            $sql = "SELECT * FROM tm_flujo_paso 
                    WHERE 
                        flujo_id = (SELECT flujo_id FROM tm_flujo_paso WHERE paso_id = ?) 
                        AND 
                        paso_orden = (SELECT paso_orden FROM tm_flujo_paso WHERE paso_id = ?) + 1
                        AND est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $paso_actual_id);
            $sql->bindValue(2, $paso_actual_id);
            $sql->execute();
            return $resultado = $sql->fetch(PDO::FETCH_ASSOC); // Usamos fetch para un solo resultado
        }
        
        /**
         * Obtiene el flujo asociado a una subcategoría.
         * Se usa al crear un ticket para ver si debe iniciar un flujo.
         */
        public function get_flujo_por_subcategoria($cats_id){
            $conectar = parent::Conexion();
            parent::set_names();
            $sql = "SELECT * FROM tm_flujo WHERE cats_id = ? AND est = 1";
            $sql = $conectar->prepare($sql);
            $sql->bindValue(1, $cats_id);
            $sql->execute();
            return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        }
    }
?>