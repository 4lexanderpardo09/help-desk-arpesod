<?php
class Cargo extends Conectar {

    public function get_cargos() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_cargo WHERE est=1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_cargo_por_id($car_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_cargo WHERE car_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_cargo($car_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_cargo (car_nom, est) VALUES (?, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_nom);
        $sql->execute();
    }

    public function update_cargo($car_id, $car_nom) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_cargo SET car_nom = ? WHERE car_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_nom);
        $sql->bindValue(2, $car_id);
        $sql->execute();
    }

    public function delete_cargo($car_id) {
        $conectar = parent::conexion();
        parent::set_names();
        // Usamos eliminación lógica para mantener la integridad de los datos
        $sql = "UPDATE tm_cargo SET est = '0' WHERE car_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $car_id);
        $sql->execute();
    }

    public function get_id_por_nombre($car_nom) {
        $conectar = parent::conexion();
        $sql = "SELECT car_id FROM tm_cargo WHERE UPPER(car_nom) = UPPER(?) AND est = 1 LIMIT 1";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, trim($car_nom));
        $sql->execute();
        $resultado = $sql->fetch(PDO::FETCH_ASSOC);
        return $resultado ? $resultado['car_id'] : null;
    }
}
?>