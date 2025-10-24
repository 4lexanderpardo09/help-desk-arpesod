<?php
// Requerir la librería para leer Excel
require dirname(__FILE__) . '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Incluir los modelos necesarios
require_once('../config/conexion.php');
require_once('../models/Flujo.php');
require_once('../models/Subcategoria.php');

$flujo_model = new Flujo();
$subcategoria_model = new Subcategoria();

// 1. Verificar que el archivo se subió correctamente
if (!isset($_FILES['archivo_flujos']) || $_FILES['archivo_flujos']['error'] != UPLOAD_ERR_OK) {
    die("<h3 style='color:red;'>Error: No se subió el archivo o hubo un problema en la subida.</h3>");
}

$nombre_temporal = $_FILES['archivo_flujos']['tmp_name'];

try {
    echo "<h1>Iniciando Cargue Masivo de Flujos...</h1>";
    $spreadsheet = IOFactory::load($nombre_temporal);
    $sheet_name_esperado = $_POST['sheet_name'];

    if ($spreadsheet->sheetNameExists($sheet_name_esperado)) {
        $worksheet = $spreadsheet->getSheetByName($sheet_name_esperado);
        echo "<p>Leyendo la hoja: '{$sheet_name_esperado}'...</p>";
    } else {
        die("<h3 style='color:red;'>Error: El archivo Excel no contiene una hoja llamada '{$sheet_name_esperado}'.</h3>");
    }

    $rows = $worksheet->toArray();
    array_shift($rows); // Quitar la fila del encabezado

    $creados = 0;
    $omitidos = 0;

    // 2. Recorrer cada fila del Excel
    foreach ($rows as $row) {
        $flujo_nom = trim($row[0]);
        $cats_nom = trim($row[1]);

        $cats_id = $subcategoria_model->get_id_por_nombre($cats_nom);

        if (!$cats_id) {
            echo "<p style='color:orange;'>OMITIDO: El flujo '{$flujo_nom}' no se pudo crear porque la Subcategoría '{$cats_nom}' no existe en la BD.</p>";
            $omitidos++;
            continue;
        }

        // 4. Verificar si ya existe un flujo para esa subcategoría (solo puede haber uno)
        $flujo_existente = $flujo_model->get_flujo_por_subcategoria($cats_id);

        if ($flujo_existente) {
            echo "<p style='color:orange;'>OMITIDO: Ya existe un flujo para la subcategoría '{$cats_nom}'.</p>";
            $omitidos++;
        } else {
            // Si no existe, lo insertamos
            $flujo_model->insert_flujo($flujo_nom, $cats_id);
            echo "<p style='color:green;'>CREADO: Se ha añadido el flujo '{$flujo_nom}'.</p>";
            $creados++;
        }
    }

    echo "<h3>¡Cargue Masivo Finalizado!</h3>";
    echo "<ul>";
    echo "<li>Flujos nuevos creados: {$creados}</li>";
    echo "<li>Flujos omitidos (duplicados o con errores): {$omitidos}</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<h3 style='color:red;'>Error al leer el archivo Excel: " . $e->getMessage() . "</h3>";
}
