<?php
require_once("config/conexion.php");
$conectar = new Conectar();
$conexion = $conectar->Conexion();

$sql = "ALTER TABLE tm_ticket_paralelo ADD COLUMN estado_tiempo_paso VARCHAR(50) NULL DEFAULT NULL AFTER estado";
$stmt = $conexion->prepare($sql);
try {
    $stmt->execute();
    echo "Columna estado_tiempo_paso agregada correctamente a tm_ticket_paralelo.";
} catch (PDOException $e) {
    echo "Error o la columna ya existe: " . $e->getMessage();
}
