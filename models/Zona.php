<?php
class Zona extends Conectar
{
    public function get_zonas()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_zona WHERE est = 1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function insert_zona($zona_nom)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_zona (zona_nom, est) VALUES (?, '1');";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $zona_nom);
        $sql->execute();
        return $conectar->lastInsertId();
    }

    public function update_zona($zona_id, $zona_nom)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_zona SET zona_nom = ? WHERE zona_id = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $zona_nom);
        $sql->bindValue(2, $zona_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete_zona($zona_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_zona SET est = '0' WHERE zona_id = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $zona_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_zona_x_id($zona_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_zona WHERE zona_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $zona_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
