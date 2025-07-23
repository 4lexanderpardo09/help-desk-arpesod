<?php
class FlujoMapeo extends Conectar {

    public function get_flujo_mapeos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    M.map_id,
                    S.cats_nom,
                    C1.car_nom AS creador_cargo,
                    C2.car_nom AS asignado_cargo,
                    M.est
                FROM 
                    tm_flujo_mapeo AS M
                LEFT JOIN tm_subcategoria AS S ON M.cats_id = S.cats_id
                LEFT JOIN tm_cargo AS C1 ON M.creador_car_id = C1.car_id
                LEFT JOIN tm_cargo AS C2 ON M.asignado_car_id = C2.car_id
                WHERE M.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_flujo_mapeo_por_id($map_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT fm.*,
                tm_categoria.cat_id,    
                tm_categoria.emp_id,
                tm_categoria.dp_id,
                tm_subcategoria.cats_id
        FROM tm_flujo_mapeo AS fm 
        INNER JOIN tm_subcategoria ON fm.cats_id = tm_subcategoria.cats_id
        INNER JOIN tm_categoria ON tm_subcategoria.cat_id = tm_categoria.cat_id
        WHERE map_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $map_id);
        $sql->execute();
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_flujo_mapeo($cats_id, $creador_car_id, $asignado_car_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_flujo_mapeo (cats_id, creador_car_id, asignado_car_id, est) VALUES (?, ?, ?, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->bindValue(2, $creador_car_id);
        $sql->bindValue(3, $asignado_car_id);
        $sql->execute();
    }

    public function update_flujo_mapeo($map_id, $cats_id, $creador_car_id, $asignado_car_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_flujo_mapeo 
                SET 
                    cats_id = ?,
                    creador_car_id = ?,
                    asignado_car_id = ?
                WHERE 
                    map_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $cats_id);
        $sql->bindValue(2, $creador_car_id);
        $sql->bindValue(3, $asignado_car_id);
        $sql->bindValue(4, $map_id);
        $sql->execute();
    }

    public function delete_flujo_mapeo($map_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_flujo_mapeo SET est = '0' WHERE map_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $map_id);
        $sql->execute();
    }
}
?>