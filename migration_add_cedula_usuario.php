<?php
require_once("config/conexion.php");

class MigrationAddCedula extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        $sql = "SHOW COLUMNS FROM tm_usuario LIKE 'usu_cedula'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();

        if ($stmt->rowCount() == 0) {
            $sql = "ALTER TABLE tm_usuario ADD COLUMN usu_cedula VARCHAR(20) NULL AFTER usu_id";
            $conectar->query($sql);
            echo "Column 'usu_cedula' added successfully.\n";
        } else {
            echo "Column 'usu_cedula' already exists.\n";
        }
    }
}

$migration = new MigrationAddCedula();
$migration->run();
