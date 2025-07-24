<?php
class ReglaAprobacion extends Conectar {

    public function get_reglas_aprobacion() {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT 
                    R.regla_id,
                    C.car_nom AS creador_cargo,
                    CONCAT(U.usu_nom, ' ', U.usu_ape) AS aprobador_nombre,
                    R.est
                FROM 
                    tm_regla_aprobacion AS R
                LEFT JOIN tm_cargo AS C ON R.creador_car_id = C.car_id
                LEFT JOIN tm_usuario AS U ON R.aprobador_usu_id = U.usu_id
                WHERE R.est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_regla_aprobacion_por_id($regla_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_regla_aprobacion WHERE regla_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $regla_id);
        $sql->execute();
        return $resultado = $sql->fetch(PDO::FETCH_ASSOC);
    }

    public function insert_regla_aprobacion($creador_car_id, $aprobador_usu_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_regla_aprobacion (creador_car_id, aprobador_usu_id, est) VALUES (?, ?, '1')";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $creador_car_id);
        $sql->bindValue(2, $aprobador_usu_id);
        $sql->execute();
    }

    public function update_regla_aprobacion($regla_id, $creador_car_id, $aprobador_usu_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_regla_aprobacion 
                SET 
                    creador_car_id = ?,
                    aprobador_usu_id = ?
                WHERE 
                    regla_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $creador_car_id);
        $sql->bindValue(2, $aprobador_usu_id);
        $sql->bindValue(3, $regla_id);
        $sql->execute();
    }

    public function delete_regla_aprobacion($regla_id) {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_regla_aprobacion SET est = '0' WHERE regla_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $regla_id);
        $sql->execute();
    }
}
?>