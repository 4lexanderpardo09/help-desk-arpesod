<?php
class Categoria extends Conectar
{
    /**
     * Inserta una nueva categoría y sus relaciones con empresas y departamentos.
     * Acepta arrays de IDs para las relaciones.
     */
    public function insert_categoria($cat_nom, $emp_ids, $dp_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Insertar la categoría principal
        $sql = "INSERT INTO tm_categoria (cat_nom, est) VALUES (?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_nom);
        $sql->execute();

        // 2. Obtener el ID de la categoría recién creada
        $cat_id = $conectar->lastInsertId();

        // 3. Insertar las relaciones con las empresas
        if (is_array($emp_ids)) {
            foreach ($emp_ids as $emp_id) {
                $sql_emp = "INSERT INTO categoria_empresa (cat_id, emp_id) VALUES (?, ?)";
                $sql_emp = $conectar->prepare($sql_emp);
                $sql_emp->bindValue(1, $cat_id);
                $sql_emp->bindValue(2, $emp_id);
                $sql_emp->execute();
            }
        }

        // 4. Insertar las relaciones con los departamentos
        if (is_array($dp_ids)) {
            foreach ($dp_ids as $dp_id) {
                $sql_dp = "INSERT INTO categoria_departamento (cat_id, dp_id) VALUES (?, ?)";
                $sql_dp = $conectar->prepare($sql_dp);
                $sql_dp->bindValue(1, $cat_id);
                $sql_dp->bindValue(2, $dp_id);
                $sql_dp->execute();
            }
        }
    }

    /**
     * Actualiza una categoría y sus relaciones.
     * El método es "borrar y volver a insertar" las relaciones, que es lo más seguro.
     */
    public function update_categoria($cat_id, $cat_nom, $emp_ids, $dp_ids)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Actualizar el nombre de la categoría
        $sql = "UPDATE tm_categoria SET cat_nom = ? WHERE cat_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_nom);
        $sql->bindValue(2, $cat_id);
        $sql->execute();

        // 2. Borrar todas las relaciones antiguas para esta categoría
        $sql_del_emp = "DELETE FROM categoria_empresa WHERE cat_id = ?";
        $sql_del_emp = $conectar->prepare($sql_del_emp);
        $sql_del_emp->bindValue(1, $cat_id);
        $sql_del_emp->execute();

        $sql_del_dp = "DELETE FROM categoria_departamento WHERE cat_id = ?";
        $sql_del_dp = $conectar->prepare($sql_del_dp);
        $sql_del_dp->bindValue(1, $cat_id);
        $sql_del_dp->execute();

        // 3. Re-insertar las nuevas relaciones (igual que en la función de inserción)
        if (is_array($emp_ids)) {
            foreach ($emp_ids as $emp_id) {
                $sql_emp = "INSERT INTO categoria_empresa (cat_id, emp_id) VALUES (?, ?)";
                $sql_emp = $conectar->prepare($sql_emp);
                $sql_emp->bindValue(1, $cat_id);
                $sql_emp->bindValue(2, $emp_id);
                $sql_emp->execute();
            }
        }

        if (is_array($dp_ids)) {
            foreach ($dp_ids as $dp_id) {
                $sql_dp = "INSERT INTO categoria_departamento (cat_id, dp_id) VALUES (?, ?)";
                $sql_dp = $conectar->prepare($sql_dp);
                $sql_dp->bindValue(1, $cat_id);
                $sql_dp->bindValue(2, $dp_id);
                $sql_dp->execute();
            }
        }
    }

    /**
     * Obtiene los datos de una categoría y listas de sus empresas y departamentos asociados.
     */
    public function get_categoria_x_id($cat_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $output = array();

        // 1. Obtener datos básicos de la categoría
        $sql_cat = "SELECT * FROM tm_categoria WHERE cat_id = ?";
        $sql_cat = $conectar->prepare($sql_cat);
        $sql_cat->bindValue(1, $cat_id);
        $sql_cat->execute();
        $output['categoria'] = $sql_cat->fetch(PDO::FETCH_ASSOC);

        // 2. Obtener lista de IDs de empresas asociadas
        $sql_emp = "SELECT emp_id FROM categoria_empresa WHERE cat_id = ?";
        $sql_emp = $conectar->prepare($sql_emp);
        $sql_emp->bindValue(1, $cat_id);
        $sql_emp->execute();
        // Usamos fetchAll y array_column para obtener un array simple de IDs [1, 2, 5]
        $output['empresas'] = array_column($sql_emp->fetchAll(PDO::FETCH_ASSOC), 'emp_id');

        // 3. Obtener lista de IDs de departamentos asociados
        $sql_dp = "SELECT dp_id FROM categoria_departamento WHERE cat_id = ?";
        $sql_dp = $conectar->prepare($sql_dp);
        $sql_dp->bindValue(1, $cat_id);
        $sql_dp->execute();
        $output['departamentos'] = array_column($sql_dp->fetchAll(PDO::FETCH_ASSOC), 'dp_id');

        return $output;
    }

    /**
     * Obtiene todas las categorías y una lista de sus empresas y departamentos asociados.
     * Usa GROUP_CONCAT para traer las listas en una sola consulta.
     */
    public function get_categorias()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT 
                    cat.cat_id, 
                    cat.cat_nom,
                    GROUP_CONCAT(DISTINCT emp.emp_nom SEPARATOR ', ') AS empresas,
                    GROUP_CONCAT(DISTINCT dep.dp_nom SEPARATOR ', ') AS departamentos
                FROM 
                    tm_categoria cat
                LEFT JOIN categoria_empresa ce ON cat.cat_id = ce.cat_id
                LEFT JOIN td_empresa emp ON ce.emp_id = emp.emp_id
                LEFT JOIN categoria_departamento cd ON cat.cat_id = cd.cat_id
                LEFT JOIN tm_departamento dep ON cd.dp_id = dep.dp_id
                WHERE 
                    cat.est = 1
                GROUP BY 
                    cat.cat_id, cat.cat_nom";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    
    /**
     * Elimina lógicamente una categoría y sus relaciones.
     */
    public function delete_categoria($cat_id){
        $conectar = parent::Conexion();
        parent::set_names();
        
        // Eliminación lógica de la categoría
        $sql = "UPDATE tm_categoria SET est = 0 WHERE cat_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1,$cat_id);
        $sql->execute();

        // Borrado físico de las relaciones (recomendado para mantener limpia la BD)
        $sql_emp = "DELETE FROM categoria_empresa WHERE cat_id = ?";
        $sql_emp = $conectar->prepare($sql_emp);
        $sql_emp->bindValue(1, $cat_id);
        $sql_emp->execute();

        $sql_dp = "DELETE FROM categoria_departamento WHERE cat_id = ?";
        $sql_dp = $conectar->prepare($sql_dp);
        $sql_dp->bindValue(1, $cat_id);
        $sql_dp->execute();
    }
    
    /**
     * Nueva función para combos: busca categorías asociadas a UNA empresa Y UN departamento.
     */
    public function get_categoria_por_empresa_y_dpto($emp_id, $dp_id){
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT T1.cat_id, T1.cat_nom FROM tm_categoria AS T1 
                INNER JOIN categoria_empresa      AS T2 ON T1.cat_id = T2.cat_id
                INNER JOIN categoria_departamento AS T3 ON T1.cat_id = T3.cat_id
                WHERE T1.est = 1 AND T2.emp_id = ? AND T3.dp_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $emp_id);
        $sql->bindValue(2, $dp_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
    
    public function insert_categoria_simple($cat_nom) {
        $conectar = parent::Conexion();
        $sql = "INSERT INTO tm_categoria (cat_nom, est) VALUES (?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_nom);
        $sql->execute();
    }

    public function get_categoria_por_nombre($cat_nom) {
        $conectar = parent::Conexion();
        $sql = "SELECT * FROM tm_categoria WHERE cat_nom = ? AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, trim($cat_nom));
        $sql->execute();
        return $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function asociar_empresa($cat_id, $emp_id) {
        $conectar = parent::Conexion();
        // Consulta para evitar duplicados
        $sql = "INSERT INTO categoria_empresa (cat_id, emp_id)
                SELECT ?, ?
                WHERE NOT EXISTS (SELECT 1 FROM categoria_empresa WHERE cat_id = ? AND emp_id = ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_id);
        $sql->bindValue(2, $emp_id);
        $sql->bindValue(3, $cat_id);
        $sql->bindValue(4, $emp_id);
        $sql->execute();
    }

    public function asociar_departamento($cat_id, $dp_id) {
        $conectar = parent::Conexion();
        $sql = "INSERT INTO categoria_departamento (cat_id, dp_id)
                SELECT ?, ?
                WHERE NOT EXISTS (SELECT 1 FROM categoria_departamento WHERE cat_id = ? AND dp_id = ?)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cat_id);
        $sql->bindValue(2, $dp_id);
        $sql->bindValue(3, $cat_id);
        $sql->bindValue(4, $dp_id);
        $sql->execute();
    }
}
?>