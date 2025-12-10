<?php
require_once("config/conexion.php");

class Migration extends Conectar
{
    public function run()
    {
        $conectar = parent::Conexion();
        parent::set_names();

        // 1. Create tm_usuario_perfiles table
        $sql1 = "CREATE TABLE IF NOT EXISTS `tm_usuario_perfiles` (
            `usu_per_id` int(11) NOT NULL AUTO_INCREMENT,
            `usu_id` int(11) NOT NULL,
            `per_id` int(11) NOT NULL,
            `fech_crea` datetime DEFAULT CURRENT_TIMESTAMP,
            `est` int(11) DEFAULT '1',
            PRIMARY KEY (`usu_per_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $stmt1 = $conectar->prepare($sql1);
        $stmt1->execute();
        echo "Table tm_usuario_perfiles created or already exists.<br>";

        // 2. Create regra_creadores_perfil table
        $sql2 = "CREATE TABLE IF NOT EXISTS `regla_creadores_perfil` (
            `rcp_id` int(11) NOT NULL AUTO_INCREMENT,
            `regla_id` int(11) NOT NULL,
            `creator_per_id` int(11) NOT NULL,
            `fech_crea` datetime DEFAULT CURRENT_TIMESTAMP,
            `est` int(11) DEFAULT '1',
            PRIMARY KEY (`rcp_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $stmt2 = $conectar->prepare($sql2);
        $stmt2->execute();
        echo "Table regla_creadores_perfil created or already exists.<br>";

        // 3. Re-create tm_perfil table if it was deleted
        $sql3 = "CREATE TABLE IF NOT EXISTS `tm_perfil` (
            `per_id` int(11) NOT NULL AUTO_INCREMENT,
            `per_nom` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
            `fech_crea` datetime DEFAULT NULL,
            `fech_modi` datetime DEFAULT NULL,
            `fech_elim` datetime DEFAULT NULL,
            `est` int(11) NOT NULL,
            PRIMARY KEY (`per_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;";

        $stmt3 = $conectar->prepare($sql3);
        $stmt3->execute();
        echo "Table tm_perfil checked/created.<br>";
    }
}

$migration = new Migration();
$migration->run();
