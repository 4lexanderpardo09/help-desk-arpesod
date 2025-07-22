<?php
class Regional extends Conectar
{
    public function insert_regional($reg_nom)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "INSERT INTO tm_regional (reg_nom, est) VALUES (?, '1');";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $reg_nom);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function update_regional($reg_id, $reg_nom)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_regional SET reg_nom = ? WHERE reg_id = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $reg_nom);
        $sql->bindValue(2, $reg_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function delete_regional($reg_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "UPDATE tm_regional SET est = '0' WHERE reg_id = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $reg_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_regionales()
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_regional WHERE est = '1';";
        $sql = $conectar->prepare($sql);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }

    public function get_regional_x_id($reg_id)
    {
        $conectar = parent::Conexion();
        parent::set_names();
        $sql = "SELECT * FROM tm_regional WHERE reg_id = ?;";
        $sql = $conectar->prepare($sql);
        $sql->bindValue(1, $reg_id);
        $sql->execute();
        return $resultado = $sql->fetchAll();
    }
}
?>