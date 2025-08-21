<?php
class Organigrama extends Conectar {

    public function get_organigrama_data() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    sub.car_nom as from_node, 
                    jefe.car_nom as to_node
                FROM 
                    tm_organigrama org
                JOIN 
                    tm_cargo sub ON org.car_id = sub.car_id
                JOIN 
                    tm_cargo jefe ON org.jefe_car_id = jefe.car_id
                WHERE 
                    org.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_organigrama_list() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    org.org_id,
                    sub.car_nom as cargo_subordinado,
                    jefe.car_nom as cargo_jefe
                FROM 
                    tm_organigrama org
                JOIN 
                    tm_cargo sub ON org.car_id = sub.car_id
                JOIN 
                    tm_cargo jefe ON org.jefe_car_id = jefe.car_id
                WHERE 
                    org.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_jerarquia($car_id, $jefe_car_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_organigrama (car_id, jefe_car_id, est) VALUES (?, ?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->bindValue(2, $jefe_car_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_jerarquia($org_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_organigrama SET est = 0 WHERE org_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $org_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_jefe_cargo_id($car_id){
        $conectar= parent::conexion();
        parent::set_names();
        $sql="SELECT jefe_car_id FROM tm_organigrama WHERE car_id = ? AND est = 1";
        $sql=$conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['jefe_car_id'] : null;
    }
}
?>