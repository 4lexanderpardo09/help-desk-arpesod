<?php
class Perfil extends Conectar
{
    public function get_perfiles()
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_perfil WHERE est=1";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert_perfil($per_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_perfil (per_nom, est) VALUES (?, 1)";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $per_nom);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_perfil($per_id, $per_nom)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_perfil SET per_nom = ? WHERE per_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $per_nom);
        $sql->bindValue(2, $per_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete_perfil($per_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "UPDATE tm_perfil SET est = 0 WHERE per_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $per_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get_perfil_x_id($per_id)
    {
        $conectar = parent::conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_perfil WHERE per_id = ?";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $per_id);
        $sql->execute();
        return $resultado = $sql->fetchAll(PDO::FETCH_ASSOC);
    }
}
