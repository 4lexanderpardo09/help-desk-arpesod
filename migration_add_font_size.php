<?php
require_once("config/conexion.php");

// Use the static method to get the connection
// Note: getConexion() instantiates Conectar internally and calls protected Conexion()
// However, since we are outside, we might still have issues if getConexion calls a protected method on $instance?
// No, $instance->Conexion() inside the class is fine.
// But wait, getConexion() is public static.
// Let's try extending just to be safe and standard.

class Migration extends Conectar
{
    public function run()
    {
        $conexion = parent::Conexion();
        $sql = "ALTER TABLE tm_campo_plantilla ADD COLUMN font_size INT DEFAULT 10";
        try {
            $conexion->query($sql);
            echo "Columna font_size agregada correctamente a tm_campo_plantilla.\n";
        } catch (PDOException $e) {
            // Check if error is "Duplicate column name"
            if ($e->getCode() == '42S21') {
                echo "La columna font_size ya existe.\n";
            } else {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
}

$migration = new Migration();
$migration->run();
