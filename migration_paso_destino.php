<?php
require_once("config/conexion.php");
$conectar = Conectar::getConexion();

echo "Iniciando migración...\n";

// 1. Agregar columna paso_destino_id
try {
    $sql = "ALTER TABLE tm_flujo_transiciones ADD COLUMN paso_destino_id INT NULL AFTER ruta_id";
    $conectar->query($sql);
    echo "Columna paso_destino_id agregada.\n";
} catch (Exception $e) {
    echo "Error al agregar columna (puede que ya exista): " . $e->getMessage() . "\n";
}

// 2. Modificar ruta_id para permitir NULL
try {
    $sql = "ALTER TABLE tm_flujo_transiciones MODIFY COLUMN ruta_id INT NULL";
    $conectar->query($sql);
    echo "Columna ruta_id modificada para permitir NULL.\n";
} catch (Exception $e) {
    echo "Error al modificar columna ruta_id: " . $e->getMessage() . "\n";
}

echo "Migración completada.\n";
