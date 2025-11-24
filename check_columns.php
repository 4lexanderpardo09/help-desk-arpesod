<?php
require_once("config/conexion.php");
$conectar = Conectar::getConexion();
$sql = "DESCRIBE tm_flujo_transiciones";
$stmt = $conectar->prepare($sql);
$stmt->execute();
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($columns);
